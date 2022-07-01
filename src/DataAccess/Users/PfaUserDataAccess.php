<?php

namespace Opf\DataAccess\Users;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Opf\Util\Util;
use Opf\Models\PFA\PfaUser;
use PDO;
use PDOException;

class PfaUserDataAccess
{
    /** @var array */
    private $config;

    /** @var PDO */
    private $dbConnection;

    /** @var \Monolog\Logger */
    private $logger;

    public function __construct()
    {
        // Instantiate our logger
        $this->logger = new Logger(PfaUserDataAccess::class);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::DEBUG));

        // Try to obtain a DSN via the Util class and complain with an Exception if there's no DSN
        $dsn = Util::buildDbDsn();
        if (!isset($dsn)) {
            throw new Exception("Can't obtain DSN to connect to DB");
        }

        $this->config = Util::getConfigFile();
        if (isset($this->config) && !empty($this->config)) {
            if (isset($this->config['db']) && !empty($this->config['db'])) {
                if (
                    isset($this->config['db']['user'])
                    && !empty($this->config['db']['user'])
                    && isset($this->config['db']['password'])
                    && !empty($this->config['db']['password'])
                ) {
                    $dbUsername = $this->config['db']['user'];
                    $dbPassword = $this->config['db']['password'];
                } else {
                    throw new Exception("No DB username and/or password provided to connect to DB");
                }
            }
        }

        // Create the DB connection with PDO
        $this->dbConnection = new PDO($dsn, $dbUsername, $dbPassword);

        // Tell PDO explicitly to throw exceptions on errors, so as to have more info when debugging DB operations
        $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll(): ?array
    {
        if (isset($this->dbConnection)) {
            // TODO: should we use a variable for the table name?
            // PFA users are 'mailbox' entries (see also https://web.audriga.com/mantis/view.php?id=5806#c28866):
            // - with a corresponding 'alias' entry
            // - with the 'alias.address' value being part of the 'alias.goto' value
            $selectStatement = $this->dbConnection->query("SELECT mailbox.* FROM mailbox INNER JOIN alias 
                WHERE mailbox.username = alias.address
                  AND alias.goto LIKE CONCAT('%', alias.address, '%')");
            if ($selectStatement) {
                $pfaUsers = [];
                $pfaUsersRaw = $selectStatement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($pfaUsersRaw as $user) {
                    $pfaUser = new PfaUser();
                    $pfaUser->mapFromArray($user);
                    $pfaUsers[] = $pfaUser;
                }
                return $pfaUsers;
            }

            $this->logger->error("Couldn't read all users from PFA. SELECT query to DB failed");
        }

        $this->logger->error("Couldn't connect to DB while attempting to read all users from PFA");
        return null;
    }

    // TODO: In the case of PFA, it maybe makes sense to rename this to something like getOneByUsername,
    // since username is the distinguishing property between users (mailboxes) in PFA and not ID
    public function getOneById($id): ?PfaUser
    {
        if (isset($id) && !empty($id)) {
            if (isset($this->dbConnection)) {
                try {
                    // TODO: should we use a variable for the table name?
                    $selectOnePreparedStatement = $this->dbConnection->prepare(
                        "SELECT mailbox.* FROM mailbox INNER JOIN alias
                            WHERE mailbox.username = alias.address
                              AND alias.goto LIKE CONCAT('%', alias.address, '%')
                              AND mailbox.username = ?"
                    );

                    $selectRes = $selectOnePreparedStatement->execute([$id]);

                    if ($selectRes) {
                        $pfaUsersRaw = $selectOnePreparedStatement->fetchAll(PDO::FETCH_ASSOC);
                        if ($pfaUsersRaw) {
                            $pfaUser = new PfaUser();
                            $pfaUser->mapFromArray($pfaUsersRaw[0]);
                            return $pfaUser;
                        } else {
                            return null;
                        }
                    } else {
                        return null;
                    }
                } catch (PDOException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        $this->logger->error(
            "Argument provided to getOneById in class " . PfaUserDataAccess::class . " is not set or empty"
        );
        return null;
    }

    public function create(PfaUser $userToCreate): ?PfaUser
    {
        $dateNow = date('Y-m-d H:i:s');
        $date2000 = '2000-01-01 00:00:00';

        if (isset($this->dbConnection)) {
            try {
                // We want to commit both insert (in mailbox and in alias) in one single commit,
                // and we want to abort both if something fails
                $this->dbConnection->beginTransaction();

                // TODO: should we use a variable for the table name?
                $insertStatement = $this->dbConnection->prepare(
                    "INSERT INTO mailbox
                    (username, password, name, maildir, quota, local_part, domain, created, modified, active, phone,
                     email_other, token, token_validity, password_expiry)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                // When performing an INSERT into the mailbox table, maildir, domain and local_part
                // as columns don't have default values.
                // That's why here in the prepared statement we currently just give them the value of the empty string,
                // so as to avoid the issue of MySQL complaining
                // that nothing is provided for them when they have no default value
                $insertRes1 = $insertStatement->execute([
                    $userToCreate->getUserName(),
                    $userToCreate->getPassword() !== null ? $userToCreate->getPassword() : '',
                    $userToCreate->getName() !== null ? $userToCreate->getName() : '',
                    $userToCreate->getMaildir(),
                    $userToCreate->getQuota() !== null ? $userToCreate->getQuota() : 0,
                    $userToCreate->getLocalPart(),
                    $userToCreate->getDomain(),
                    $dateNow,
                    $dateNow,
                    $userToCreate->getActive(),
                    $userToCreate->getPhone() !== null ? $userToCreate->getPhone() : '',
                    $userToCreate->getEmailOther() !== null ? $userToCreate->getEmailOther() : '',
                    $userToCreate->getToken() !== null ? $userToCreate->getToken() : '',
                    $userToCreate->getTokenValidity() !== null ? $userToCreate->getTokenValidity() : $date2000,
                    $userToCreate->getPasswordExpiry() !== null ? $userToCreate->getPasswordExpiry() : $date2000
                ]);

                $insertStatement = $this->dbConnection->prepare(
                    "INSERT INTO alias
                    (address, goto, domain, created, modified, active)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                // When performing an INSERT into the mailbox table, maildir, domain and local_part
                // as columns don't have default values.
                // That's why here in the prepared statement we currently just give them the value of the empty string,
                // so as to avoid the issue of MySQL complaining
                // that nothing is provided for them when they have no default value
                $insertRes2 = $insertStatement->execute([
                    $userToCreate->getUserName(),
                    $userToCreate->getUserName(),
                    $userToCreate->getDomain(),
                    $dateNow,
                    $dateNow,
                    $userToCreate->getActive()
                ]);

                // In case the write was successful, return the user that was just written
                if ($insertRes1 && $insertRes2) {
                    $this->dbConnection->commit();
                    $this->logger->info("Created user " . $userToCreate->getUserName());
                    return $this->getOneById($userToCreate->getUserName());
                    //return $userToCreate;
                } else {
                    // Otherwise, rollback and just return null
                    $this->dbConnection->rollBack();
                    return null;
                }
            } catch (PDOException $e) {
                $this->dbConnection->rollBack();
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("DB connection not available");
        }
        $this->logger->error("Error creating user");
        return null;
    }

    public function update(string $username, PfaUser $userToUpdate): ?PfaUser
    {
        $dateNow = date('Y-m-d H:i:s');

        if (isset($this->dbConnection)) {
            try {
                $query = "";
                $values = array();
                if ($userToUpdate->getPassword() !== null) {
                    $query = $query . "password = ?, ";
                    $values[] = $userToUpdate->getPassword();
                }
                if ($userToUpdate->getName() !== null) {
                    $query = $query . "name = ?, ";
                    $values[] = $userToUpdate->getName();
                }
                if ($userToUpdate->getQuota() !== null) {
                    $query = $query . "quota = ?, ";
                    $values[] = $userToUpdate->getQuota();
                }
                if ($userToUpdate->getActive() !== null) {
                    $query = $query . "active = ?, ";
                    $values[] = $userToUpdate->getActive();
                }
                if ($userToUpdate->getEmailOther() !== null) {
                    $query = $query . "email_other = ?, ";
                    $values[] = $userToUpdate->getEmailOther();
                }

                if (empty($query)) {
                    $this->logger->error("No user properties to update");
                    return null;
                }

                $query = $query . "modified = ? ";
                $values[] = $dateNow;
                $values[] = $username;

                // Since in PFA the username column in the mailbox table is the primary and is unique,
                // we use username in this case as an id that serves the purpose of a unique identifier
                // TODO: should we use a variable for the table name?
                $updateStatement = $this->dbConnection->prepare(
                    "UPDATE mailbox SET " . $query . " WHERE username = ?"
                );

                $updateRes = $updateStatement->execute($values);

                // In case the update was successful, return the user that was just updated
                if ($updateRes) {
                    $this->logger->info("Updated user " . $username);
                    return $this->getOneById($username);
                } else {
                    $this->logger->error("Error updating user " . $username);
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Error updating user " . $username . " - DB connection unavailable");
        }
        $this->logger->error("Error updating user " . $username);
        return null;
    }

    public function delete($username): bool
    {
        if (isset($this->dbConnection)) {
            try {
                // We want to commit both delete (in mailbox and in alias) in one single commit,
                // and we want to abort both if something fails
                $this->dbConnection->beginTransaction();

                // TODO: should we use a variable for the table name?
                $deleteStatement = $this->dbConnection->prepare(
                    "DELETE FROM mailbox WHERE username = ?"
                );
                $deleteRes1 = $deleteStatement->execute([$username]);

                // TODO: should we use a variable for the table name?
                $deleteStatement = $this->dbConnection->prepare(
                    "DELETE FROM alias WHERE address = ?"
                );
                $deleteRes2 = $deleteStatement->execute([$username]);

                // In case the delete was successful, return true
                if ($deleteRes1 && $deleteRes2) {
                    $this->dbConnection->commit();
                    $this->logger->info("Deleted user " . $username);
                    return true;
                    //return $userToCreate;
                } else {
                    // Otherwise, rollback and just return false
                    $this->dbConnection->rollBack();
                    return false;
                }
            } catch (PDOException $e) {
                $this->dbConnection->rollBack();
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Error deleting user " . $username . " - DB connection unavailable");
        }
        $this->logger->error("Error deleting user " . $username);
        return false;
    }
}

<?php

namespace Opf\DataAccess\Users;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Opf\Models\Mock\MockUser;
use Opf\Util\Util;
use PDO;
use PDOException;

class MockUserDataAccess
{
    /** @var PDO */
    private $dbConnection;

    /** @var \Monolog\Logger */
    private $logger;

    public function __construct()
    {
        // Instantiate our logger
        $this->logger = new Logger(MockUserDataAccess::class);
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::DEBUG));

        // Try to obtain a DSN via the Util class and complain with an Exception if there's no DSN
        $dsn = Util::buildDbDsn();
        if (!isset($dsn)) {
            throw new Exception("Can't obtain DSN to connect to DB");
        }

        // Create the DB connection with PDO (no need to pass username or password for mock DB)
        $this->dbConnection = new PDO($dsn, null, null);

        // Tell PDO explicitly to throw exceptions on errors, so as to have more info when debugging DB operations
        $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAll(): ?array
    {
        if (isset($this->dbConnection)) {
            $selectStatement = $this->dbConnection->query("SELECT * from users");
            if ($selectStatement) {
                $mockUsers = [];
                $mockUsersRaw = $selectStatement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($mockUsersRaw as $user) {
                    $mockUser = new MockUser();
                    $mockUser->mapFromArray($user);
                    $mockUsers[] = $mockUser;
                }
                return $mockUsers;
            }

            $this->logger->error("Couldn't read all users from mock DB. SELECT query to DB failed");
            return null;
        }
    }

    public function getOneById($id): ?MockUser
    {
        if (isset($id) && !empty($id)) {
            if (isset($this->dbConnection)) {
                try {
                    $selectOnePreparedStatement = $this->dbConnection->prepare(
                        "SELECT * FROM users WHERE id = ?"
                    );

                    $selectRes = $selectOnePreparedStatement->execute([$id]);

                    if ($selectRes) {
                        $mockUsersRaw = $selectOnePreparedStatement->fetchAll(PDO::FETCH_ASSOC);
                        if ($mockUsersRaw) {
                            $mockUser = new MockUser();
                            $mockUser->mapFromArray($mockUsersRaw[0]);
                            return $mockUser;
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
            "Argument provided to getOneById in class " . MockUserDataAccess::class . " is not set or empty"
        );
        return null;
    }

    public function create(MockUser $userToCreate): ?MockUser
    {
        $dateNow = date('Y-m-d H:i:s');

        if (isset($this->dbConnection)) {
            try {
                $insertStatement = $this->dbConnection->prepare(
                    "INSERT INTO users
                    (id, userName, active, externalId, profileUrl, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                $userToCreate->setId(Util::genUuid());

                $insertRes = $insertStatement->execute([
                    $userToCreate->getId(),
                    $userToCreate->getUserName(),
                    $userToCreate->getActive(),
                    $userToCreate->getExternalId(),
                    $userToCreate->getProfileUrl(),
                    $dateNow,
                    $dateNow
                ]);

                if ($insertRes) {
                    $this->logger->info("Created user " . $userToCreate->getUserName());
                    return $userToCreate;
                } else {
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("DB connection not available");
        }
        $this->logger->error("Error creating user");
        return null;
    }

    public function update(string $id, MockUser $userToUpdate): ?MockUser
    {
        $dateNow = date('Y-m-d H:i:s');

        if (isset($this->dbConnection)) {
            try {
                $query = "";
                $values = array();

                if ($userToUpdate->getUserName() !== null) {
                    $query = $query . "userName = ?, ";
                    $values[] = $userToUpdate->getUserName();
                }

                if ($userToUpdate->getActive() !== null) {
                    $query = $query . "active = ?, ";
                    $values[] = $userToUpdate->getActive();
                }

                if ($userToUpdate->getProfileUrl() !== null) {
                    $query = $query . "profileUrl = ?, ";
                    $values[] = $userToUpdate->getProfileUrl();
                }

                if ($userToUpdate->getExternalId() !== null) {
                    $query = $query . "externalId = ?, ";
                    $values[] = $userToUpdate->getExternalId();
                }

                if (empty($query)) {
                    $this->logger->error("No user properties to update");
                    return null;
                }

                $query = $query . "updated_at = ? ";
                $values[] = $dateNow;
                $values[] = $id;

                $updateStatement = $this->dbConnection->prepare(
                    "UPDATE users SET " . $query . " WHERE id = ?"
                );

                $updateRes = $updateStatement->execute($values);

                if ($updateRes) {
                    $this->logger->info("Updated user " . $id);
                    return $this->getOneById($id);
                } else {
                    $this->logger->error("Error updating user " . $id);
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Error updating user " . $id . " - DB connection unavailable");
        }
        $this->logger->error("Error updating user " . $id);
        return null;
    }

    public function delete($id): bool
    {
        if (isset($this->dbConnection)) {
            try {
                $deleteStatement = $this->dbConnection->prepare(
                    "DELETE FROM users WHERE id = ?"
                );
                $deleteRes = $deleteStatement->execute([$id]);

                // In case the delete was successful, return true
                if ($deleteRes) {
                    $this->logger->info("Deleted user " . $id);
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Error deleting user " . $id . " - DB connection unavailable");
        }
        $this->logger->error("Error deleting user " . $id);
        return false;
    }
}

<?php

namespace Opf\DataAccess\Groups;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Opf\Models\Mock\MockGroup;
use Opf\Util\Util;
use PDO;
use PDOException;

class MockGroupDataAccess
{
    /** @var PDO */
    private $dbConnection;

    /** @var \Monolog\Logger */
    private $logger;

    public function __construct()
    {
        // Instantiate our logger
        $this->logger = new Logger(MockGroupDataAccess::class);
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
            $selectStatement = $this->dbConnection->query("SELECT * from groups");
            if ($selectStatement) {
                $mockGroups = [];
                $mockGroupsRaw = $selectStatement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($mockGroupsRaw as $group) {
                    $mockGroup = new MockGroup();
                    $mockGroup->mapFromArray($group);
                    $mockGroups[] = $mockGroup;
                }
                return $mockGroups;
            }

            $this->logger->error("Couldn't read all groups from mock DB. SELECT query to DB failed");
            return null;
        }
    }

    public function getOneById($id): ?MockGroup
    {
        if (isset($id) && !empty($id)) {
            if (isset($this->dbConnection)) {
                try {
                    $selectOnePreparedStatement = $this->dbConnection->prepare(
                        "SELECT * FROM groups WHERE id = ?"
                    );

                    $selectRes = $selectOnePreparedStatement->execute([$id]);

                    if ($selectRes) {
                        $mockGroupsRaw = $selectOnePreparedStatement->fetchAll(PDO::FETCH_ASSOC);
                        if ($mockGroupsRaw) {
                            $mockGroup = new MockGroup();
                            $mockGroup->mapFromArray($mockGroupsRaw[0]);
                            return $mockGroup;
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
            "Argument provided to getOneById in class " . MockGroupDataAccess::class . " is not set or empty"
        );
        return null;
    }

    public function create(MockGroup $groupToCreate): ?MockGroup
    {
        $dateNow = date('Y-m-d H:i:s');

        if (isset($this->dbConnection)) {
            try {
                $insertStatement = $this->dbConnection->prepare(
                    "INSERT INTO groups
                    (id, displayName, members, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?)"
                );

                $groupToCreate->setId(Util::genUuid());

                $insertRes = $insertStatement->execute([
                    $groupToCreate->getId(),
                    $groupToCreate->getDisplayName(),
                    $groupToCreate->getMembers() !== null && !empty($groupToCreate->getMembers())
                        ? $groupToCreate->getMembers() : "",
                    $dateNow,
                    $dateNow
                ]);

                if ($insertRes) {
                    $this->logger->info("Created group " . $groupToCreate->getDisplayName());
                    return $groupToCreate;
                } else {
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("DB connection not available");
        }
        $this->logger->error("Error creating group");
        return null;
    }

    public function update(string $id, MockGroup $groupToUpdate): ?MockGroup
    {
        $dateNow = date('Y-m-d H:i:s');

        if (isset($this->dbConnection)) {
            try {
                $query = "";
                $values = array();

                if ($groupToUpdate->getDisplayName() !== null) {
                    $query = $query . "displayName = ?, ";
                    $values[] = $groupToUpdate->getDisplayName();
                }

                if ($groupToUpdate->getMembers() !== null) {
                    $query = $query . "members = ?, ";

                    // We need to transform the string array of user IDs to a single string
                    $values[] = implode(",", $groupToUpdate->getMembers());
                }

                if (empty($query)) {
                    $this->logger->error("No group properties to update");
                    return null;
                }

                $query = $query . "updated_at = ? ";
                $values[] = $dateNow;
                $values[] = $id;

                $updateStatement = $this->dbConnection->prepare(
                    "UPDATE groups SET " . $query . " WHERE id = ?"
                );

                $updateRes = $updateStatement->execute($values);

                if ($updateRes) {
                    $this->logger->info("Updated group " . $id);
                    return $this->getOneById($id);
                } else {
                    $this->logger->error("Error updating group " . $id);
                    return null;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Error updating group " . $id . " - DB connection unavailable");
        }
        $this->logger->error("Error updating group " . $id);
        return null;
    }

    public function delete($id): bool
    {
        if (isset($this->dbConnection)) {
            try {
                $deleteStatement = $this->dbConnection->prepare(
                    "DELETE FROM groups WHERE id = ?"
                );
                $deleteRes = $deleteStatement->execute([$id]);

                // In case the delete was successful, return true
                if ($deleteRes) {
                    $this->logger->info("Deleted group " . $id);
                    return true;
                } else {
                    return false;
                }
            } catch (PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->logger->error("Error deleting group " . $id . " - DB connection unavailable");
        }
        $this->logger->error("Error deleting group " . $id);
        return false;
    }
}

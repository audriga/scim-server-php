<?php

namespace Opf\Repositories\Users;

use Monolog\Logger;
use Opf\Models\SCIM\Standard\Users\CoreUser;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Repositories\Repository;
use Opf\Util\Filters\FilterUtil;
use Psr\Container\ContainerInterface;

class MockUsersRepository extends Repository
{
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dataAccess = $this->container->get('UsersDataAccess');
        $this->adapter = $this->container->get('UsersAdapter');
        $this->logger = $this->container->get(Logger::class);
    }

    public function getAll(
        $filter = '',
        $startIndex = 0,
        $count = 0,
        $attributes = [],
        $excludedAttributes = []
    ): array {
        // Read all mock users from the database
        $mockUsers = $this->dataAccess->getAll();
        $scimUsers = [];

        foreach ($mockUsers as $mockUser) {
            $scimUser = $this->adapter->getCoreUser($mockUser);
            $scimUsers[] = $scimUser;
        }

        if (isset($filter) && !empty($filter)) {
            $scimUsersToFilter = [];
            foreach ($scimUsers as $scimUser) {
                $scimUsersToFilter[] = $scimUser->toSCIM(false);
            }

            $filteredScimData = FilterUtil::performFiltering($filter, $scimUsersToFilter);

            $scimUsers = [];
            foreach ($filteredScimData as $filteredScimUser) {
                $scimUser = new CoreUser();
                $scimUser->fromSCIM($filteredScimUser);
                $scimUsers[] = $scimUser;
            }

            return $scimUsers;
        }

        return $scimUsers;
    }

    public function getOneById(
        string $id,
        $filter = '',
        $startIndex = 0,
        $count = 0,
        $attributes = [],
        $excludedAttributes = []
    ): ?CoreUser {
        $mockUser = $this->dataAccess->getOneById($id);
        $scimUser = $this->adapter->getCoreUser($mockUser);

        if (isset($filter) && !empty($filter)) {
            // Pass the single user as an array of an array, representing the user
            $scimUsersToFilter = array($scimUser->toSCIM(false));
            $filteredScimData = FilterUtil::performFiltering($filter, $scimUsersToFilter);

            if (!empty($filteredScimData)) {
                $scimUser = new CoreUser();
                $scimUser->fromSCIM($filteredScimData[0]);
                return $scimUser;
            }
        }

        return $scimUser;
    }

    public function create($object): ?CoreUser
    {
        $scimUserToCreate = new CoreUser();
        $scimUserToCreate->fromSCIM($object);

        $mockUserToCreate = $this->adapter->getMockUser($scimUserToCreate);

        $mockUserCreated = $this->dataAccess->create($mockUserToCreate);

        if (isset($mockUserCreated)) {
            return $this->adapter->getCoreUser($mockUserCreated);
        }
        return null;
    }

    public function update(string $id, $object): ?CoreUser
    {
        $scimUserToUpdate = new CoreUser();
        $scimUserToUpdate->fromSCIM($object);

        $mockUserToUpdate = $this->adapter->getMockUser($scimUserToUpdate);

        $mockUserUpdated = $this->dataAccess->update($id, $mockUserToUpdate);

        if (isset($mockUserUpdated)) {
            return $this->adapter->getCoreUser($mockUserUpdated);
        }
        return null;
    }

    public function delete(string $id): bool
    {
        return $this->dataAccess->delete($id);
    }
}

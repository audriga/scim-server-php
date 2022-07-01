<?php

namespace Opf\Repositories\Users;

use Opf\Models\SCIM\Custom\Users\ProvisioningUser;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Models\SCIM\Standard\MultiValuedAttribute;
use Opf\Repositories\Repository;
use Opf\Util\Util;
use Psr\Container\ContainerInterface;
use Monolog\Logger;

class PfaUsersRepository extends Repository
{
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dataAccess = $this->container->get('UsersDataAccess');
        $this->adapter = $this->container->get('UsersAdapter');
        $this->logger = $this->container->get(Logger::class);
    }

    public function getAll(): array
    {
        $pfaUsers = $this->dataAccess->getAll();
        $scimUsers = [];

        foreach ($pfaUsers as $pfaUser) {
            $scimUser = $this->adapter->getProvisioningUser($pfaUser);
            $scimUsers[] = $scimUser;
        }

        return $scimUsers;
    }

    public function getOneById(string $id): ?ProvisioningUser
    {
        $pfaUser = $this->dataAccess->getOneById($id);
        return $this->adapter->getProvisioningUser($pfaUser);
    }

    public function create($object): ?ProvisioningUser
    {
        $scimUserToCreate = new ProvisioningUser();
        $scimUserToCreate->fromSCIM($object);

        $pfaUserToCreate = $this->adapter->getPfaUser($scimUserToCreate);

        $pfaUserCreated = $this->dataAccess->create($pfaUserToCreate);

        if (isset($pfaUserCreated)) {
            return $this->adapter->getProvisioningUser($pfaUserCreated);
        }
        return null;
    }

    public function update(string $id, $object): ?ProvisioningUser
    {
        $scimUserToUpdate = new ProvisioningUser();
        $scimUserToUpdate->fromSCIM($object);

        $pfaUserToUpdate = $this->adapter->getPfaUser($scimUserToUpdate);

        $pfaUserUpdated = $this->dataAccess->update($id, $pfaUserToUpdate);

        if (isset($pfaUserUpdated)) {
            return $this->adapter->getProvisioningUser($pfaUserUpdated);
        }
        return null;
    }

    public function delete(string $id): bool
    {
        return $this->dataAccess->delete($id);
    }
}

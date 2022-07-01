<?php

namespace Opf\Repositories\Users;

use Opf\Models\SCIM\Standard\Users\CoreUser;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Repositories\Repository;
use Psr\Container\ContainerInterface;

class MockUsersRepository extends Repository
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dataAccess = $this->container->get('UsersDataAccess');
        $this->adapter = $this->container->get('UsersAdapter');
    }
    public function getAll(): array
    {
        // Read all mock users from the database
        $mockUsers = $this->dataAccess::all();
        $scimUsers = [];

        // Transform each mock user to a SCIM user via the injected adapter
        foreach ($mockUsers as $mockUser) {
            // TODO: Possibly refactor the transformation logic between SCIM users and other users
            // in a separate method or class, since it seems to be rather repetitive
            $this->adapter->setUser($mockUser);

            $scimUser = new CoreUser();
            $scimUser->setId($this->adapter->getId());
            $scimUser->setUserName($this->adapter->getUserName());

            $scimUserMeta = new Meta();
            $scimUserMeta->setCreated($this->adapter->getCreatedAt());

            $scimUser->setMeta($scimUserMeta);
            $scimUser->setActive($this->adapter->getActive());
            $scimUser->setExternalId($this->adapter->getExternalId());
            $scimUser->setProfileUrl($this->adapter->getProfileUrl());

            $scimUsers[] = $scimUser;
        }

        return $scimUsers;
    }

    public function getOneByUserName(string $userName): ?CoreUser
    {
        if (isset($userName) && !empty($userName)) {
            // Try to find the first user from the database with the supplied username
            $mockUser = $this->dataAccess::where('userName', $userName)->first();

            // If such a user exists, map it to a SCIM user and return the SCIM user
            if (isset($mockUser) && !empty($mockUser)) {
                $this->adapter->setUser($mockUser);

                $scimUser = new CoreUser();
                $scimUser->setId($this->adapter->getId());
                $scimUser->setUserName($this->adapter->getUserName());

                $scimUserMeta = new Meta();
                $scimUserMeta->setCreated($this->adapter->getCreatedAt());

                $scimUser->setMeta($scimUserMeta);
                $scimUser->setActive($this->adapter->getActive());
                $scimUser->setExternalId($this->adapter->getExternalId());
                $scimUser->setProfileUrl($this->adapter->getProfileUrl());

                return $scimUser;
            } else {
                return null;
            }
        }
    }

    public function getOneById(string $id): ?CoreUser
    {
        if (isset($id) && !empty($id)) {
            // Try to find a user from the database with the supplied ID
            $mockUser = $this->dataAccess::find($id);

            // If there's such a user, transform it to a SCIM user and return the SCIM user
            if (isset($mockUser) && !empty($mockUser)) {
                $this->adapter->setUser($mockUser);

                $scimUser = new CoreUser();
                $scimUser->setId($this->adapter->getId());
                $scimUser->setUserName($this->adapter->getUserName());

                $scimUserMeta = new Meta();
                $scimUserMeta->setCreated($this->adapter->getCreatedAt());

                $scimUser->setMeta($scimUserMeta);
                $scimUser->setActive($this->adapter->getActive());
                $scimUser->setExternalId($this->adapter->getExternalId());
                $scimUser->setProfileUrl($this->adapter->getProfileUrl());

                return $scimUser;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function create($object): ?CoreUser
    {
        if (isset($object) && !empty($object)) {
            // Transform the incoming JSON user object to a SCIM object
            // Then transform the SCIM object to a mock user that can be stored in the database
            $scimUser = new CoreUser();
            $scimUser->fromSCIM($object);

            // $this->dataAccess represents an instance of the MockUser ORM model
            // that we use for user storage to and retrieval from SQLite
            $this->adapter->setUser($this->dataAccess);

            $this->adapter->setId($scimUser->getId());
            $this->adapter->setUserName($scimUser->getUserName());
            $this->adapter->setCreatedAt($scimUser->getMeta()->getCreated());
            $this->adapter->setActive($scimUser->getActive());
            $this->adapter->setExternalId($scimUser->getExternalId());
            $this->adapter->setProfileUrl($scimUser->getProfileUrl());

            // Obtain the transformed mock user from the adapter and try to save it to the database
            $this->dataAccess = $this->adapter->getUser();
            if ($this->dataAccess->save()) {
                return $scimUser;
            } else {
                return null;
            }
        }
    }

    public function update(string $id, $object): ?CoreUser
    {
        if (isset($id) && !empty($id)) {
            // Try to find the user with the supplied ID
            $mockUser = $this->dataAccess::find($id);
            if (isset($mockUser) && !empty($mockUser)) {
                // Transform the received JSON user object to a SCIM object
                $scimUser = new CoreUser();
                $scimUser->fromSCIM($object);

                // Set the adapter's internal user object to the found user from the database
                $this->adapter->setUser($mockUser);

                // Set the SCIM user's ID to be the same as the ID of the found user from the database
                // Otherwise, we might lose the ID if a new one is supplied in the request
                $scimUser->setId($this->adapter->getId());

                // Transform the SCIM object to a mock user via the adapter and replace
                // any properties of the mock user with the new properties incoming via the SCIM object
                $this->adapter->setUserName($scimUser->getUserName());
                $this->adapter->setCreatedAt($scimUser->getMeta()->getCreated());
                $this->adapter->setActive($scimUser->getActive());
                $this->adapter->setExternalId($scimUser->getExternalId());
                $this->adapter->setProfileUrl($scimUser->getProfileUrl());

                // Obtain the updated mock user via the adapter and try to save it to the database
                $mockUser = $this->adapter->getUser();
                if ($mockUser->save()) {
                    return $scimUser;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function delete(string $id): bool
    {
        if (isset($id) && !empty($id)) {
            // Try to find the user to be deleted
            $mockUser = $this->dataAccess::find($id);
            if (!isset($mockUser) || empty($mockUser)) {
                return false;
            }
            $mockUser->delete();
            return true;
        } else {
            return false;
        }
    }
}

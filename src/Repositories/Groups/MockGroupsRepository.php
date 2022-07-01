<?php

namespace Opf\Repositories\Groups;

use Opf\Models\SCIM\Standard\Groups\CoreGroup;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Repositories\Repository;
use Psr\Container\ContainerInterface;

class MockGroupsRepository extends Repository
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dataAccess = $this->container->get('GroupsDataAccess');
        $this->adapter = $this->container->get('GroupsAdapter');
    }

    public function getAll(): array
    {
        // Read all mock groups from the database
        $mockGroups = $this->dataAccess::all();
        $scimGroups = [];

        // Transform each mock group to a SCIM group via the injected adapter
        foreach ($mockGroups as $mockGroup) {
            $this->adapter->setGroup($mockGroup);

            $scimGroup = new CoreGroup();
            $scimGroup->setId($this->adapter->getId());
            $scimGroup->setDisplayName($this->adapter->getDisplayName());
            $scimGroup->setMembers($this->adapter->getMembers());

            $scimGroupMeta = new Meta();
            $scimGroupMeta->setCreated($this->adapter->getCreatedAt());

            $scimGroup->setMeta($scimGroupMeta);

            $scimGroups[] = $scimGroup;
        }

        return $scimGroups;
    }

    public function getOneById(string $id): ?CoreGroup
    {
        if (isset($id) && !empty($id)) {
            $mockGroup = $this->dataAccess::find($id);

            if (isset($mockGroup) && !empty($mockGroup)) {
                $this->adapter->setGroup($mockGroup);

                $scimGroup = new CoreGroup();
                $scimGroup->setId($this->adapter->getId());
                $scimGroup->setDisplayName($this->adapter->getDisplayName());
                $scimGroup->setMembers($this->adapter->getMembers());

                $scimGroupMeta = new Meta();
                $scimGroupMeta->setCreated($this->adapter->getCreatedAt());

                $scimGroup->setMeta($scimGroupMeta);

                return $scimGroup;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function create($object): ?CoreGroup
    {
        if (isset($object) && !empty($object)) {
            $scimGroup = new CoreGroup();
            $scimGroup->fromSCIM($object);

            $this->adapter->setGroup($this->dataAccess);

            $this->adapter->setId($scimGroup->getId());
            $this->adapter->setDisplayName($scimGroup->getDisplayName());
            $this->adapter->setMembers($scimGroup->getMembers());
            $this->adapter->setCreatedAt($scimGroup->getMeta()->getCreated());

            $this->dataAccess = $this->adapter->getGroup();
            if ($this->dataAccess->save()) {
                return $scimGroup;
            } else {
                return null;
            }
        }
    }

    public function update(string $id, $object): ?CoreGroup
    {
        if (isset($id) && !empty($id)) {
            $mockGroup = $this->dataAccess::find($id);
            if (isset($mockGroup) && !empty($mockGroup)) {
                $scimGroup = new CoreGroup();
                $scimGroup->fromSCIM($object);

                $this->adapter->setGroup($mockGroup);

                $scimGroup->setId($this->adapter->getId());

                $this->adapter->setDisplayName($scimGroup->getDisplayName());
                $this->adapter->setMembers($scimGroup->getMembers());
                $this->adapter->setCreatedAt($scimGroup->getMeta()->getCreated());

                $mockGroup = $this->adapter->getGroup();
                if ($mockGroup->save()) {
                    return $scimGroup;
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
            $mockGroup = $this->dataAccess::find($id);
            if (!isset($mockGroup) || empty($mockGroup)) {
                return false;
            }
            $mockGroup->delete();
            return true;
        } else {
            return false;
        }
    }
}

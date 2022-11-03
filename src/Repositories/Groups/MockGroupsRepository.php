<?php

namespace Opf\Repositories\Groups;

use Monolog\Logger;
use Opf\Models\SCIM\Standard\Groups\CoreGroup;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Repositories\Repository;
use Opf\Util\Filters\FilterUtil;
use Psr\Container\ContainerInterface;

class MockGroupsRepository extends Repository
{
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dataAccess = $this->container->get('GroupsDataAccess');
        $this->adapter = $this->container->get('GroupsAdapter');
        $this->logger = $this->container->get(Logger::class);
    }

    public function getAll(
        $filter = '',
        $startIndex = 0,
        $count = 0,
        $attributes = [],
        $excludedAttributes = []
    ): array {
        // Read all mock groups from the database
        $mockGroups = $this->dataAccess->getAll();
        $scimGroups = [];

        foreach ($mockGroups as $mockGroup) {
            $scimGroup = $this->adapter->getCoreGroup($mockGroup);
            $scimGroups[] = $scimGroup;
        }

        if (isset($filter) && !empty($filter)) {
            $scimGroupsToFilter = [];
            foreach ($scimGroups as $scimGroup) {
                $scimGroupsToFilter[] = $scimGroup->toSCIM(false);
            }

            $filteredScimData = FilterUtil::performFiltering($filter, $scimGroupsToFilter);

            $scimGroups = [];
            foreach ($filteredScimData as $filteredScimGroup) {
                $scimGroup = new CoreGroup();
                $scimGroup->fromSCIM($filteredScimGroup);
                $scimGroups[] = $scimGroup;
            }

            return $scimGroups;
        }

        return $scimGroups;
    }

    public function getOneById(
        string $id,
        $filter = '',
        $startIndex = 0,
        $count = 0,
        $attributes = [],
        $excludedAttributes = []
    ): ?CoreGroup {
        $mockGroup = $this->dataAccess->getOneById($id);
        return $this->adapter->getCoreGroup($mockGroup);
    }

    public function create($object): ?CoreGroup
    {
        $scimGroupToCreate = new CoreGroup();
        $scimGroupToCreate->fromSCIM($object);

        $mockGroupToCreate = $this->adapter->getMockGroup($scimGroupToCreate);

        $mockGroupCreated = $this->dataAccess->create($mockGroupToCreate);

        if (isset($mockGroupCreated)) {
            return $this->adapter->getCoreGroup($mockGroupCreated);
        }
        return null;
    }

    public function update(string $id, $object): ?CoreGroup
    {
        $scimGroupToUpdate = new CoreGroup();
        $scimGroupToUpdate->fromSCIM($object);

        $mockGroupToUpdate = $this->adapter->getMockGroup($scimGroupToUpdate);

        $mockGroupUpdated = $this->dataAccess->update($id, $mockGroupToUpdate);

        if (isset($mockGroupUpdated)) {
            return $this->adapter->getCoreGroup($mockGroupUpdated);
        }
        return null;
    }

    public function delete(string $id): bool
    {
        return $this->dataAccess->delete($id);
    }
}

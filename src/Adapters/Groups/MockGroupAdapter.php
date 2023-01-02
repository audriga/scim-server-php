<?php

namespace Opf\Adapters\Groups;

use Opf\Adapters\AbstractAdapter;
use Opf\Models\Mock\MockGroup;
use Opf\Models\SCIM\Standard\Groups\CoreGroup;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Models\SCIM\Standard\MultiValuedAttribute;

class MockGroupAdapter extends AbstractAdapter
{
    public function getCoreGroup(?MockGroup $mockGroup): ?CoreGroup
    {
        if (!isset($mockGroup)) {
            return null;
        }

        $coreGroup = new CoreGroup();
        $coreGroup->setId($mockGroup->getId());

        $coreGroupMeta = new Meta();
        $coreGroupMeta->setResourceType("Group");
        $coreGroupMeta->setCreated($mockGroup->getCreatedAt());
        $coreGroupMeta->setLastModified($mockGroup->getUpdatedAt());
        $coreGroup->setMeta($coreGroupMeta);

        $coreGroup->setDisplayName($mockGroup->getDisplayName());

        if ($mockGroup->getMembers() !== null && !empty($mockGroup->getMembers())) {
            $coreGroupMembers = [];
            foreach ($mockGroup->getMembers() as $mockGroupMember) {
                $coreGroupMember = new MultiValuedAttribute();
                $coreGroupMember->setValue($mockGroupMember->getValue());
                $coreGroupMember->setDisplay($mockGroupMember->getDisplay());
                $coreGroupMember->setRef($mockGroupMember->getRef());
                $coreGroupMembers[] = $coreGroupMember;
            }

            $coreGroup->setMembers($coreGroupMembers);
        }

        return $coreGroup;
    }

    public function getMockGroup(?CoreGroup $coreGroup): ?MockGroup
    {
        if (!isset($coreGroup)) {
            return null;
        }

        $mockGroup = new MockGroup();
        $mockGroup->setId($coreGroup->getId());

        if ($coreGroup->getMeta() !== null) {
            $mockGroup->setCreatedAt($coreGroup->getMeta()->getCreated());
            $mockGroup->setUpdatedAt($coreGroup->getMeta()->getLastModified());
        }

        $mockGroup->setDisplayName($coreGroup->getDisplayName());

        if ($coreGroup->getMembers() !== null && !empty($coreGroup->getMembers())) {
            $mockGroupMembers = [];
            foreach ($coreGroup->getMembers() as $coreGroupMember) {
                $mockGroupMember = new MultiValuedAttribute();
                $mockGroupMember->setValue($coreGroupMember->getValue());
                $mockGroupMember->setDisplay($coreGroupMember->getDisplay());
                $mockGroupMember->setRef($coreGroupMember->getRef());
                $mockGroupMembers[] = $mockGroupMember;
            }

            $mockGroup->setMembers($mockGroupMembers);
        }
        return $mockGroup;
    }
}

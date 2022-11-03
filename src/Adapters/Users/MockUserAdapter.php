<?php

namespace Opf\Adapters\Users;

use Opf\Adapters\AbstractAdapter;
use Opf\Models\Mock\MockUser;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Models\SCIM\Standard\Users\CoreUser;

class MockUserAdapter extends AbstractAdapter
{
    public function getCoreUser(?MockUser $mockUser): ?CoreUser
    {
        if (!isset($mockUser)) {
            return null;
        }

        $coreUser = new CoreUser();
        $coreUser->setId($mockUser->getId());
        $coreUser->setExternalId($mockUser->getExternalId());

        $coreUserMeta = new Meta();
        $coreUserMeta->setResourceType("User");
        $coreUserMeta->setCreated($mockUser->getCreatedAt());
        $coreUserMeta->setLastModified($mockUser->getUpdatedAt());
        $coreUser->setMeta($coreUserMeta);

        $coreUser->setUserName($mockUser->getUserName());
        $coreUser->setActive(boolval($mockUser->getActive()));
        $coreUser->setProfileUrl($mockUser->getProfileUrl());

        return $coreUser;
    }

    public function getMockUser(?CoreUser $coreUser): ?MockUser
    {
        if (!isset($coreUser)) {
            return null;
        }

        $mockUser = new MockUser();
        $mockUser->setId($coreUser->getId());

        if ($coreUser->getMeta() !== null) {
            $mockUser->setCreatedAt($coreUser->getMeta()->getCreated());
            $mockUser->setUpdatedAt($coreUser->getMeta()->getLastModified());
        }

        $mockUser->setUserName($coreUser->getUserName());
        $mockUser->setActive(boolval($coreUser->getActive()));
        $mockUser->setExternalId($coreUser->getExternalId());
        $mockUser->setProfileUrl($coreUser->getProfileUrl());

        return $mockUser;
    }
}

<?php

namespace Opf\Adapters\Users;

use Exception;
use Opf\Adapters\AbstractAdapter;
use Opf\Models\SCIM\Custom\Users\ProvisioningUser;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Models\SCIM\Standard\MultiValuedAttribute;
use Opf\Models\PFA\PfaUser;
use Opf\Models\SCIM\Standard\Users\Name;
use Opf\Util\Util;

class PfaUserAdapter extends AbstractAdapter
{
    public function getProvisioningUser(?PfaUser $pfaUser): ?ProvisioningUser
    {
        if ($pfaUser === null) {
            return null;
        }
        $provisioningUser = new ProvisioningUser();
        $provisioningUser->setId($pfaUser->getUserName());
        $provisioningUser->setUserName($pfaUser->getUserName());

        $scimUserMeta = new Meta();
        $scimUserMeta->setResourceType("User");
        $scimUserMeta->setCreated($pfaUser->getCreated());
        $scimUserMeta->setLastModified($pfaUser->getModified());
        $provisioningUser->setMeta($scimUserMeta);

        $name = new Name();
        $name->setFormatted($pfaUser->getName());
        $provisioningUser->setName($name);
        $provisioningUser->setDisplayName($pfaUser->getName());
        $provisioningUser->setActive($pfaUser->getActive());
        $scimUserPhoneNumbers = new MultiValuedAttribute();
        $scimUserPhoneNumbers->setValue($pfaUser->getPhone());
        $provisioningUser->setPhoneNumbers(array($scimUserPhoneNumbers));

        $provisioningUser->setSizeQuota($pfaUser->getQuota());

        return $provisioningUser;
    }

    public function getPfaUser(?ProvisioningUser $provisioningUser): ?PfaUser
    {
        if ($provisioningUser === null) {
            return null;
        }
        $pfaUser = new PfaUser();

        if (filter_var($provisioningUser->getUserName(), FILTER_VALIDATE_EMAIL)) {
            $pfaUser->setUserName($provisioningUser->getUserName());
        } elseif (filter_var($provisioningUser->getId(), FILTER_VALIDATE_EMAIL)) {
            $pfaUser->setUserName($provisioningUser->getId());
        } else {
            $pfaUser->setUserName($provisioningUser->getEmails()[0]->getValue());
        }
        $pfaUser->setPassword($provisioningUser->getPassword());
        if ($provisioningUser->getName() !== null) {
            if (!empty($provisioningUser->getName()->getFormatted())) {
                $pfaUser->setName($provisioningUser->getName()->getFormatted());
            } else {
                $formattedName = "";
                if (!empty($provisioningUser->getName()->getHonorificPrefix())) {
                    $formattedName = $formattedName . $provisioningUser->getName()->getHonorificPrefix() . " ";
                }
                if (!empty($provisioningUser->getName()->getGivenName())) {
                    $formattedName = $formattedName . $provisioningUser->getName()->getGivenName() . " ";
                }
                if (!empty($provisioningUser->getName()->getFamilyName())) {
                    $formattedName = $formattedName . $provisioningUser->getName()->getFamilyName() . " ";
                }
                if (!empty($provisioningUser->getName()->getHonorificSuffix())) {
                    $formattedName = $formattedName . $provisioningUser->getName()->getHonorificSuffix();
                }
                $formattedName = trim($formattedName);
                if (!empty($formattedName)) {
                    $pfaUser->setName($formattedName);
                } else {
                    $pfaUser->setName($provisioningUser->getDisplayName());
                }
            }
        } else {
            $pfaUser->setName($provisioningUser->getDisplayName());
        }
        $pfaUser->setMaildir(Util::getDomainFromEmail($pfaUser->getUserName()) . "/"
            . Util::getLocalPartFromEmail($pfaUser->getUserName()) . "/");
        // We default PFA quota to 0 (unlimited) if not set
        $pfaUser->setQuota($provisioningUser->getSizeQuota());
        $pfaUser->setLocalPart(Util::getLocalPartFromEmail($pfaUser->getUserName()));
        $pfaUser->setDomain(Util::getDomainFromEmail($pfaUser->getUserName()));
        $pfaUser->setActive($provisioningUser->getActive());
        if (isset($provisioningUser->getPhoneNumbers()[0])) {
            $pfaUser->setPhone($provisioningUser->getPhoneNumbers()[0]->getValue());
        }

        return $pfaUser;
    }
}

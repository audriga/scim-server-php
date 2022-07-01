<?php

namespace Opf\Models\SCIM\Standard\Users;

use Opf\Util\Util;

class EnterpriseUser extends CoreUser
{
    /** @var string $employeeNumber */
    private $employeeNumber;

    /** @var string $costCenter */
    private $costCenter;

    /** @var string $organization */
    private $organization;

    /** @var string $division */
    private $division;

    /** @var string $department */
    private $department;

    /** @var \Opf\Models\SCIM\Manager $manager */
    private $manager;

    public function getEmployeeNumber()
    {
        return $this->employeeNumber;
    }

    public function setEmployeeNumber($employeeNumber)
    {
        $this->employeeNumber = $employeeNumber;
    }

    public function getCostCenter()
    {
        return $this->costCenter;
    }

    public function setCostCenter($costCenter)
    {
        $this->costCenter = $costCenter;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    public function getDivision()
    {
        return $this->division;
    }

    public function setDivision($division)
    {
        $this->division = $division;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        $data = [
            'schemas' => [Util::ENTERPRISE_USER_SCHEMA],
            'id' => $this->getId(),
            'externalId' => $this->getExternalId(),
            'meta' => null !== $this->getMeta() ? [
                'resourceType' => null !== $this->getMeta()->getResourceType()
                    ? $this->getMeta()->getResourceType() : null,
                'created' => null !== $this->getMeta()->getCreated() ? $this->getMeta()->getCreated() : null,
                'location' => $baseLocation . '/Users/' . $this->getId(),
                'version' => null !== $this->getMeta()->getVersion() ? $this->getMeta()->getVersion() : null
            ] : null,
            'userName' => $this->getUserName(),
            'name' => null !== $this->getName() ? [
                'formatted' => null !== $this->getName()->getFormatted() ? $this->getName()->getFormatted() : null,
                'familyName' => null !== $this->getName()->getFamilyName() ? $this->getName()->getFamilyName() : null,
                'givenName' => null !== $this->getName()->getGivenName() ? $this->getName()->getGivenName() : null,
                'middleName' => null !== $this->getName()->getMiddleName() ? $this->getName()->getMiddleName() : null,
                'honorificPrefix' => null !== $this->getName()->getHonorificPrefix()
                    ? $this->getName()->getHonorificPrefix()
                    : null,
                'honorificSuffix' => null !== $this->getName()->getHonorificSuffix()
                    ? $this->getName()->getHonorificSuffix()
                    : null
            ] : null,
            'displayName' => $this->getDisplayName(),
            'nickName' => $this->getNickName(),
            'profileUrl' => $this->getProfileUrl(),
            'title' => $this->getTitle(),
            'userType' => $this->getUserType(),
            'preferredLanguage' => $this->getPreferredLanguage(),
            'locale' => $this->getLocale(),
            'timezone' => $this->getTimezone(),
            'active' => $this->getActive(),
            'password' => $this->getPassword(),
            'emails' => $this->getEmails(),
            'phoneNumbers' => $this->getPhoneNumbers(),
            'ims' => $this->getIms(),
            'photos' => $this->getPhotos(),
            'addresses' => $this->getAddresses(),
            'groups' => $this->getGroups(),
            'entitlements' => $this->getEntitlements(),
            'roles' => $this->getRoles(),
            'x509Certificates' => $this->getX509Certificates(),
            'employeeNumber' => $this->getEmployeeNumber(),
            'costCenter' => $this->getCostCenter(),
            'organization' => $this->getOrganization(),
            'division' => $this->getDivision(),
            'department' => $this->getDepartment(),
            'manager' => null !== $this->getManager() ? [
                'value' => null !== $this->getManager()->getValue() ? $this->getManager()->getValue() : null,
                '\$ref' => null !== $this->getManager()->getRef() ? $this->getManager()->getRef() : null,
                'displayName' => null !== $this->getManager()->getDisplayName()
                    ? $this->getManager()->getDisplayName()
                    : null
            ] : null
        ];

        if (null !== $this->getMeta() && null !== $this->getMeta()->getLastModified()) {
            $data['meta']['updated'] = $this->getMeta()->getLastModified();
        }

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}

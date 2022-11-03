<?php

namespace Opf\Models\SCIM\Standard\Users;

use Opf\Util\Util;
use Opf\Models\SCIM\Standard\CommonEntity;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Models\SCIM\Standard\MultiValuedAttribute;

// TODO: Also implement support for enterprise user schema
class CoreUser extends CommonEntity
{
    /** @var string $userName */
    private $userName;

    /** @var \Opf\Models\SCIM\Name $name */
    private $name;

    /** @var string $displayName */
    private $displayName;

    /** @var string $nickName */
    private $nickName;

    /** @var string $profileUrl */
    private $profileUrl;

    /** @var string $title */
    private $title;

    /** @var string $userType */
    private $userType;

    /** @var string $preferredLanguage */
    private $preferredLanguage;

    /** @var string $locale */
    private $locale;

    /** @var string $timezone */
    private $timezone;

    /** @var bool $active */
    private $active;

    /** @var string $password */
    private $password;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $emails */
    private $emails;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $phoneNumbers */
    private $phoneNumbers;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $ims */
    private $ims;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $photos */
    private $photos;

    /** @var \Opf\Models\SCIM\Address[] $addresses */
    private $addresses;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $groups */
    private $groups;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $entitlements */
    private $entitlements;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $roles */
    private $roles;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute[] $x509Certificates */
    private $x509Certificates;

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getNickName()
    {
        return $this->nickName;
    }

    public function setNickName($nickName)
    {
        $this->nickName = $nickName;
    }

    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    public function setProfileUrl($profileUrl)
    {
        $this->profileUrl = $profileUrl;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getUserType()
    {
        return $this->userType;
    }

    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    public function getPreferredLanguage()
    {
        return $this->preferredLanguage;
    }

    public function setPreferredLanguage($preferredLanguage)
    {
        $this->preferredLanguage = $preferredLanguage;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    public function getPhoneNumbers()
    {
        return $this->phoneNumbers;
    }

    public function setPhoneNumbers($phoneNumbers)
    {
        $this->phoneNumbers = $phoneNumbers;
    }

    public function getIms()
    {
        return $this->ims;
    }

    public function setIms($ims)
    {
        $this->ims = $ims;
    }

    public function getPhotos()
    {
        return $this->photos;
    }

    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    public function getAddresses()
    {
        return $this->addresses;
    }

    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    public function getEntitlements()
    {
        return $this->entitlements;
    }

    public function setEntitlements($entitlements)
    {
        $this->entitlements = $entitlements;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getX509Certificates()
    {
        return $this->x509Certificates;
    }

    public function setX509Certificates($x509Certificates)
    {
        $this->x509Certificates = $x509Certificates;
    }

    // TODO: Finish the implementation of this method for all properties
    // TODO: Maybe rename this method to a more meaningful one,
    // since it's currently just translating a SCIM JSON object to a PHP SCIM object
    public function fromSCIM($data)
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        } elseif ($this->getId() !== null) {
            $this->setId($this->getId());
        } else {
            $this->setId(Util::genUuid());
        }

        $this->setUserName(isset($data['userName']) ? $data['userName'] : null);

        $name = new Name();
        if (isset($data['name']) && !empty($data['name'])) {
            if (isset($data['name']['familyName']) && !empty($data['name']['familyName'])) {
                $name->setFamilyName($data['name']['familyName']);
            }

            if (isset($data['name']['formatted']) && !empty($data['name']['formatted'])) {
                $name->setFormatted($data['name']['formatted']);
            }

            if (isset($data['name']['givenName']) && !empty($data['name']['givenName'])) {
                $name->setGivenName($data['name']['givenName']);
            }

            if (isset($data['name']['honorificPrefix']) && !empty($data['name']['honorificPrefix'])) {
                $name->setHonorificPrefix($data['name']['honorificPrefix']);
            }

            if (isset($data['name']['honorificSuffix']) && !empty($data['name']['honorificSuffix'])) {
                $name->setHonorificSuffix($data['name']['honorificSuffix']);
            }
        }
        $this->setName($name);

        $meta = new Meta();
        // This is currently commented out, since the code complains about wrongly
        // formatted timestamps sometimes when fromSCIM is called
        // TODO: Need to possibly refactor string2datetime and/or dateTime2string in order to fix this
        /*if (isset($data['meta']) && isset($data['meta']['created'])) {
            $meta->setCreated(Util::string2dateTime($data['meta']['created']));
        } else {
            $meta->setCreated(Util::dateTime2string(new \DateTime('NOW')));
        }*/
        $this->setMeta($meta);

        $this->setActive(isset($data['active']) ? $data['active'] : true);

        $emails = [];
        if (isset($data['emails']) && !empty($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $scimEmail = new MultiValuedAttribute();
                $scimEmail->setType(isset($email['type']) && !empty($email['type']) ? $email['type'] : null);
                $scimEmail->setPrimary(
                    isset($email['primary']) && !empty($email['primary']) ? $email['primary'] : null
                );
                $scimEmail->setDisplay(
                    isset($email['display']) && !empty($email['display']) ? $email['display'] : null
                );
                $scimEmail->setValue(isset($email['value']) && !empty($email['value']) ? $email['value'] : null);

                $emails[] = $scimEmail;
            }
        }
        $this->setEmails($emails);

        $this->setDisplayName(isset($data['displayName']) ? $data['displayName'] : null);
        $this->setPassword(isset($data['password']) ? strval($data['password']) : null);

        $this->setExternalId(isset($data['externalId']) ? $data['externalId'] : null);
        $this->setProfileUrl(isset($data['profileUrl']) ? $data['profileUrl'] : null);
    }

    // TODO: Maybe rename this method to a more meaningful one,
    // since it's currently just translating a SCIM PHP object to a JSON SCIM object
    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        $data = [
            'schemas' => [Util::USER_SCHEMA],
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
            'active' => boolval($this->getActive()),
            'password' => $this->getPassword(),
            'emails' => $this->getEmails(),
            'phoneNumbers' => $this->getPhoneNumbers(),
            'ims' => $this->getIms(),
            'photos' => $this->getPhotos(),
            'addresses' => $this->getAddresses(),
            'groups' => $this->getGroups(),
            'entitlements' => $this->getEntitlements(),
            'roles' => $this->getRoles(),
            'x509Certificates' => $this->getX509Certificates()
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

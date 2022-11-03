<?php

namespace Opf\Models\SCIM\Standard\Groups;

use Opf\Util\Util;
use Opf\Models\SCIM\Standard\CommonEntity;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Models\SCIM\Standard\MultiValuedAttribute;

class CoreGroup extends CommonEntity
{
    /** @var string $displayName */
    private $displayName;

    /** @var \Opf\Models\SCIM\MultiValuedAttribute $members */
    private $members;

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function setMembers($members)
    {
        $this->members = $members;
    }

    public function fromSCIM($data)
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        } elseif ($this->getId() !== null) {
            $this->setId($this->getId());
        } else {
            $this->setId(Util::genUuid());
        }

        $this->setDisplayName(isset($data['displayName']) ? $data['displayName'] : null);

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

        if (isset($data['members'])) {
            $members = [];
            foreach ($data['members'] as $member) {
                $scimMember = new MultiValuedAttribute();
                $scimMember->setValue($member);
                $members[] = $scimMember;
            }
            $this->setMembers($members);
        } else {
            $this->setMembers(null);
        }

        $this->setExternalId(isset($data['externalId']) ? $data['externalId'] : null);
    }

    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        $data = [
            'schemas' => [Util::GROUP_SCHEMA],
            'id' => $this->getId(),
            'externalId' => $this->getExternalId(),
            'meta' => null !== $this->getMeta() ? [
                'resourceType' => null !== $this->getMeta()->getResourceType()
                    ? $this->getMeta()->getResourceType() : null,
                'created' => null !== $this->getMeta()->getCreated() ? $this->getMeta()->getCreated() : null,
                'location' => $baseLocation . '/Groups/' . $this->getId(),
                'version' => null !== $this->getMeta()->getVersion() ? $this->getMeta()->getVersion() : null
            ] : null,
            'displayName' => $this->getDisplayName(),
            'members' => $this->getMembers()
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

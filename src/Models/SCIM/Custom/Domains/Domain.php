<?php

namespace Opf\Models\SCIM\Custom\Domains;

use Opf\Models\SCIM\Standard\CommonEntity;
use Opf\Models\SCIM\Standard\Meta;
use Opf\Util\Util;

class Domain extends CommonEntity
{
    /** @var string|null $domainName */
    private ?string $domainName;

    /** @var string|null $description */
    private ?string $description = null;

    /** @var int $maxAliases */
    private int $maxAliases;

    /** @var int $maxMailboxes */
    private int $maxMailboxes;

    /** @var int $maxQuota */
    private int $maxQuota;

    /** @var int $usedQuota */
    private int $usedQuota;

    /** @var bool $active */
    private bool $active;

    /**
     * @return string|null
     */
    public function getDomainName(): ?string
    {
        return $this->domainName;
    }

    /**
     * @param string|null $domainName
     */
    public function setDomainName(?string $domainName): void
    {
        $this->domainName = $domainName;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getMaxAliases(): int
    {
        return $this->maxAliases;
    }

    /**
     * @param int $maxAliases
     */
    public function setMaxAliases(int $maxAliases): void
    {
        $this->maxAliases = $maxAliases;
    }

    /**
     * @return int
     */
    public function getMaxMailboxes(): int
    {
        return $this->maxMailboxes;
    }

    /**
     * @param int $maxMailboxes
     */
    public function setMaxMailboxes(int $maxMailboxes): void
    {
        $this->maxMailboxes = $maxMailboxes;
    }

    /**
     * @return int
     */
    public function getMaxQuota(): int
    {
        return $this->maxQuota;
    }

    /**
     * @param int $maxQuota
     */
    public function setMaxQuota(int $maxQuota): void
    {
        $this->maxQuota = $maxQuota;
    }

    /**
     * @return int
     */
    public function getUsedQuota(): int
    {
        return $this->usedQuota;
    }

    /**
     * @param int $usedQuota
     */
    public function setUsedQuota(int $usedQuota): void
    {
        $this->usedQuota = $usedQuota;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * Create a Domain object from JSON SCIM data
     *
     * @param array $data The JSON SCIM data
     */
    public function fromSCIM(array $data)
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }

        $this->setExternalId(isset($data['externalId']) ? $data['externalId'] : null);

        $this->setDomainName(isset($data['domainName']) ? $data['domainName'] : null);
        $this->setDescription(isset($data['description']) ? $data['description'] : null);

        // For the int attributes that are set below, we set 0 as the default value
        // in case that nothing is supplied and/or set in the JSON
        // TODO: Is that an okayish solution with this default value?
        $this->setMaxAliases(isset($data['maxAliases']) ? $data['maxAliases'] : 0);
        $this->setMaxMailboxes(isset($data['maxMailboxes']) ? $data['maxMailboxes'] : 0);
        $this->setMaxQuota(isset($data['maxQuota']) ? $data['maxQuota'] : 0);
        $this->setUsedQuota(isset($data['usedQuota']) ? $data['usedQuota'] : 0);

        if (isset($data['meta']) && !empty($data['meta'])) {
            $meta = new Meta();
            $meta->setResourceType("Domain");
            $meta->setCreated(isset($data['meta']['created']) ? $data['meta']['created'] : null);
            $meta->setLastModified(isset($data['meta']['modified']) ? $data['meta']['modified'] : null);
            $meta->setVersion(isset($data['meta']['version']) ? $data['meta']['version'] : null);

            $this->setMeta($meta);
        }

        // In case that "active" is not set in the JSON, we set it to true by default
        // TODO: Is that an okayish solution with this default value?
        $this->setActive(isset($data['active']) ? boolval($data['active']) : true);

        $this->setSchemas(isset($data['schemas']) ? $data['schemas'] : []);
    }

    /**
     * Convert a Domain object to its JSON or array representation
     *
     * @param bool $encode A flag indicating if the object should be encoded as JSON
     * @param string $baseLocation A path indicating the base location of the SCIM server
     *
     * @return array|string|false If $encode is true, return either a JSON string or false on failure, else an array
     */
    public function toSCIM(bool $encode = true, string $baseLocation = 'http://localhost:8888/v1')
    {
        $data = [
            'id' => $this->getId(),
            'externalId' => $this->getExternalId(),
            'schemas' => [Util::DOMAIN_SCHEMA],
            'meta' => null !== $this->getMeta() ? [
                'resourceType' => null !== $this->getMeta()->getResourceType()
                    ? $this->getMeta()->getResourceType() : null,
                'created' => null !== $this->getMeta()->getCreated() ? $this->getMeta()->getCreated() : null,
                'updated' => null !== $this->getMeta()->getLastModified() ? $this->getMeta()->getLastModified() : null,
                'location' => $baseLocation . '/Domains/' . $this->getId(),
                'version' => null !== $this->getMeta()->getVersion() ? $this->getMeta()->getVersion() : null
            ] : null,
            'domainName' => null !== $this->getDomainName() ? $this->getDomainName() : null,
            'description' => null !== $this->getDescription() ? $this->getDescription() : null,
            'maxAliases' => $this->getMaxAliases(),
            'maxMailboxes' => $this->getMaxMailboxes(),
            'maxQuota' => $this->getMaxQuota(),
            'usedQuota' => $this->getUsedQuota(),
            'active' => $this->getActive()
        ];

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}

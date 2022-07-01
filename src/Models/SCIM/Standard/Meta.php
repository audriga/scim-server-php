<?php

namespace Opf\Models\SCIM\Standard;

/**
 * This class represents the common SCIM attribute "meta"
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7643#section-3.1
 */
class Meta
{
    /** @var string $resourceType */
    private $resourceType;

    /** @var string $created */
    private $created;

    /** @var string $lastModified */
    private $lastModified;

    // location is determined when converting to JSON
    // /** @var string $location */
    // private $location;

    /** @var string $version */
    private $version;

    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }
}

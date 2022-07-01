<?php

namespace Opf\Models\SCIM\Standard;

/**
 * This class contains all common attributes of SCIM resources,
 * as well as the "Schemas" attribute
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7643#section-3
 */
class CommonEntity
{
    /** @var array<string> $schemas */
    private $schemas;

    /** @var string $id */
    private $id;

    /** @var string $externalId */
    private $externalId;

    /** @var \Opf\Models\SCIM\Meta $meta */
    private $meta;

    public function getSchemas()
    {
        return $this->schemas;
    }

    public function setSchemas($schemas)
    {
        $this->schemas = $schemas;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;
    }
}

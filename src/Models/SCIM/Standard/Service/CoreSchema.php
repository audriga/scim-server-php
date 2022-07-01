<?php

namespace Opf\Models\SCIM\Standard\Service;

/**
 * This class represents the SCIM "Schema" resource object
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7643#section-7
 */
class CoreSchema
{
    /** @var string $id */
    private $id;

    /** @var string $name */
    private $name;

    /** @var string $description */
    private $description;

    /** @var array<\Opf\Models\SCIM\Attribute> $attributes */
    private $attributes;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function toSCIM($encode = true)
    {
        $data = [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "attributes" => $this->attributes
        ];

        if ($encode) {
            $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        }

        return $data;
    }
}

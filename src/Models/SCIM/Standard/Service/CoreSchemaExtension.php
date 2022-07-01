<?php

namespace Opf\Models\SCIM\Standard\Service;

class CoreSchemaExtension
{
    /** @var string */
    private $schema;

    /** @var boolean */
    private $required;

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function toSCIM($encode = false)
    {
        $data = [
            'schema' => $this->schema,
            'required' => $this->required
        ];

        if ($encode) {
            $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        }

        return $data;
    }
}

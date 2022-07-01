<?php

namespace Opf\Models\SCIM\Standard\Service;

use Opf\Util\Util;

// TODO: Refactor this model to be not only hardcode-usable like now, but to be configurable
// and usable for various configurable data storages
class CoreResourceType
{
    /** @var array<string> */
    private $schemas = [Util::RESOURCE_TYPE_SCHEMA];

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $endpoint;

    /** @var string */
    private $schema;

    /** @var array<SchemaExtension> */
    private $schemaExtensions;

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

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public function getSchemaExtensions()
    {
        return $this->schemaExtensions;
    }

    public function setSchemaExtensions($schemaExtensions)
    {
        $this->schemaExtensions = $schemaExtensions;
    }

    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        if (isset($this->schemaExtensions) && !empty($this->schemaExtensions)) {
            $transformedSchemaExtensions = [];

            foreach ($this->schemaExtensions as $schemaExtension) {
                $transformedSchemaExtensions[] = $schemaExtension->toSCIM();
            }

            $this->setSchemaExtensions($transformedSchemaExtensions);
        }

        $data = [
            'schemas' => $this->schemas,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'endpoint' => $this->endpoint,
            'schema' => $this->schema,
            'schemaExtensions' => $this->schemaExtensions,
            'meta' => [
                'location' => $baseLocation . '/' . $this->id,
                'resourceType' => 'ResourceType'
            ]
        ];

        if ($encode) {
            $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        }

        return $data;
    }
}

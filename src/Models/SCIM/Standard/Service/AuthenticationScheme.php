<?php

namespace Opf\Models\SCIM\Standard\Service;

class AuthenticationScheme
{
    /** @var string $type */
    private $type;

    /** @var string $name */
    private $name;

    /** @var string $description */
    private $description;

    /** @var string $specUri */
    private $specUri;

    /** @var string $documentationUri */
    private $documentationUri;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
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

    public function getSpecUri()
    {
        return $this->specUri;
    }

    public function setSpecUri($specUri)
    {
        $this->specUri = $specUri;
    }

    public function getDocumentationUri()
    {
        return $this->documentationUri;
    }

    public function setDocumentationUri($documentationUri)
    {
        $this->documentationUri = $documentationUri;
    }
}

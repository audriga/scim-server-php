<?php

namespace Opf\Models\SCIM\Standard\Service;

use JsonSerializable;

/**
 * This class represents the SCIM object used for the "attributes" property of the "Schema" resource
 *
 * @see https://datatracker.ietf.org/doc/html/rfc7643#section-7
 */
class Attribute implements JsonSerializable
{
    /** @var string $name */
    private $name;

    /** @var string $type */
    private $type;

    /** @var array<Attribute> $subAttributes */
    private $subAttributes;

    /** @var boolean $multiValued */
    private $multiValued;

    /** @var string $description */
    private $description;

    /** @var boolean $required */
    private $required;

    /** @var array<string> $canonicalValues */
    private $canonicalValues;

    /** @var boolean $caseExact */
    private $caseExact;

    /** @var string $mutability */
    private $mutability;

    /** @var string $returned */
    private $returned;

    /** @var string $uniqueness */
    private $uniqueness;

    /** @var array<string> $referenceTypes */
    private $referenceTypes;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getSubAttributes()
    {
        return $this->subAttributes;
    }

    public function setSubAttributes($subAttributes)
    {
        $this->subAttributes = $subAttributes;
    }

    public function getMultiValued()
    {
        return $this->multiValued;
    }

    public function setMultiValued($multiValued)
    {
        $this->multiValued = $multiValued;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function setRequired($required)
    {
        $this->required = $required;
    }

    public function getCanonicalValues()
    {
        return $this->canonicalValues;
    }

    public function setCanonicalValues($canonicalValues)
    {
        $this->canonicalValues = $canonicalValues;
    }

    public function getCaseExact()
    {
        return $this->caseExact;
    }

    public function setCaseExact($caseExact)
    {
        $this->caseExact = $caseExact;
    }

    public function getMutability()
    {
        return $this->mutability;
    }

    public function setMutability($mutability)
    {
        $this->mutability = $mutability;
    }

    public function getReturned()
    {
        return $this->returned;
    }

    public function setReturned($returned)
    {
        $this->returned = $returned;
    }

    public function getUniqueness()
    {
        return $this->uniqueness;
    }

    public function setUniqueness($uniqueness)
    {
        $this->uniqueness = $uniqueness;
    }

    public function getReferenceTypes()
    {
        return $this->referenceTypes;
    }

    public function setReferenceTypes($referenceTypes)
    {
        $this->referenceTypes = $referenceTypes;
    }

    public function jsonSerialize()
    {
        return (object)[
            "name" => $this->getName(),
            "type" => $this->getType(),
            "subAttributes" => $this->getSubAttributes(),
            "multiValued" => $this->getMultiValued(),
            "description" => $this->getDescription(),
            "required" => $this->getRequired(),
            "canonicalValues" => $this->getCanonicalValues(),
            "caseExact" => $this->getCaseExact(),
            "mutability" => $this->getMutability(),
            "returned" => $this->getReturned(),
            "uniqueness" => $this->getUniqueness(),
            "referenceTypes" => $this->getReferenceTypes()
        ];
    }
}

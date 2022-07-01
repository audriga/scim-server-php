<?php

namespace Opf\Models\SCIM\Standard\Users;

class Name
{
    /** @var string $formatted */
    private $formatted;

    /** @var string $familyName */
    private $familyName;

    /** @var string $givenName */
    private $givenName;

    /** @var string $middleName */
    private $middleName;

    /** @var string $honorificPrefix */
    private $honorificPrefix;

    /** @var string $honorificSuffix */
    private $honorificSuffix;

    public function getFormatted()
    {
        return $this->formatted;
    }

    public function setFormatted($formatted)
    {
        $this->formatted = $formatted;
    }

    public function getFamilyName()
    {
        return $this->familyName;
    }

    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    public function getGivenName()
    {
        return $this->givenName;
    }

    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    public function getHonorificPrefix()
    {
        return $this->honorificPrefix;
    }

    public function setHonorificPrefix($honorificPrefix)
    {
        $this->honorificPrefix = $honorificPrefix;
    }

    public function getHonorificSuffix()
    {
        return $this->honorificSuffix;
    }

    public function setHonorificSuffix($honorificSuffix)
    {
        $this->honorificSuffix = $honorificSuffix;
    }
}

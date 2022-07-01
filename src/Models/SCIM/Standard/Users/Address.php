<?php

namespace Opf\Models\SCIM\Standard\Users;

use Opf\Models\SCIM\Standard\MultiValuedAttribute;

class Address extends MultiValuedAttribute
{
    /** @var string $formatted */
    private $formatted;

    /** @var string $streetAddress */
    private $streetAddress;

    /** @var string $locality */
    private $locality;

    /** @var string $region */
    private $region;

    /** @var string $postalCode */
    private $postalCode;

    /** @var string $country */
    private $country;

    public function getFormatted()
    {
        return $this->formatted;
    }

    public function setFormatted($formatted)
    {
        $this->formatted = $formatted;
    }

    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
    }

    public function getLocality()
    {
        return $this->locality;
    }

    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function jsonSerialize(): mixed
    {
        return (object)[
            "type" => $this->getType(),
            "primary" => $this->getPrimary(),
            "display" => $this->getDisplay(),
            "value" => $this->getValue(),
            "\$ref" => $this->getRef(),
            "formatted" => $this->getFormatted(),
            "streetAddress" => $this->getStreetAddress(),
            "locality" => $this->getLocality(),
            "region" => $this->getRegion(),
            "postalCode" => $this->getPostalCode(),
            "country" => $this->getCountry()
        ];
    }
}

<?php

namespace Opf\Models\SCIM\Standard;

use JsonSerializable;

class MultiValuedAttribute implements JsonSerializable
{
    /** @var string $type */
    private $type;

    /** @var bool $primary */
    private $primary;

    /** @var string $display */
    private $display;

    /** @var string $value */
    private $value;

    /** @var string $ref */
    private $ref;

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function setPrimary($primary)
    {
        $this->primary = $primary;
    }

    public function getDisplay()
    {
        return $this->display;
    }

    public function setDisplay($display)
    {
        $this->display = $display;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function setRef($ref)
    {
        $this->ref = $ref;
    }

    public function jsonSerialize()
    {
        return (object)[
            "type" => $this->getType(),
            "primary" => $this->getPrimary(),
            "display" => $this->getDisplay(),
            "value" => $this->getValue(),
            "\$ref" => $this->getRef()
        ];
    }
}

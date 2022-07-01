<?php

namespace opf\Models\SCIM\Standard\Users;

class Manager
{
    /** @var string $value */
    private $value;

    /** @var string $ref */
    private $ref;

    /** @var string $displayName */
    private $displayName;

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

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }
}

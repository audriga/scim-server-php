<?php

namespace Opf\Models\SCIM\Standard\Service;

class SupportableConfigProperty
{
    /** @var boolean $supported */
    private $supported;

    public function getSupported()
    {
        return $this->supported;
    }

    public function setSupported($supported)
    {
        $this->supported = $supported;
    }
}

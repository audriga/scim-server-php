<?php

namespace Opf\Models\SCIM\Standard\Service;

class Bulk extends SupportableConfigProperty
{
    /** @var int $maxOperations */
    private $maxOperations;

    /** @var int $maxPayloadSize */
    private $maxPayloadSize;

    public function getMaxOperations()
    {
        return $this->maxOperations;
    }

    public function setMaxOperations($maxOperations)
    {
        $this->maxOperations = $maxOperations;
    }
}

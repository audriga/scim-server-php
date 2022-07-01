<?php

namespace Opf\Models\SCIM\Standard\Service;

class Filter extends SupportableConfigProperty
{
    /** @var int $maxResults */
    private $maxResults;

    public function getMaxResults()
    {
        return $this->maxResults;
    }

    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }
}

<?php

namespace Opf\Models\SCIM\Standard\Service;

use Opf\Util\Util;

class CoreServiceProviderConfiguration
{
    /** @var array<string> $schemas */
    private $schemas = [Util::SERVICE_PROVIDER_CONFIGURATION_SCHEMA];

    /** @var string $documentationUri */
    private $documentationUri;

    /** @var \Opf\Models\SCIM\SupportableConfigProperty $patch */
    private $patch;

    /** @var \Opf\Models\SCIM\Bulk $bulk */
    private $bulk;

    /** @var \Opf\Models\SCIM\Filter $filter */
    private $filter;

    /** @var \Opf\Models\SCIM\SupportableConfigProperty $changePassword */
    private $changePassword;

    /** @var \Opf\Models\SCIM\SupportableConfigProperty $sort */
    private $sort;

    /** @var \Opf\Models\SCIM\SupportableConfigProperty $etag */
    private $etag;

    /** @var array<\Opf\Models\SCIM\AuthenticationScheme> $authenticationSchemes */
    private $authenticationSchemes;

    public function getSchemas()
    {
        return $this->schemas;
    }

    public function setSchemas($schemas)
    {
        $this->schemas = $schemas;
    }

    public function getDocumentationUri()
    {
        return $this->documentationUri;
    }

    public function setDocumentationUri($documentationUri)
    {
        $this->documentationUri = $documentationUri;
    }

    public function getPatch()
    {
        return $this->patch;
    }

    public function setPatch($patch)
    {
        $this->patch = $patch;
    }

    public function getBulk()
    {
        return $this->bulk;
    }

    public function setBulk($bulk)
    {
        $this->bulk = $bulk;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function getChangePassword()
    {
        return $this->changePassword;
    }

    public function setChangePassword($changePassword)
    {
        $this->changePassword = $changePassword;
    }

    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    public function getEtag()
    {
        return $this->etag;
    }

    public function setEtag($etag)
    {
        $this->etag = $etag;
    }

    public function getAuthenticationSchemes()
    {
        return $this->authenticationSchemes;
    }

    public function setAuthenticationSchemes($authenticationSchemes)
    {
        $this->authenticationSchemes = $authenticationSchemes;
    }

    public function toSCIM($encode = true)
    {
        $data = [
            "schemas" => $this->schemas,
            "documentationUri" => $this->documentationUri,
            "patch" => $this->patch,
            "bulk" => $this->bulk,
            "filter" => $this->filter,
            "changePassword" => $this->changePassword,
            "sort" => $this->sort,
            "etag" => $this->etag,
            "authenticationSchemes" => $this->authenticationSchemes
        ];

        if ($encode) {
            $data = json_encode($data, JSON_UNESCAPED_SLASHES);
        }

        return $data;
    }
}

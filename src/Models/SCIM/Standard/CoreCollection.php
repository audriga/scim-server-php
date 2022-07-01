<?php

namespace Opf\Models\SCIM\Standard;

class CoreCollection
{
    public $schemas = ["urn:ietf:params:scim:schemas:core:2.0"];

    private $scimItems;

    public function __construct($scimItems = [])
    {
        $this->scimItems = $scimItems;
    }

    public function toSCIM($encode = true)
    {
        $data = [
            'totalResults' => count($this->scimItems),
            'startIndex' => 1,
            'schemas' => $this->schemas,
            'Resources' => $this->scimItems
        ];

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}

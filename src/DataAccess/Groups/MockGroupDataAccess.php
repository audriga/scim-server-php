<?php

namespace Opf\DataAccess\Groups;

use Illuminate\Database\Eloquent\Model;
use Opf\Util\Util;

class MockGroupDataAccess extends Model
{
    protected $table = 'groups';
    protected $fillable = ['id', 'displayName', 'members', 'created_at'];
    public $incrementing = false;

    public $schemas = ["urn:ietf:params:scim:schemas:core:2.0:Group"];
    private $baseLocation;

    public function fromArray($data)
    {
        $this->id = isset($data['id']) ? $data['id'] : (isset($this->id) ? $this->id : Util::genUuid());
        $this->displayName = $data['displayName'];
        $this->members = is_string($data['members']) ? $data['members'] : implode(",", $data['members']);
        $this->created_at = isset($data['created']) ? Util::string2dateTime($data['created']) : new \DateTime('NOW');
    }

    public function fromSCIM($data)
    {
        $this->id = isset($data['id']) ? $data['id'] : (isset($this->id) ? $this->id : Util::genUuid());
        $this->displayName = $data['displayName'];
        $this->members = is_string($data['members']) ? $data['members'] : implode(",", $data['members']);
        $this->created_at = isset($data['created']) ? Util::string2dateTime($data['created']) : new \DateTime('NOW');
    }

    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        $data = [
            'schemas' => $this->schemas,
            'id' => $this->id,
            'displayName' => $this->displayName,
            'members' => [],
            'meta' => [
                'created' => Util::dateTime2string($this->created_at),
                'location' => $baseLocation . '/Groups/' . $this->id
            ]
        ];

        if (!empty($this->members)) {
            $data['members'] = explode(',', $this->members);
        }

        if (isset($this->updated_at)) {
            $data['meta']['updated'] = Util::dateTime2string($this->updated_at);
        }

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}

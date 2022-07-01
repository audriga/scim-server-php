<?php

namespace Opf\DataAccess\Users;

use Illuminate\Database\Eloquent\Model;
use Opf\Util\Util;

class MockUserDataAccess extends Model
{
    protected $table = 'users';
    protected $fillable = ['id', 'userName', 'created_at', 'active',
        'externalId', 'profileUrl'];
    public $incrementing = false;

    public $schemas = ["urn:ietf:params:scim:schemas:core:2.0:User"];
    private $baseLocation;

    public function fromArray($data)
    {
        $this->id = isset($data['id']) ? $data['id'] : (isset($this->id) ? $this->id : Util::genUuid());
        $this->userName = isset($data['userName']) ? $data['userName'] : null;
        $this->created_at = isset($data['created']) ? Util::string2dateTime($data['created'])
            : (isset($this->created_at) ? $this->created_at : new \DateTime('NOW'));
        $this->active = isset($data['active']) ? $data['active'] : true;

        $this->externalId = isset($data['externalId']) ? $data['externalId'] : null;
        $this->profileUrl = isset($data['profileUrl']) ? $data['profileUrl'] : null;
    }

    public function fromSCIM($data)
    {
        $this->id = isset($data['id']) ? $data['id'] : (isset($this->id) ? $this->id : Util::genUuid());
        $this->userName = isset($data['userName']) ? $data['userName'] : null;
        $this->created_at = isset($data['meta']) && isset($data['meta']['created'])
            ? Util::string2dateTime($data['meta']['created'])
            : (isset($this->created_at) ? $this->created_at : new \DateTime('NOW'));
        $this->active = isset($data['active']) ? $data['active'] : true;

        $this->externalId = isset($data['externalId']) ? $data['externalId'] : null;
        $this->profileUrl = isset($data['profileUrl']) ? $data['profileUrl'] : null;
    }

    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        $data = [
            'schemas' => $this->schemas,
            'id' => $this->id,
            'externalId' => $this->externalId,
            'meta' => [
                'created' => Util::dateTime2string($this->created_at),
                'location' => $baseLocation . '/Users/' . $this->id
            ],
            'userName' => $this->userName,
            'profileUrl' => $this->profileUrl,
            'active' => (bool) $this->active
        ];

        if (isset($this->updated_at)) {
            $data['meta']['updated'] = Util::dateTime2string($this->updated_at);
        }

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}

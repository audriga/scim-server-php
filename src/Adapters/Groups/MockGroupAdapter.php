<?php

namespace Opf\Adapters\Groups;

use Opf\Adapters\AbstractAdapter;
use Opf\DataAccess\Groups\MockGroupDataAccess;

class MockGroupAdapter extends AbstractAdapter
{
    /** @var Opf\Models\MockGroup $group */
    private $group;

    public function getGroup()
    {
        return $this->group;
    }

    public function setGroup(MockGroupDataAccess $group)
    {
        $this->group = $group;
    }

    public function getId()
    {
        if (isset($this->group->id) && !empty($this->group->id)) {
            return $this->group->id;
        }
    }

    public function setId($id)
    {
        if (isset($id) && !empty($id)) {
            $this->group->id = $id;
        }
    }

    public function getCreatedAt()
    {
        if (isset($this->group->created_at) && !empty($this->group->created_at)) {
            return $this->group->created_at;
        }
    }

    public function setCreatedAt($createdAt)
    {
        if (isset($createdAt) && !empty($createdAt)) {
            $this->group->created_at = $createdAt;
        }
    }

    public function getDisplayName()
    {
        if (isset($this->group->displayName) && !empty($this->group->displayName)) {
            return $this->group->displayName;
        }
    }

    public function setDisplayName($displayName)
    {
        if (isset($displayName) && !empty($displayName)) {
            $this->group->displayName = $displayName;
        }
    }

    public function getMembers()
    {
        if (isset($this->group->members) && !empty($this->group->members)) {
            return $this->group->members;
        }
    }

    public function setMembers($members)
    {
        if (isset($members) && !empty($members)) {
            $this->group->members = $members;
        }
    }
}

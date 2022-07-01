<?php

namespace Opf\Adapters\Users;

use Opf\Adapters\AbstractAdapter;
use Opf\DataAccess\Users\MockUserDataAccess;

class MockUserAdapter extends AbstractAdapter
{
    /** @var Opf\Models\MockUser $user */
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(MockUserDataAccess $user)
    {
        $this->user = $user;
    }

    public function getId()
    {
        if (isset($this->user->id) && !empty($this->user->id)) {
            return $this->user->id;
        }
    }

    public function setId($id)
    {
        if (isset($id) && !empty($id)) {
            $this->user->id = $id;
        }
    }

    public function getUserName()
    {
        if (isset($this->user->userName) && !empty($this->user->userName)) {
            return $this->user->userName;
        }
    }

    public function setUserName($userName)
    {
        if (isset($userName) && !empty($userName)) {
            $this->user->userName = $userName;
        }
    }

    public function getCreatedAt()
    {
        if (isset($this->user->created_at) && !empty($this->user->created_at)) {
            return $this->user->created_at;
        }
    }

    public function setCreatedAt($createdAt)
    {
        if (isset($createdAt) && !empty($createdAt)) {
            $this->user->created_at = $createdAt;
        }
    }

    public function getActive()
    {
        if (isset($this->user->active) && !empty($this->user->active)) {
            return boolval($this->user->active);
        }
    }

    public function setActive($active)
    {
        if (isset($active) && !empty($active)) {
            $this->user->active = $active;
        }
    }

    public function getExternalId()
    {
        if (isset($this->user->externalId) && !empty($this->user->externalId)) {
            return $this->user->externalId;
        }
    }

    public function setExternalId($externalId)
    {
        if (isset($externalId) && !empty($externalId)) {
            $this->user->externalId = $externalId;
        }
    }

    public function getProfileUrl()
    {
        if (isset($this->user->profileUrl) && !empty($this->user->profileUrl)) {
            return $this->user->profileUrl;
        }
    }

    public function setProfileUrl($profileUrl)
    {
        if (isset($profileUrl) && !empty($profileUrl)) {
            $this->user->profileUrl = $profileUrl;
        }
    }
}

<?php

namespace Opf\Models\Mock;

class MockUser extends MockCommonEntity
{
    /** @var string|null $userName */
    private $userName;

    /** @var bool $active */
    private $active;

    /** @var string|null $externalId */
    private $externalId;

    /** @var string|null $profileUrl */
    private $profileUrl;

    public function mapFromArray($properties = null): bool
    {
        $result = true;
        if ($properties !== null) {
            foreach ($properties as $key => $value) {
                if (strcasecmp($key, 'id') === 0) {
                    $this->id = $value;
                    continue;
                }

                if (strcasecmp($key, 'created_at') === 0) {
                    $this->createdAt = $value;
                    continue;
                }

                if (strcasecmp($key, 'updated_at') === 0) {
                    $this->updatedAt = $value;
                    continue;
                }

                if (strcasecmp($key, 'userName') === 0) {
                    $this->userName = $value;
                    continue;
                }

                if (strcasecmp($key, 'active') === 0) {
                    if ($value === "1") {
                        $this->active = true;
                    } elseif ($value === "0") {
                        $this->active = false;
                    } else {
                        $this->active = $value;
                    }
                    continue;
                }

                if (strcasecmp($key, 'externalId') === 0) {
                    $this->externalId = $value;
                    continue;
                }

                if (strcasecmp($key, 'profileUrl') === 0) {
                    $this->profileUrl = $value;
                    continue;
                }
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    public function setProfileUrl($profileUrl)
    {
        $this->profileUrl = $profileUrl;
    }
}

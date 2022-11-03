<?php

namespace Opf\Models\Mock;

class MockGroup extends MockCommonEntity
{
    /** @var string|null $displayName */
    private $displayName;

    /** @var array<string>|null $members */
    private $members;

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

                if (strcasecmp($key, 'displayName') === 0) {
                    $this->displayName = $value;
                    continue;
                }

                if (strcasecmp($key, 'members') === 0) {
                    $this->members = $value;
                    continue;
                }
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getMembers()
    {
        return $this->members;
    }

    public function setMembers($members)
    {
        $this->members = $members;
    }
}

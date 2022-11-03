<?php

namespace Opf\Models\Mock;

class MockCommonEntity
{
    /** @var string|null $id */
    protected $id;

    /** @var string|null $createdAt */
    protected $createdAt;

    /** @var string|null $updatedAt */
    protected $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}

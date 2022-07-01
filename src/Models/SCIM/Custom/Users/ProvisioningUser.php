<?php

namespace Opf\Models\SCIM\Custom\Users;

use Opf\Models\SCIM\Standard\Users\CoreUser;
use Opf\Util\Util;

class ProvisioningUser extends CoreUser
{
    /** @var ?int $sizeQuota */
    private ?int $sizeQuota = null;

    /**
     * @return int|null
     */
    public function getSizeQuota(): ?int
    {
        return $this->sizeQuota;
    }

    /**
     * @param int|null $sizeQuota
     */
    public function setSizeQuota(?int $sizeQuota): void
    {
        $this->sizeQuota = $sizeQuota;
    }

    public function fromSCIM($data)
    {
        parent::fromSCIM($data);
        if (isset($data[Util::PROVISIONING_USER_SCHEMA])) {
            $provisioningUserData = $data[Util::PROVISIONING_USER_SCHEMA];
            $this->setSizeQuota(isset($provisioningUserData['sizeQuota']) ? $provisioningUserData['sizeQuota'] : null);
        }
    }

    public function toSCIM($encode = true, $baseLocation = 'http://localhost:8888/v1')
    {
        $data = parent::toSCIM($encode, $baseLocation);
        $data['schemas'][] = Util::PROVISIONING_USER_SCHEMA;
        $data[Util::PROVISIONING_USER_SCHEMA]['sizeQuota'] = $this->getSizeQuota();

        if (null !== $this->getMeta() && null !== $this->getMeta()->getLastModified()) {
            $data['meta']['updated'] = $this->getMeta()->getLastModified();
        }

        if ($encode) {
            $data = json_encode($data);
        }

        return $data;
    }
}

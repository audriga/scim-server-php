<?php

namespace Opf\Models\PFA;

class PfaUser
{
    /** @var string|null $userName */
    private ?string $userName = null;

    /** @var string|null $password */
    private ?string $password = null;

    /** @var string|null $name */
    private ?string $name = null;

    /** @var string|null $maildir */
    private ?string $maildir = null;

    /** @var int|null $quota */
    private ?int $quota = null;

    /** @var string|null $localPart */
    private ?string $localPart = null;

    /** @var string|null $domain */
    private ?string $domain = null;

    /** @var string|null $created */
    private ?string $created = null;

    /** @var string|null $modified */
    private ?string $modified = null;

    /** @var string|null $active */
    private ?string $active = null;

    /** @var string|null $phone */
    private ?string $phone = null;

    /** @var string|null $emailOther */
    private ?string $emailOther = null;

    /** @var string|null $token */
    private ?string $token = null;

    /** @var string|null $tokenValidity */
    private ?string $tokenValidity = null;

    /** @var string|null $passwordExpiry */
    private ?string $passwordExpiry = null;

    public function mapFromArray($properties = null): bool
    {
        $result = true;
        if ($properties !== null) {
            foreach ($properties as $key => $value) {
                if (strcasecmp($key, 'userName') === 0) {
                    $this->userName = $value;
                    continue;
                }
                if (strcasecmp($key, 'password') === 0) {
                    $this->password = $value;
                    continue;
                }
                if (strcasecmp($key, 'name') === 0) {
                    $this->name = $value;
                    continue;
                }
                if (strcasecmp($key, 'maildir') === 0) {
                    $this->maildir = $value;
                    continue;
                }
                if (strcasecmp($key, 'quota') === 0) {
                    $this->quota = $value;
                    continue;
                }
                if (strcasecmp($key, 'localpart') === 0) {
                    $this->localpart = $value;
                    continue;
                }
                if (strcasecmp($key, 'domain') === 0) {
                    $this->domain = $value;
                    continue;
                }
                if (strcasecmp($key, 'created') === 0) {
                    $this->created = $value;
                    continue;
                }
                if (strcasecmp($key, 'modified') === 0) {
                    $this->modified = $value;
                    continue;
                }
                if (strcasecmp($key, 'active') === 0) {
                    $this->active = $value;
                    continue;
                }
                if (strcasecmp($key, 'phone') === 0) {
                    $this->phone = $value;
                    continue;
                }
                if (strcasecmp($key, 'emailOther') === 0) {
                    $this->emailOther = $value;
                    continue;
                }
                if (strcasecmp($key, 'token') === 0) {
                    $this->token = $value;
                    continue;
                }
                if (strcasecmp($key, 'tokenValidity') === 0) {
                    $this->tokenValidity = $value;
                    continue;
                }
                if (strcasecmp($key, 'passwordExpiry') === 0) {
                    $this->passwordExpiry = $value;
                    continue;
                }
                $result = false;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string|null $userName
     */
    public function setUserName(?string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getMaildir(): ?string
    {
        return $this->maildir;
    }

    /**
     * @param string|null $maildir
     */
    public function setMaildir(?string $maildir): void
    {
        $this->maildir = $maildir;
    }

    /**
     * @return int|null
     */
    public function getQuota(): ?int
    {
        return $this->quota;
    }

    /**
     * @param int|null $quota
     */
    public function setQuota(?int $quota): void
    {
        $this->quota = $quota;
    }

    /**
     * @return string|null
     */
    public function getLocalPart(): ?string
    {
        return $this->localPart;
    }

    /**
     * @param string|null $localPart
     */
    public function setLocalPart(?string $localPart): void
    {
        $this->localPart = $localPart;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string|null $domain
     */
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return string|null
     */
    public function getCreated(): ?string
    {
        return $this->created;
    }

    /**
     * @param string|null $created
     */
    public function setCreated(?string $created): void
    {
        $this->created = $created;
    }

    /**
     * @return string|null
     */
    public function getModified(): ?string
    {
        return $this->modified;
    }

    /**
     * @param string|null $modified
     */
    public function setModified(?string $modified): void
    {
        $this->modified = $modified;
    }

    /**
     * @return string|null
     */
    public function getActive(): ?string
    {
        return $this->active;
    }

    /**
     * @param string|null $active
     */
    public function setActive(?string $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getEmailOther(): ?string
    {
        return $this->emailOther;
    }

    /**
     * @param string|null $emailOther
     */
    public function setEmailOther(?string $emailOther): void
    {
        $this->emailOther = $emailOther;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string|null
     */
    public function getTokenValidity(): ?string
    {
        return $this->tokenValidity;
    }

    /**
     * @param string|null $tokenValidity
     */
    public function setTokenValidity(?string $tokenValidity): void
    {
        $this->tokenValidity = $tokenValidity;
    }

    /**
     * @return string|null
     */
    public function getPasswordExpiry(): ?string
    {
        return $this->passwordExpiry;
    }

    /**
     * @param string|null $passwordExpiry
     */
    public function setPasswordExpiry(?string $passwordExpiry): void
    {
        $this->passwordExpiry = $passwordExpiry;
    }
}

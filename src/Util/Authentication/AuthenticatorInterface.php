<?php

namespace Opf\Util\Authentication;

interface AuthenticatorInterface
{
    /**
     * A method for performing authentication
     *
     * @param string $credentials The authentication credentials
     * @param array $authorizationInfo Array with infor to check if user is authorized to perform a given operation
     *
     * @return bool Return true if authentication succeeded, otherwise false
     */
    public function authenticate(string $credentials, array $authorizationInfo): bool;
}

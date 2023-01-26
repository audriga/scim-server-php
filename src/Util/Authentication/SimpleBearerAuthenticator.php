<?php

namespace Opf\Util\Authentication;

use Exception;
use Opf\Vendor\Firebase\JWT\JWT;
use Opf\Vendor\Firebase\JWT\Key;
use Opf\Util\Util;
use Psr\Container\ContainerInterface;

class SimpleBearerAuthenticator implements AuthenticatorInterface
{
    /** @var \Monolog\Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get(\Monolog\Logger::class);
    }

    public function authenticate(string $credentials, array $authorizationInfo): bool
    {
        $jwtPayload = [];
        $jwtSecret = Util::getConfigFile()['jwt']['secret'];
        try {
            $jwtPayload = (array) JWT::decode($credentials, new Key($jwtSecret, 'HS256'));
        } catch (Exception $e) {
            // If we land here, something was wrong with the JWT and auth has thus failed
            $this->logger->error($e->getMessage());
            return false;
        }
        return true;
    }
}

<?php

namespace Opf\Util;

abstract class Util
{
    public const USER_SCHEMA = "urn:ietf:params:scim:schemas:core:2.0:User";
    public const ENTERPRISE_USER_SCHEMA = "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User";
    public const PROVISIONING_USER_SCHEMA = "urn:audriga:params:scim:schemas:extension:provisioning:2.0:User";
    public const GROUP_SCHEMA = "urn:ietf:params:scim:schemas:core:2.0:Group";
    public const RESOURCE_TYPE_SCHEMA = "urn:ietf:params:scim:schemas:core:2.0:ResourceType";
    public const SERVICE_PROVIDER_CONFIGURATION_SCHEMA = "urn:ietf:params:scim:schemas:core:2.0:ServiceProviderConfig";

    // Note: The name below probably doesn't make much sense,
    // but I went for it for consistency's sake as with the other names above
    public const SCHEMA_SCHEMA = "urn:ietf:params:scim:schemas:core:2.0:Schema";

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public static function dateTime2string(\DateTime $dateTime = null)
    {
        if (!isset($dateTime)) {
            $dateTime = new \DateTime("NOW");
        }

        if ($dateTime->getTimezone()->getName() === \DateTimeZone::UTC) {
            return $dateTime->format('Y-m-d\Th:i:s\Z');
        } else {
            return $dateTime->format('Y-m-d\TH:i:sP');
        }
    }

    /**
     * @param string $string
     * @param \DateTimeZone $zone
     *
     * @return \DateTime
     */
    public static function string2dateTime($string, \DateTimeZone $zone = null)
    {
        if (!$zone) {
            $zone = new \DateTimeZone('UTC');
        }

        $dt = new \DateTime('now', $zone);
        $dt->setTimestamp(self::string2timestamp($string));
        return $dt;
    }

    /**
     * @param $string
     *
     * @return int
     */
    public static function string2timestamp($string)
    {
        $matches = array();
        if (
            !preg_match(
                '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D',
                $string,
                $matches
            )
        ) {
            throw new \InvalidArgumentException('Invalid timestamp: ' . $string);
        }

        $year = intval($matches[1]);
        $month = intval($matches[2]);
        $day = intval($matches[3]);
        $hour = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);

        // Use gmmktime because the timestamp will always be given in UTC?
        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);
        return $ts;
    }

    public static function getUserNameFromFilter($filter)
    {
        $username = null;
        if (preg_match('/userName eq \"([a-z0-9\_\.\-\@]*)\"/i', $filter, $matches) === 1) {
            $username = $matches[1];
        }
        return $username;
    }

    public static function genUuid(): string
    {
        $uuid4 = \Ramsey\Uuid\Uuid::uuid4();
        return $uuid4->toString();
    }

    public static function buildDbDsn(): ?string
    {
        $config = self::getConfigFile();
        if (isset($config) && !empty($config)) {
            if (isset($config['db']) && !empty($config['db'])) {
                if (
                    isset($config['db']['driver']) && !empty($config['db']['driver'])
                    && isset($config['db']['host']) && !empty($config['db']['host'])
                    && isset($config['db']['port']) && !empty($config['db']['port'])
                    && isset($config['db']['database']) && !empty($config['db']['database'])
                ) {
                    return $config['db']['driver'] . ':host='
                         . $config['db']['host'] . ';port='
                         . $config['db']['port'] . ';dbname='
                         . $config['db']['database'];
                }
            }
        }

        // In case we can't build a DSN, just return null
        // Note: make sure to check for null equality in the caller
        return null;
    }

    public static function getDomainFromEmail($email)
    {
        $parts = explode("@", $email);
        if (count($parts) != 2) {
            return null;
        }
        return $parts[1];
    }

    public static function getLocalPartFromEmail($email)
    {
        $parts = explode("@", $email);
        if (count($parts) != 2) {
            return null;
        }
        return $parts[0];
    }

    /**
     * This function can (and should) be used for obtaining the config file of the scim-server-php
     * It tries to fetch the custom-defined config file and return its contents
     * If no custom config file exists, it resorts to the config.default.php file as a fallback
     *
     * Either way, it returns the config file's contents in the form of an associative array
     */
    public static function getConfigFile()
    {
        $defaultConfigFilePath = dirname(__DIR__) . '/../config/config.default.php';
        $customConfigFilePath = dirname(__DIR__) . '/../config/config.php';

        $config = [];

        // In case we don't have a custom config, we just rely on the default one
        if (!file_exists($customConfigFilePath)) {
            $config = require($defaultConfigFilePath);
        } else {
            $config = require($customConfigFilePath);
        }

        return $config;
    }
}

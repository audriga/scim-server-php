<?php

namespace Opf\Util;

use Opf\Models\SCIM\Standard\Service\CoreResourceType;
use Opf\Models\SCIM\Standard\Service\CoreSchemaExtension;
use Exception;
use PDO;

abstract class Util
{
    private static string $defaultConfigFilePath = __DIR__ . '/../../config/config.default.php';
    private static string $customConfigFilePath = '';

    public const USER_SCHEMA = "urn:ietf:params:scim:schemas:core:2.0:User";
    public const ENTERPRISE_USER_SCHEMA = "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User";
    public const PROVISIONING_USER_SCHEMA = "urn:ietf:params:scim:schemas:extension:audriga:provisioning:2.0:User";
    public const DOMAIN_SCHEMA = "urn:ietf:params:scim:schemas:audriga:2.0:Domain";
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
                    && isset($config['db']['database']) && !empty($config['db']['database']
                    && strcmp($config['db']['driver'], 'mysql') === 0)
                ) {
                    return $config['db']['driver'] . ':host='
                         . $config['db']['host'] . ';port='
                         . $config['db']['port'] . ';dbname='
                         . $config['db']['database'];
                } elseif (
                    isset($config['db']['driver']) && !empty($config['db']['driver'])
                    && isset($config['db']['databaseFile']) && !empty($config['db']['databaseFile']
                    && strcmp($config['db']['driver'], 'sqlite') === 0)
                ) {
                    return $config['db']['driver'] . ':host='
                        . '../../' . $config['db']['databaseFile'];
                }
            }
        }

        // In case we can't build a DSN, just return null
        // Note: make sure to check for null equality in the caller
        return null;
    }

    /**
     * Utility method for providing a DB connection via PDO
     *
     * @throws Exception if there was an issue with obtaining the DB connection
     * @return PDO A PDO object representing the DB connection
     */
    public static function getDbConnection()
    {
        // Try to obtain a DSN and complain with an Exception if there's no DSN
        $dsn = self::buildDbDsn();
        if (!isset($dsn)) {
            throw new Exception("Can't obtain DSN to connect to DB");
        }

        $config = self::getConfigFile();
        if (isset($config) && !empty($config)) {
            if (isset($config['db']) && !empty($config['db'])) {
                if (
                    isset($config['db']['user'])
                    && !empty($config['db']['user'])
                    && isset($config['db']['password'])
                    && !empty($config['db']['password'])
                ) {
                    $dbUsername = $config['db']['user'];
                    $dbPassword = $config['db']['password'];
                } else {
                    // If no DB username and/or password provided, throw an Exception
                    throw new Exception("No DB username and/or password provided to connect to DB");
                }
            }
        }

        // Create the DB connection with PDO
        try {
            $dbConnection = new PDO($dsn, $dbUsername, $dbPassword);
        } catch (Exception $e) {
            throw $e;
        }

        // Tell PDO explicitly to throw exceptions on errors, so as to have more info when debugging DB operations
        if (isset($config['isInProduction'])) {
            if ($config['isInProduction'] === false) {
                $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        }

        return $dbConnection;
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
        $config = [];

        // In case we don't have a custom config, we just rely on the default one
        if (!file_exists(self::$customConfigFilePath)) {
            $config = require(self::$defaultConfigFilePath);
        } else {
            $config = require(self::$customConfigFilePath);
        }

        return $config;
    }

    public static function setConfigFile(string $configFilePath)
    {
        self::$customConfigFilePath = $configFilePath;
    }

    /**
     * A utility method for obtaining the supported SCIM resource types
     *
     * @param string $baseUrl A base URL required for each resource type that is returned
     *
     * @return array The array containing the resource types
     */
    public static function getResourceTypes($baseUrl)
    {
        // Check which resource types are supported via the config file and in this method further down below
        // make sure to only return those that are indeed supported
        $config = Util::getConfigFile();
        $supportedResourceTypes = $config['supportedResourceTypes'];

        $scimResourceTypes = [];

        if (in_array('User', $supportedResourceTypes)) {
            $userResourceType = new CoreResourceType();
            $userResourceType->setId("User");
            $userResourceType->setName("User");
            $userResourceType->setEndpoint("/Users");
            $userResourceType->setDescription("User Account");
            $userResourceType->setSchema(Util::USER_SCHEMA);

            if (in_array('EnterpriseUser', $supportedResourceTypes)) {
                $enterpriseUserSchemaExtension = new CoreSchemaExtension();
                $enterpriseUserSchemaExtension->setSchema(Util::ENTERPRISE_USER_SCHEMA);
                $enterpriseUserSchemaExtension->setRequired(true);

                $userResourceType->setSchemaExtensions(array($enterpriseUserSchemaExtension));
            }
            if (in_array('ProvisioningUser', $supportedResourceTypes)) {
                $provisioningUserSchemaExtension = new CoreSchemaExtension();
                $provisioningUserSchemaExtension->setSchema(Util::PROVISIONING_USER_SCHEMA);
                $provisioningUserSchemaExtension->setRequired(true);

                $userResourceType->setSchemaExtensions(array($provisioningUserSchemaExtension));
            }

            $scimResourceTypes[] = $userResourceType->toSCIM(false, $baseUrl);
        }

        if (in_array('Group', $supportedResourceTypes)) {
            $groupResourceType = new CoreResourceType();
            $groupResourceType->setId("Group");
            $groupResourceType->setName("Group");
            $groupResourceType->setEndpoint("/Groups");
            $groupResourceType->setDescription("Group");
            $groupResourceType->setSchema("urn:ietf:params:scim:schemas:core:2.0:Group");
            $groupResourceType->setSchemaExtensions([]);

            $scimResourceTypes[] = $groupResourceType->toSCIM(false, $baseUrl);
        }

        if (in_array('Domain', $supportedResourceTypes)) {
            $domainResourceType = new CoreResourceType();
            $domainResourceType->setId("Domain");
            $domainResourceType->setName("Domain");
            $domainResourceType->setEndpoint("/Domains");
            $domainResourceType->setDescription("Domain");
            $domainResourceType->setSchema(self::DOMAIN_SCHEMA);
            $domainResourceType->setSchemaExtensions([]);

            $scimResourceTypes[] = $domainResourceType->toSCIM(false, $baseUrl);
        }

        return $scimResourceTypes;
    }

    /**
     * A utility method for obtaining the configured SCIM schemas
     *
     * @return array|null Return an array of schemas or null if no schemas were found
     */
    public static function getSchemas()
    {
        $config = Util::getConfigFile();
        $supportedSchemas = $config['supportedResourceTypes'];
        $mandatorySchemas = ['Schema', 'ResourceType'];

        $scimSchemas = [];

        // We store the schemas that the SCIM server supports in separate JSON files
        // That's why we try to read them here and add them to $scimSchemas, which is returned as a result
        $pathToSchemasDir = dirname(__DIR__, 2) . '/config/Schema';
        $schemaFiles = scandir($pathToSchemasDir, SCANDIR_SORT_NONE);

        // If scandir() failed (i.e., it returned false), then return null
        if ($schemaFiles === false) {
            return null;
        }

        foreach ($schemaFiles as $schemaFile) {
            if (!in_array($schemaFile, array('.', '..'))) {
                $scimSchemaJsonDecoded = json_decode(file_get_contents($pathToSchemasDir . '/' . $schemaFile), true);

                // Only return schemas that are either mandatory (like the 'Schema' and 'ResourceType' ones)
                // or supported by the server
                if (in_array($scimSchemaJsonDecoded['name'], array_merge($supportedSchemas, $mandatorySchemas))) {
                    $scimSchemas[] = $scimSchemaJsonDecoded;
                }
            }
        }

        return $scimSchemas;
    }

    /**
     * A utility method for obtaining the SCIM service provider configuration
     *
     * @return string|null Return the service provider configuration or null if no config was found
     */
    public static function getServiceProviderConfig()
    {
        $pathToServiceProviderConfigurationFile =
            dirname(__DIR__, 2) . '/config/ServiceProviderConfig/serviceProviderConfig.json';

        $scimServiceProviderConfigurationFile = file_get_contents($pathToServiceProviderConfigurationFile);

        // If there was no service provider config JSON file found, then return null
        if ($scimServiceProviderConfigurationFile === false) {
            return null;
        }

        return json_decode($scimServiceProviderConfigurationFile);
    }
}

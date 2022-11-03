<?php

use Opf\ScimServer;

require __DIR__ . '/../vendor/autoload.php';

// Obtain the root of the project
$scimServerPhpRoot = dirname(__DIR__);

// Create a new ScimServer instance and give it the project root
$scimServer = new ScimServer($scimServerPhpRoot);

// Take the config file path and pass it to the scimServer instance
$configFilePath = __DIR__ . '/../config/config.php';
$scimServer->setConfig($configFilePath);

// Obtain custom dependencies (if any) and pass them to the scimServer instance
$dependencies = require __DIR__ . '/../src/Dependencies/mock-dependencies.php';
$scimServer->setDependencies($dependencies);

// Set the Authentication Middleware configured in the dependencies files above to the scimServer instance
//$scimServerPhpAuthMiddleware = 'AuthMiddleware';
//$scimServer->setMiddleware(array($scimServerPhpAuthMiddleware));

// Start the scimServer
$scimServer->run();

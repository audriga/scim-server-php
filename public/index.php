<?php

use DI\ContainerBuilder;
use Opf\Handlers\HttpErrorHandler;
use Opf\Util\Util;
use Slim\Factory\AppFactory;

require dirname(__DIR__) . '/vendor/autoload.php';
session_start();

// Instantiate the PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

$config = Util::getConfigFile();
if ($config['isInProduction']) {
    $containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up a few Slim-related settings
$settings = [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    ]
];
$containerBuilder->addDefinitions($settings);

// Set up common dependencies
$dependencies = require dirname(__DIR__) . '/src/Dependencies/dependencies.php';
$dependencies($containerBuilder);

// Set up system-specific dependencies
$dependencies = require dirname(__DIR__) . '/src/Dependencies/mock-dependencies.php';
$dependencies($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

// Set our app's base path if it's configured
if (isset($config['basePath']) && !empty($config['basePath'])) {
    $app->setBasePath($config['basePath']);
}

// Set up the ORM
$eloquent = require dirname(__DIR__) . '/src/eloquent.php';
$eloquent($app);

// Register routes
$routes = require dirname(__DIR__) . '/src/routes.php';
$routes($app);

// Add Routing Middleware
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

// Add JWT middleware
$app->addMiddleware($container->get(JwtAuthentication::class));

// Instantiate our custom Http error handler that we need further down below
$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

// Add error middleware
$errorMiddleware = $app->addErrorMiddleware(
    $config['isInProduction'] ? false : true,
    true,
    true
);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run app
$app->run();

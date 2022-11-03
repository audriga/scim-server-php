<?php

namespace Opf;

use DI\ContainerBuilder;
use Exception;
use Opf\Handlers\HttpErrorHandler;
use Opf\Util\Util;
use Slim\App;
use Slim\Factory\AppFactory;

class ScimServer
{
    /**
     * @var string $scimServerPhpRoot The root of the project using
     * OPF as a dependency. This is needed for autoloading purposes,
     * such that all classes of the project using OPF can be made
     * visible to OPF.
     */
    private string $scimServerPhpRoot;

    /**
     * A container builder which is used to configure and create a
     * DI container, used by the Slim application
     */
    private ContainerBuilder $containerBuilder;

    /**
     * The Slim application to configure and run as a server, exposing
     * the SCIM API
     */
    private App $app;

    /**
     * @var array $dependencies An array holding dependency definitions,
     * passed to the DI ContainerBuilder for configuring the DI container
     */
    private array $dependencies;

    /**
     * @var array $middleware Any custom middleware that needs to be added
     * to the Slim application (e.g., custom auth middleware)
     */
    private array $middleware;

    /**
     * ScimServer class constructor
     *
     * @param string $scimServerPhpRoot The root of the project using
     * the OPF library. Needed for autoloading. See more in description
     * of the dedicated class property of the same name
     */
    public function __construct(string $scimServerPhpRoot)
    {
        $this->scimServerPhpRoot = $scimServerPhpRoot;

        /**
         * Once we have the root directory of the project that's using
         * OPF, we include its autoload file, so that we don't run into
         * autoloading issues.
         */
        require $this->scimServerPhpRoot . '/vendor/autoload.php';
    }

    public function setConfig(string $configFilePath)
    {
        if (!isset($configFilePath) || empty($configFilePath)) {
            throw new Exception("Config file path must be supplied");
        }

        Util::setConfigFile($configFilePath);
    }

    public function setDependencies(array $dependencies = array())
    {
        $baseDependencies = require __DIR__ . '/Dependencies/dependencies.php';
        $this->dependencies = array_merge($baseDependencies, $dependencies);
    }

    public function setMiddleware(array $middleware = array())
    {
        $this->middleware = $middleware;
    }

    public function run()
    {
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

        // Set all necessary dependencies which are provided in this class'
        // $dependencies attribute
        $containerBuilder->addDefinitions($this->dependencies);

        // Build PHP-DI Container instance
        $container = $containerBuilder->build();

        // Instantiate the app
        AppFactory::setContainer($container);
        $this->app = AppFactory::create();

        // Set our app's base path if it's configured
        if (isset($config['basePath']) && !empty($config['basePath'])) {
            $this->app->setBasePath($config['basePath']);
        }

        // Register routes
        $routes = require __DIR__ . '/routes.php';
        $routes($this->app);


        // Iterate through the custom middleware (if any) and set it
        if (isset($this->middleware) && !empty($this->middleware)) {
            foreach ($this->middleware as $middleware) {
                $this->app->addMiddleware($this->app->getContainer()->get($middleware));
            }
        }

        // Add Routing Middleware
        $this->app->addRoutingMiddleware();
        $this->app->addBodyParsingMiddleware();

        $callableResolver = $this->app->getCallableResolver();
        $responseFactory = $this->app->getResponseFactory();

        // Instantiate our custom Http error handler that we need further down below
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);

        // Add error middleware
        $errorMiddleware = $this->app->addErrorMiddleware(
            $config['isInProduction'] ? false : true,
            true,
            true
        );
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        // Run app
        $this->app->run();
    }
}

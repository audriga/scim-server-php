<?php

namespace Opf\Controllers;

use Monolog\Logger;
use Opf\Util\Util;
use Psr\Container\ContainerInterface;

class Controller
{
    protected $container;
    protected $logger;
    protected $repository;

    // The Slim app's base path that we need when constructing the SCIM "location" property
    protected $basePath;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get(Logger::class);

        $config = Util::getConfigFile();
        if (isset($config['basePath']) && !empty($config['basePath'])) {
            $this->basePath = $config['basePath'];
        }
    }
}

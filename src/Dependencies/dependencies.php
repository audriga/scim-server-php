<?php

declare(strict_types=1);

use Opf\Controllers\Controller;
use Opf\Util\Util;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

return [
    // Monolog
    Monolog\Logger::class => function () {
        $config = Util::getConfigFile();
        $settings = $config['logger'];
        $logger = new Monolog\Logger($settings['name']);
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    },

    // Controllers
    Controller::class => function (ContainerInterface $c) {
        return new Controller($c);
    }
];

<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Opf\Controllers\Controller;
use Opf\Util\Util;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Middleware\JwtAuthentication;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Monolog
        Monolog\Logger::class => function (ContainerInterface $c) {
            $config = Util::getConfigFile();
            $settings = $config['logger'];
            $logger = new Monolog\Logger($settings['name']);
            $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
            return $logger;
        },

        // JWT
        'JwtAuthentication' => function (ContainerInterface $c) {
            $config = Util::getConfigFile();
            $settings = $config['jwt'];
            $settings["logger"] = $c->get(Monolog\Logger::class);
            $settings["attribute"] = "jwt";

            // Don't ask for JWT when trying to obtain one
            $basePath = "";
            if (isset($config) && !empty($config)) {
                if (isset($config["basePath"]) && !empty($config["basePath"])) {
                    $basePath = $config["basePath"];
                }
            }
            $settings["ignore"] = [$basePath . "/jwt"];

            if (!isset($settings['error'])) {
                $settings["error"] = function (
                    ResponseInterface $response,
                    $arguments
                ) {
                    $data["status"] = "error";
                    $data["message"] = $arguments["message"];
                    return $response
                        ->withHeader("Content-Type", "application/json")
                        ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                };
            }
            return new JwtAuthentication($settings);
        },

        // Controllers
        Controller::class => function (ContainerInterface $c) {
            return new Controller($c);
        }
    ]);
};

<?php

namespace Opf\Controllers\ServiceProviders;

use Opf\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListServiceProviderConfigurationsAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET ServiceProviderConfigurations");

        $pathToServiceProviderConfigurationFile =
            dirname(__DIR__, 3) . '/config/ServiceProviderConfig/serviceProviderConfig.json';

        $scimServiceProviderConfigurationFile = file_get_contents($pathToServiceProviderConfigurationFile);

        if ($scimServiceProviderConfigurationFile === false) {
            $this->logger->info("No ServiceProviderConfiguration found");
            $response = new Response($status = 404);
            $response = $response->withHeader('Content-Type', 'application/scim+json');

            return $response;
        }

        $responseBody = $scimServiceProviderConfigurationFile;
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');

        return $response;
    }
}

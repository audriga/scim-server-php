<?php

namespace Opf\Controllers\ServiceProviders;

use Opf\Controllers\Controller;
use Opf\Util\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListServiceProviderConfigurationsAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET ServiceProviderConfigurations");

        $scimServiceProviderConfiguration = Util::getServiceProviderConfig();

        if (is_null($scimServiceProviderConfiguration)) {
            $this->logger->info("No ServiceProviderConfiguration found");
            $response = new Response($status = 404);
            $response = $response->withHeader('Content-Type', 'application/scim+json');

            return $response;
        }

        $responseBody = $scimServiceProviderConfiguration;
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');

        return $response;
    }
}

<?php

namespace Opf\Controllers\ServiceProviders;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Opf\Util\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListSchemasAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Schemas");

        $scimSchemas = Util::getSchemas();

        // If there were no schemas found, return 404
        if (is_null($scimSchemas)) {
            $this->logger->info("No Schemas found");
            $response = new Response($status = 404);
            $response = $response->withHeader('Content-Type', 'application/scim+json');

            return $response;
        }

        $scimSchemasCollection = (new CoreCollection($scimSchemas))->toSCIM(false);

        $responseBody = json_encode($scimSchemasCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');

        return $response;
    }
}

<?php

namespace Opf\Controllers\ServiceProviders;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Opf\Models\SCIM\Standard\Service\CoreResourceType;
use Opf\Models\SCIM\Standard\Service\CoreSchemaExtension;
use Opf\Util\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

// TODO: Refactor this action class at some point to properly deliver configurable ResourceType entities
final class ListResourceTypesAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET ResourceTypes");

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $scimResourceTypes = Util::getResourceTypes($baseUrl);
        $scimResourceTypeCollection = (new CoreCollection($scimResourceTypes))->toSCIM(false);

        $responseBody = json_encode($scimResourceTypeCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

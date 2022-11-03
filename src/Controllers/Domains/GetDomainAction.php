<?php

namespace Opf\Controllers\Domains;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class GetDomainAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('DomainsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Domain");
        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);
        $domain = $this->repository->getOneById($id);
        if (!isset($domain) || empty($domain)) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }

        $scimDomain = $domain->toSCIM(false, $baseUrl);

        $responseBody = json_encode($scimDomain, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

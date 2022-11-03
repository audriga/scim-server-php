<?php

namespace Opf\Controllers\Domains;

use Exception;
use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class UpdateDomainAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('DomainsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("UPDATE Domain");
        $this->logger->info($request->getBody());

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);

        // Try to find a domain with the supplied ID
        // and if it doesn't exist, return a 404
        $domain = $this->repository->getOneById($id);
        if (!isset($domain) || empty($domain)) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }

        try {
            $domain = $this->repository->update($id, $request->getParsedBody());
            if (isset($domain) && !empty($domain)) {
                $scimDomain = $domain->toSCIM(false, $baseUrl);

                $responseBody = json_encode($scimDomain, JSON_UNESCAPED_SLASHES);
                $this->logger->info($responseBody);
                $response = new Response($status = 200);
                $response->getBody()->write($responseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            } else {
                $this->logger->error("Error updating domain");
                $errorResponseBody = json_encode(
                    ["Errors" => ["decription" => "Error updating domain", "code" => 400]]
                );
                $response = new Response($status = 400);
                $response->getBody()->write($errorResponseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            }
        } catch (Exception $e) {
            $this->logger->error("Error updating domain: " . $e->getMessage());
            $errorResponseBody = json_encode(["Errors" => ["description" => $e->getMessage(), "code" => 400]]);
            $response = new Response($status = 400);
            $response->getBody()->write($errorResponseBody);
            $response = $response->withHeader('Content-Type', 'application/scim+json');
            return $response;
        }
    }
}

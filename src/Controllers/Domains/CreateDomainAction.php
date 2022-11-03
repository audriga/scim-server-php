<?php

namespace Opf\Controllers\Domains;

use Exception;
use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class CreateDomainAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('DomainsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("CREATE Domain");
        $this->logger->info($request->getBody());

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        try {
            $domain = $this->repository->create($request->getParsedBody());

            if (isset($domain) && !empty($domain)) {
                $this->logger->info("Created domain / domain=" . $domain->getId());

                $scimDomain = $domain->toSCIM(false, $baseUrl);

                $responseBody = json_encode($scimDomain, JSON_UNESCAPED_SLASHES);
                $this->logger->info($responseBody);
                $response = new Response($status = 201);
                $response->getBody()->write($responseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            } else {
                $this->logger->error("Error creating domain");
                $errorResponseBody = json_encode(
                    ["Errors" => ["description" => "Error creating domain", "code" => 400]]
                );
                $response = new Response($status = 400);
                $response->getBody()->write($errorResponseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            }
        } catch (Exception $e) {
            $this->logger->error("Error creating domain: " . $e->getMessage());
            $errorResponseBody = json_encode(["Errors" => ["description" => $e->getMessage(), "code" => 400]]);
            $response = new Response($status = 400);
            $response->getBody()->write($errorResponseBody);
            $response = $response->withHeader('Content-Type', 'application/scim+json');
            return $response;
        }
    }
}

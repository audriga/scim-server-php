<?php

namespace Opf\Controllers\Groups;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class CreateGroupAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('GroupsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("CREATE Group");
        $this->logger->info($request->getBody());

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        try {
            $group = $this->repository->create($request->getParsedBody());

            if (isset($group) && !empty($group)) {
                $scimGroup = $group->toSCIM(false, $baseUrl);

                $responseBody = json_encode($scimGroup, JSON_UNESCAPED_SLASHES);
                $this->logger->info($responseBody);
                $response = new Response($status = 201);
                $response->getBody()->write($responseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            } else {
                $this->logger->error("Error creating group");
                $errorResponseBody = json_encode(
                    [
                        "Errors" => [
                            "description" => "Error creating group", "code" => 400
                        ]
                    ]
                );
                $response = new Response($status = 400);
                $response->getBody()->write($errorResponseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            }
        } catch (\Exception $e) {
            $this->logger->error("Error creating group: " . $e->getMessage());
            $errorResponseBody = json_encode(["Errors" => ["description" => $e->getMessage(), "code" => 400]]);
            $response = new Response($status = 400);
            $response->getBody()->write($errorResponseBody);
            $response = $response->withHeader('Content-Type', 'application/scim+json');
            return $response;
        }
    }
}

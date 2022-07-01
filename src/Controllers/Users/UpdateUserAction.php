<?php

namespace Opf\Controllers\Users;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class UpdateUserAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('UsersRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("UPDATE User");
        $this->logger->info($request->getBody());

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);

        // Try to find a user with the supplied ID
        // and if it doesn't exist, return a 404
        $user = $this->repository->getOneById($id);
        if (!isset($user) || empty($user)) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }

        try {
            $user = $this->repository->update($id, $request->getParsedBody());
            if (isset($user) && !empty($user)) {
                $scimUser = $user->toSCIM(false, $baseUrl);

                $responseBody = json_encode($scimUser, JSON_UNESCAPED_SLASHES);
                $this->logger->info($responseBody);
                $response = new Response($status = 200);
                $response->getBody()->write($responseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            } else {
                $this->logger->error("Error updating user");
                $errorResponseBody = json_encode(["Errors" => ["decription" => "Error updating user", "code" => 400]]);
                $response = new Response($status = 400);
                $response->getBody()->write($errorResponseBody);
                $response = $response->withHeader('Content-Type', 'application/scim+json');
                return $response;
            }
        } catch (\Exception $e) {
            $this->logger->error("Error updating user: " . $e->getMessage());
            $errorResponseBody = json_encode(["Errors" => ["description" => $e->getMessage(), "code" => 400]]);
            $response = new Response($status = 400);
            $response->getBody()->write($errorResponseBody);
            $response = $response->withHeader('Content-Type', 'application/scim+json');
            return $response;
        }
    }
}

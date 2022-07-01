<?php

namespace Opf\Controllers\Users;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class GetUserAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('UsersRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET User");
        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);
        $user = $this->repository->getOneById($id);
        if (!isset($user) || empty($user)) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }

        $scimUser = $user->toSCIM(false, $baseUrl);

        $responseBody = json_encode($scimUser, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

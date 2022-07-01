<?php

namespace Opf\Controllers\Groups;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class GetGroupAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('GroupsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Group");
        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);
        $group = $this->repository->getOneById($id);
        if (!isset($group) || empty($group)) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }

        $scimGroup = $group->toSCIM(false, $baseUrl);

        $responseBody = json_encode($scimGroup, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

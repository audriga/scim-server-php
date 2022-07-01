<?php

namespace Opf\Controllers\Groups;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListGroupsAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('GroupsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Groups");

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $groups = [];
        $groups = $this->repository->getAll();

        $scimGroups = [];
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $scimGroups[] = $group->toSCIM(false, $baseUrl);
            }
        }
        $scimGroupCollection = (new CoreCollection($scimGroups))->toSCIM(false);

        $responseBody = json_encode($scimGroupCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

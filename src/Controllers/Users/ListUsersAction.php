<?php

namespace Opf\Controllers\Users;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Opf\Util\Util;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListUsersAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('UsersRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Users");
        $filter = '';
        if (!empty($request->getQueryParams()['filter'])) {
            $this->logger->info("Filter --> " . $request->getQueryParams()['filter']);
            $filter = $request->getQueryParams()['filter'];
        }
        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $userName = null;
        $users = [];
        $users = $this->repository->getAll($filter);

        $scimUsers = [];
        if (!empty($users)) {
            foreach ($users as $user) {
                $scimUsers[] = $user->toSCIM(false, $baseUrl);
            }
        }
        $scimUserCollection = (new CoreCollection($scimUsers))->toSCIM(false);

        $responseBody = json_encode($scimUserCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

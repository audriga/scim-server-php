<?php

namespace Opf\Controllers\Groups;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteGroupAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('GroupsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("DELETE Group");
        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);
        $deleteRes = $this->repository->delete($id);
        if (!$deleteRes) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }
        $this->logger->info("Group deleted");

        return $response->withStatus(200);
    }
}

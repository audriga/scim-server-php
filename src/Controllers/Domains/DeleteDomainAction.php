<?php

namespace Opf\Controllers\Domains;

use Opf\Controllers\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteDomainAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('DomainsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("DELETE Domain");
        $id = $request->getAttribute('id');
        $this->logger->info("ID: " . $id);
        $deleteRes = $this->repository->delete($id);
        if (!$deleteRes) {
            $this->logger->info("Not found");
            return $response->withStatus(404);
        }
        $this->logger->info("Domain deleted");

        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response->withStatus(204);
    }
}

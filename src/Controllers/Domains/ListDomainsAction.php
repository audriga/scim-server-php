<?php

namespace Opf\Controllers\Domains;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListDomainsAction extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get('DomainsRepository');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Domains");
        $filter = '';
        if (!empty($request->getQueryParams()['filter'])) {
            $this->logger->info("Filter --> " . $request->getQueryParams()['filter']);
            $filter = $request->getQueryParams()['filter'];
        }
        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        $domains = [];
        $domains = $this->repository->getAll($filter);

        $scimDomains = [];
        if (!empty($domains)) {
            foreach ($domains as $domain) {
                $scimDomains[] = $domain->toSCIM(false, $baseUrl);
            }
        }
        $scimDomainCollection = (new CoreCollection($scimDomains))->toSCIM(false);

        $responseBody = json_encode($scimDomainCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

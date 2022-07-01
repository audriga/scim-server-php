<?php

namespace Opf\Controllers\ServiceProviders;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Opf\Models\SCIM\Standard\Service\CoreResourceType;
use Opf\Models\SCIM\Standard\Service\CoreSchemaExtension;
use Opf\Util\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

// TODO: Refactor this action class at some point to properly deliver configurable ResourceType entities
final class ListResourceTypesAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET ResourceTypes");

        $uri = $request->getUri();
        $baseUrl = sprintf('%s://%s', $uri->getScheme(), $uri->getAuthority() . $this->basePath);

        // Check which resource types are supported via the config file and in this method further down below
        // make sure to only return those that are indeed supported
        $config = Util::getConfigFile();
        $supportedResourceTypes = $config['supportedResourceTypes'];

        $scimResourceTypes = [];

        if (in_array('User', $supportedResourceTypes)) {
            $userResourceType = new CoreResourceType();
            $userResourceType->setId("User");
            $userResourceType->setName("User");
            $userResourceType->setEndpoint("/Users");
            $userResourceType->setDescription("User Account");
            $userResourceType->setSchema(Util::USER_SCHEMA);

            if (in_array('EnterpriseUser', $supportedResourceTypes)) {
                $enterpriseUserSchemaExtension = new CoreSchemaExtension();
                $enterpriseUserSchemaExtension->setSchema(Util::ENTERPRISE_USER_SCHEMA);
                $enterpriseUserSchemaExtension->setRequired(true);

                $userResourceType->setSchemaExtensions(array($enterpriseUserSchemaExtension));
            }
            if (in_array('ProvisioningUser', $supportedResourceTypes)) {
                $provisioningUserSchemaExtension = new CoreSchemaExtension();
                $provisioningUserSchemaExtension->setSchema(Util::PROVISIONING_USER_SCHEMA);
                $provisioningUserSchemaExtension->setRequired(true);

                $userResourceType->setSchemaExtensions(array($provisioningUserSchemaExtension));
            }

            $scimResourceTypes[] = $userResourceType->toSCIM(false, $baseUrl);
        }

        if (in_array('Group', $supportedResourceTypes)) {
            $groupResourceType = new CoreResourceType();
            $groupResourceType->setId("Group");
            $groupResourceType->setName("Group");
            $groupResourceType->setEndpoint("/Groups");
            $groupResourceType->setDescription("Group");
            $groupResourceType->setSchema("urn:ietf:params:scim:schemas:core:2.0:Group");
            $groupResourceType->setSchemaExtensions([]);

            $scimResourceTypes[] = $groupResourceType->toSCIM(false, $baseUrl);
        }

        $scimResourceTypeCollection = (new CoreCollection($scimResourceTypes))->toSCIM(false);

        $responseBody = json_encode($scimResourceTypeCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');
        return $response;
    }
}

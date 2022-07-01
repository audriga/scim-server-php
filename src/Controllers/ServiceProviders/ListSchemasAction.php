<?php

namespace Opf\Controllers\ServiceProviders;

use Opf\Controllers\Controller;
use Opf\Models\SCIM\Standard\CoreCollection;
use Opf\Util\Util;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

final class ListSchemasAction extends Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info("GET Schemas");

        $config = Util::getConfigFile();
        $supportedSchemas = $config['supportedResourceTypes'];
        $mandatorySchemas = ['Schema', 'ResourceType'];

        $scimSchemas = [];

        // We store the schemas that the SCIM server supports in separate JSON files
        // That's why we try to read them here and add them to $scimSchemas, which
        // in turn is then put into the SCIM response body
        $pathToSchemasDir = dirname(__DIR__, 3) . '/config/Schema';
        $schemaFiles = scandir($pathToSchemasDir, SCANDIR_SORT_NONE);

        // If scandir() failed (i.e., it returned false), then return 404 (is this spec-compliant?)
        if ($schemaFiles === false) {
            $this->logger->info("No Schemas found");
            $response = new Response($status = 404);
            $response = $response->withHeader('Content-Type', 'application/scim+json');

            return $response;
        }

        foreach ($schemaFiles as $schemaFile) {
            if (!in_array($schemaFile, array('.', '..'))) {
                $scimSchemaJsonDecoded = json_decode(file_get_contents($pathToSchemasDir . '/' . $schemaFile), true);

                // Only return schemas that are either mandatory (like the 'Schema' and 'ResourceType' ones)
                // or supported by the server
                if (in_array($scimSchemaJsonDecoded['name'], array_merge($supportedSchemas, $mandatorySchemas))) {
                    $scimSchemas[] = $scimSchemaJsonDecoded;
                }
            }
        }

        $scimSchemasCollection = (new CoreCollection($scimSchemas))->toSCIM(false);

        $responseBody = json_encode($scimSchemasCollection, JSON_UNESCAPED_SLASHES);
        $this->logger->info($responseBody);
        $response = new Response($status = 200);
        $response->getBody()->write($responseBody);
        $response = $response->withHeader('Content-Type', 'application/scim+json');

        return $response;
    }
}

<?php

use Opf\Controllers\Domains\CreateDomainAction;
use Opf\Controllers\Domains\DeleteDomainAction;
use Opf\Controllers\Domains\GetDomainAction;
use Opf\Controllers\Domains\ListDomainsAction;
use Opf\Controllers\Domains\UpdateDomainAction;
use Opf\Controllers\Groups\CreateGroupAction;
use Opf\Controllers\Groups\DeleteGroupAction;
use Opf\Controllers\Groups\GetGroupAction;
use Opf\Controllers\Groups\ListGroupsAction;
use Opf\Controllers\Groups\UpdateGroupAction;
use Opf\Controllers\ServiceProviders\ListResourceTypesAction;
use Opf\Controllers\ServiceProviders\ListSchemasAction;
use Opf\Controllers\ServiceProviders\ListServiceProviderConfigurationsAction;
use Opf\Controllers\Users\CreateUserAction;
use Opf\Controllers\Users\DeleteUserAction;
use Opf\Controllers\Users\GetUserAction;
use Opf\Controllers\Users\ListUsersAction;
use Opf\Controllers\Users\UpdateUserAction;
use Opf\Util\Util;
use Slim\App;

return function (App $app) {
    // We need this information so that we can only expose endpoints
    // for resource types that the deployment actually supports
    $config = Util::getConfigFile();
    $supportedResourceTypes = $config['supportedResourceTypes'];

    // Users routes
    if (in_array('User', $supportedResourceTypes)) {
        $app->get('/Users', ListUsersAction::class)->setName('users.list');
        $app->get('/Users/{id}', GetUserAction::class)->setName('users.get');
        $app->post('/Users', CreateUserAction::class)->setName('users.create');
        $app->put('/Users/{id}', UpdateUserAction::class)->setName('users.update');
        $app->delete('/Users/{id}', DeleteUserAction::class)->setName('users.delete');
    }

    // Group routes
    if (in_array('Group', $supportedResourceTypes)) {
        $app->get('/Groups', ListGroupsAction::class)->setName('groups.list');
        $app->get('/Groups/{id}', GetGroupAction::class)->setName('groups.get');
        $app->post('/Groups', CreateGroupAction::class)->setName('groups.create');
        $app->put('/Groups/{id}', UpdateGroupAction::class)->setName('groups.update');
        $app->delete('/Groups/{id}', DeleteGroupAction::class)->setName('groups.delete');
    }

    if (in_array('Domain', $supportedResourceTypes)) {
        $app->get('/Domains', ListDomainsAction::class)->setName('domains.list');
        $app->get('/Domains/{id}', GetDomainAction::class)->setName('domains.get');
        $app->post('/Domains', CreateDomainAction::class)->setName('domains.create');
        $app->put('/Domains/{id}', UpdateDomainAction::class)->setName('domains.update');
        $app->delete('/Domains/{id}', DeleteDomainAction::class)->setName('domains.delete');
    }

    // ServiceProvider routes
    $app->get('/ResourceTypes', ListResourceTypesAction::class)->setName('resourceTypes.list');
    $app->get('/Schemas', ListSchemasAction::class)->setName('schemas.list');
    $app->get(
        '/ServiceProviderConfig',
        ListServiceProviderConfigurationsAction::class
    )->setName('serviceProviderConfigs.list');
};

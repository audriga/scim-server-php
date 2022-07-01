<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Opf\Adapters\Groups\MockGroupAdapter;
use Opf\Adapters\Users\MockUserAdapter;
use Opf\Controllers\Controller;
use Opf\DataAccess\Groups\MockGroupDataAccess;
use Opf\DataAccess\Users\MockUserDataAccess;
use Opf\Repositories\Groups\MockGroupsRepository;
use Opf\Repositories\Users\MockUsersRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Middleware\JwtAuthentication;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Repositories
        'UsersRepository' => function (ContainerInterface $c) {
            return new MockUsersRepository($c);
        },

        'GroupsRepository' => function (ContainerInterface $c) {
            return new MockGroupsRepository($c);
        },

        // Data access classes
        'UsersDataAccess' => function () {
            return new MockUserDataAccess();
        },

        'GroupsDataAccess' => function () {
            return new MockGroupDataAccess();
        },

        // Adapters
        'UsersAdapter' => function () {
            return new MockUserAdapter();
        },

        'GroupsAdapter' => function () {
            return new MockGroupAdapter();
        }
    ]);
};

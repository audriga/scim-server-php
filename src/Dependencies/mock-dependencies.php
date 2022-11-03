<?php

declare(strict_types=1);

use Opf\Adapters\Groups\MockGroupAdapter;
use Opf\Adapters\Users\MockUserAdapter;
use Opf\DataAccess\Groups\MockGroupDataAccess;
use Opf\DataAccess\Users\MockUserDataAccess;
use Opf\Middleware\SimpleAuthMiddleware;
use Opf\Repositories\Groups\MockGroupsRepository;
use Opf\Repositories\Users\MockUsersRepository;
use Opf\Util\Authentication\SimpleBearerAuthenticator;
use Psr\Container\ContainerInterface;

return [
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
    },

    // Auth middleware
    'AuthMiddleware' => function (ContainerInterface $c) {
        return new SimpleAuthMiddleware($c);
    },

    // Authenticators (used by SimpleAuthMiddleware)
    'BearerAuthenticator' => function (ContainerInterface $c) {
        return new SimpleBearerAuthenticator($c);
    }
];

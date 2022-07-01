<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Opf\Adapters\Users\PfaUserAdapter;
use Opf\DataAccess\Users\PfaUserDataAccess;
use Opf\Repositories\Users\PfaUsersRepository;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Repositories
        'UsersRepository' => function (ContainerInterface $c) {
            return new PfaUsersRepository($c);
        },

        // Data access classes
        'UsersDataAccess' => function () {
            return new PfaUserDataAccess();
        },

        // Adapters
        'UsersAdapter' => function () {
            return new PfaUserAdapter();
        }
    ]);
};

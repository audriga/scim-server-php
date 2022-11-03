<?php

return [
    'isInProduction' => false, // Set to true when deploying in production
    'basePath' => null, // If you want to specify a base path for the Slim app, add it here (e.g., '/test/scim')
    'supportedResourceTypes' => ['User', 'Group'], // Specify all the supported SCIM ResourceTypes by their names here

    // SQLite DB settings
    'db' => [
        'driver' => 'sqlite', // Type of DB
        'databaseFile' => 'db/scim-mock.sqlite' // DB name
    ],

    // Monolog settings
    'logger' => [
        'name' => 'scim-opf',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../../logs/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],

    // Bearer token settings
    'jwt' => [
        'secret' => 'secret'
    ]
];

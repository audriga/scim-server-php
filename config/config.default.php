<?php

return [
    'isInProduction' => false, // Set to true when deploying in production
    'basePath' => '', // If you want to specify a base path for the Slim app, add it here (e.g., '/test/scim')
    'supportedResourceTypes' => ['User', 'Group'], // Specify all the supported SCIM ResourceTypes by their names here

    // SQLite DB settings
    'db' => [
        'driver' => 'sqlite', // Type of DB
        'databaseFile' => 'db/scim-mock.sqlite' // DB name
    ],

    // MySQL DB settings
    //'db' => [
    //    'driver' => 'mysql', // Type of DB
    //    'host' => 'localhost', // DB host
    //    'port' => '3306', // Port on DB host
    //    'database' => 'db_name', // DB name
    //    'user' => 'db_user', // DB user
    //    'password' => 'db_password' // DB user's password
    //],

    // Monolog settings
    'logger' => [
        'name' => 'scim-opf',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],

    // Bearer token settings
    'jwt' => [
        'secret' => 'secret'
    ]
];

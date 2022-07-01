<?php

use Opf\Util\Util;
use Slim\App;

return static function (App $app) {
    $config = Util::getConfigFile();
    $dbSettings = $config['db'];

    // Boot eloquent
    $capsule = new Illuminate\Database\Capsule\Manager();
    $capsule->addConnection($dbSettings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
};

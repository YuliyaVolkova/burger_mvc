<?php

require_once '../app/Core/Bootstrap.php';

use App\Core\Bootstrap;
use App\Core\Router;

$init = new Bootstrap();
try {
    $router = new Router();
} catch (Exception $e) {
    require APPLICATION_PATH . 'Views/Errors/404.php';
}

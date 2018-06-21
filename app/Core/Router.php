<?php

namespace App\Core;

use App\Controllers\MainController;
use App\Controllers\Admin;
use App\Controllers\Order;

class Router
{
    public $controller;

    protected function rootGet()
    {
        $this->controller = new MainController();
        $this->controller->mainPage();
    }

    protected function rootPost()
    {
        $this->controller = new Order();
    }

    protected function adminGet()
    {
        $this->controller = new Admin();
    }

    public function __construct()
    {
        $this->controller = explode('/', $_SERVER['REQUEST_URI'])[1];
        $reqMethod = $_SERVER['REQUEST_METHOD'];

        if (empty($this->controller) && $reqMethod == 'GET') {
            $this->rootGet();
        } elseif (empty($this->controller) && !empty($_POST)) {
            $this->rootPost();
        } elseif ($this->controller === 'admin' && $reqMethod == 'GET') {
            $this->adminGet();
        } else {
            header('Location: /');
        }
    }
}

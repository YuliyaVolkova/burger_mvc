<?php

namespace App\Controllers;

use App\Views\View;

class MainController
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function mainPage()
    {
        $this->view->render('index', []);
    }
}

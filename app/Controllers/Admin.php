<?php

namespace App\Controllers;

use App\Models\Users;
use App\Models\Orders;

class Admin extends MainController
{
    public function __construct()
    {
        parent::__construct();
        $users = new Users;
        $orders = new Orders;
        $data['users'] = $users->getAllUsers();
        $data['orders'] = $orders->getAllOrders();
        $this->view->twigLoad('admin', $data);  //  рендерим Twig template
        //$this->view->render('admin', $data); //   при рендеринге в PHP template
    }
}

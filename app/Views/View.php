<?php

namespace App\Views;

class View
{
    public function render(string $filename, array $data)
    {
        extract($data);
        require_once __DIR__ . "/" . $filename . ".php";
    }
}

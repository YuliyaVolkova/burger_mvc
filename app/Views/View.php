<?php

namespace App\Views;

use Twig_Environment;

class View
{
    protected $loader;
    protected $twig;

    /**
     *  Метод для рендеринга  данных в PHP templates
     * @param string $filename
     * @param array $data
     */
    public function render(string $filename, array $data)
    {
        extract($data);
        require_once __DIR__ . "/" . $filename . ".php";
    }

    /**
     * Метод для рендеринга TWIG templates
     * @param String $filename
     * @param array $data
     */

    public function twigLoad(String $filename, array $data)
    {
        echo $this->twig->render($filename . ".html", $data);
    }

    /**
     * View constructor.
     *  При инициализации будем рендерить в Twig template
     */
    public function __construct($data = [])
    {
        $this->loader = new \Twig_Loader_Filesystem(APPLICATION_PATH.'Views');
        $this->twig = new Twig_Environment($this->loader);
    }
}

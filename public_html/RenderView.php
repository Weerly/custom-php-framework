<?php

class RenderView
{
    public static function setTemplate($page, $array)
    {
        require_once __DIR__ . '/../Twig/Autoloader.php';
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('View/templates');
        $twig = new Twig_Environment($loader);
        return ($twig->render($page, $array));
    }
}
<?php
namespace Core;

class Controller extends Route
{
    public function __construct($cookies, $request, $post, $get)
    {
        parent::__construct( $cookies);
        $this->callCantroller($request);
    }

    private function callCantroller($request)
    {
        $object = $this->checkIncomingRoute($request);

        if (!empty($object)) {
            $_controller = str_ireplace(" ", "", "Controllers\\" . $object[0] . "Controller");
            $controllerName = str_ireplace("\\", "/", $_controller);

            require_once(__DIR__ . "/../" . $controllerName . '.php');
            $controller = new $_controller($this->session);
            $action = ucfirst($object[1] . "Action");
            $action = trim($action);
            $arg = $object[2] ?? null;

            echo $controller->$action($arg);
        } else {

            echo \RenderView::setTemplate("404.html.twig", ['auth' => false]);
        }
    }

    private function checkAuthData()
    {

    }
}
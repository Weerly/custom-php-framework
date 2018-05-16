<?php
namespace Core;
class Route extends Session
{
    public function __construct($cookies)
    {
        parent::__construct($cookies);
    }

    protected function checkIncomingRoute($request)
    {
        include ("router.php");

        $routes = getRoutes();

        $j = 0;
        $requestUrl = $_SERVER["REQUEST_URI"];
        $controllerObj = null;
        do {
            $url = $routes[$j]["url"];
            if ($url === $requestUrl) {
                $controllerObj = explode("=>", $routes[$j]["action"]);

                if ($routes[$j]["method"] == 'post') {
                    $controllerObj[] = $request;
                }
            } else if (preg_match_all('%^\/.*\{(.*)\}.*$%mi',$url, $matches )) {
                if (count($matches)) {
                    $var = $request[$matches[1][0]] ?? null;

                    if ($var !== null) {
                        $controllerObj = explode("=>", $routes[$j]["action"]);
                        $controllerObj[] = $var;
                    }
                }
            }

            $j++;
        } while($j < count($routes));

        if (empty($controllerObj)) {

        }

        return $controllerObj;

    }
}
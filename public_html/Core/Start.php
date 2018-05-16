<?php
namespace Core;
class Start extends Controller
{
    public function __construct($cookies, $request, $post, $get)
    {
        parent::__construct($cookies, $request, $post, $get);
    }

    public static function letStart()
    {
        $cookies = $_COOKIE;
        $request = $_REQUEST;
        $post    = $_POST;
        $get     = $_GET;

        $Start = new Start( $cookies, $request, $post, $get);
    }
}
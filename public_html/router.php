<?php
function getRoutes()
{
    return $routes = [
        [
            "name" => "main",
            "url" => "/",
            "action" => "Default => index",
            "method" => "get"
        ], [
            "name" => "login",
            "url" => "/login",
            "action" => "User => login",
            "method" => "post"
        ], [
            "name" => "activate",
            "url" => "/activate/{code}",
            "action" => "User => activate",
            "method" => "get"
        ], [
            "name" => "register",
            "url" => "/register",
            "action" => "User => register",
            "method" => "post",
        ], [
            "name" => "saveEdit",
            "url" => "/edit",
            "action" => "User => edit",
            "method" => "post",
        ], [
            "name" => "sign In",
            "url" => "/signin",
            "action" => "User => signin",
            "method" => "get"
        ], [
            "name" => "sign up",
            "url" => "/signup",
            "action" => "User => signup",
            "method" => ""
        ], [
            "name" => "logout",
            "url" => "/logout",
            "action" => "User => logout",
            "method" => "get"
        ],
    ];
}
<?php
spl_autoload_register(function ($class_name) {
    $name = str_ireplace("\\","/", $class_name . '.php');
    $file = __DIR__ . '/' . $name;

    if (is_file($file)) {
        include_once $file;
    }
});

spl_autoload_register(function ($class_name) {
    $name = str_ireplace("\\","/", $class_name . '.php');
    $file = __DIR__ . "/../Twig/" . $name;

    if (is_file($file)) {
        include_once $file;
    }
});
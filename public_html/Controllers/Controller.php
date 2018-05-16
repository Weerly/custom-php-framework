<?php
namespace Controllers;

abstract class Controller
{
    protected $session;
    public function __construct($session)
    {
        $this->session = $session;
    }
}
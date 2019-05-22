<?php
namespace Tests\Lib;

class ApiSimulator extends \LINE\Api
{

    protected
        $handler_stack,
        $container,
        $history = [];

    public function __construct($options)
    { }

    public function get($url, $param = [], $options = [], $auth_type = "client")
    { }

    public function post($url, $param = [], $options = [], $auth_type = "client")
    { }
}

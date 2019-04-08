<?php
namespace LINE\Interfaces;

interface ApiInterface {
    public function getChannelID():string;

    public function setChannelID(string $id):void;

    public function getChannelSecret():string;

    public function setChannelSecret(string $id):void;

    public function post($url, $param = [], $options = [], $auth_type="client");

    public function get($url, $param = [], $options = [], $auth_type="client");
}
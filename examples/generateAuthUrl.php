<?php
require_once('../vendor/autoload.php');

$api = new LINE\Api([
    "channel_secret" => '00112233445566778899aabbccddeeff',
    "channel_id" => '0123456789'
]);

$login = new LINE\Login($api);

echo $login->get_authorization_url("http://localhost:3000/akun/oauth/line", [
    LINE\Login::SCOPE_OPENID,
    LINE\Login::SCOPE_PROFILE
]) . PHP_EOL;

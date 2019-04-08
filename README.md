# line-sdk-php
Line SDK Client for PHP. Used for simple Login API or Social API.

**STILL UNSTABLE, I'M STILL TESTING THESE APIs**. Do at your own risk.

# About the LINE API
See the official API documentation for more information.

English: [developers.line.me/en/docs](https://developers.line.me/en/docs)

Japanese: [developers.line.me/ja/docs](https://developers.line.me/ja/docs)

# Getting Started
## Installation
Use [Composer](https://getcomposer.org) to pull this library.
```shell
$ composer require chez14/line-sdk
```

## Create a Channel 
1. Create a LINE Channel, follow [this step](https://developers.line.biz/en/docs/line-login/getting-started/) to get started. Or simply use the one you have.
2. Go to [LINE Developer Console](https://developers.line.biz/console/), grab the Channel Secret and Channel ID.

## Creating API Instance

Create a new object from `LINE\Api`. Don't forget to supply both `client_id` and `client_secret` to the parameter.

```php
$api = new LINE\Api([
    "channel_secret" => '00112233445566778899aabbccddeeff',
    "channel_id" => '0123456789'
]);
```

After that, you can use your get your auth code, and do some OAuth job.
```php
$login = new LINE\Login($api);

echo $login->get_authorization_url("http://localhost/callback.php", [
    LINE\Login::SCOPE_OPENID,
    LINE\Login::SCOPE_PROFILE
]) . PHP_EOL;

// will produce:
// https://access.line.me/oauth2/v2.1/authorize?response_type=code&redirect_uri=http%3A%2F%2Flocalhost%3A3000%2Fakun%2Foauth%2Fline&client_id=0123456789&scope=openid+profile&state=1OaoBjV9US69fzOx&prompt=consent

// OR
echo $login->get_authorization_url("http://localhost/callback.php", [
    "openid","profile"
]) . PHP_EOL;


// -------------- on the callback.php:
$token = $login->parse_from_request();

$api->setToken($token);

$social = new LINE\Social($api);
var_dump($social->getProfile()); //echo the profiles
```

Check our documentation page to get more info about this library classes, and
see our [`examples` folder](examples/) to get more examples.

## Go beyond, and plus ultra!
Check this API Documentation (tbd), and check LINE's corresponding documentations.

Have fun!

![All Might - Thumbs Up](https://thumbs.gfycat.com/GrandScratchyCicada-small.gif)

# License
[MIT](LICENSE).

# Bug Report
Please, if you have any feedback or bug report for this lib, submit it to
the issue tracker. If you concern about the security and privacy, you can
PGP-encrypt it using [Chez14's Keybase](https://keybase.io/encrypt#chez14),
and submit it as an issue.
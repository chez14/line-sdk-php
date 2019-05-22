<?php
namespace LINE;

/**
 * Token management class.
 * 
 * @see https://developers.line.biz/en/docs/social-api/managing-access-tokens/
 */
class Token
{
    protected
        $api,
        $id_token,
        $refresh_token;

    public static
        $base_url = "oauth2/v2.1/";

    public function __construct(Interfaces\ApiInterface $api, string $id_token = null, string $refresh_token = null)
    {
        $this->api = $api;
        $this->id_token = $id_token;
        $this->refresh_token = $refresh_token;
    }

    public function getAccessToken()
    {
        if (!$this->id_token) {
            throw new \BadMethodCallException("id_token is not supplied.");
        }
        return $this->id_token;
    }

    public function setAccessToken(string $id)
    {
        $this->id_token = $id;
    }

    public function getRefreshToken()
    {
        if (!$this->id_token) {
            throw new \BadMethodCallException("refresh_token is not supplied.");
        }
        return $this->id_token;
    }

    public function setRefreshToken()
    {
        $this->id_token = $id;
    }

    /**
     * Revoke this access token.
     * 
     * @return bool true if success full, otherwise we'll give you Exception instead.
     */
    public function revoke(): bool
    {
        try {
            $response = $this->api->post(self::$base_url . 'revoke', [
                'access_token' => $this->getAccessToken()
            ]);
        } catch (\Exception $e) {
            //TODO: do some validation
            return false;
        }
        return true;
    }

    /**
     * Validate the token using signature validation.
     * 
     * @see https://developers.line.biz/en/docs/line-login/web/integrate-line-login/#spy-getting-an-access-token
     * @param $token Token string that we want to validate.
     * @param $nonce Nonce parameter validation. If not supplied/null, this will be ignored.
     */
    protected function validate_token($token, string $nonce = null)
    {
        $token = (new \Lcobucci\JWT\Parser())->parse((string)$token);

        $tokenVerifier = new \Lcobucci\JWT\ValidationData();
        $tokenVerifier->setIssuer('https://access.line.me');
        $tokenVerifier->setAudience($this->api->getChannelID());
        if (!$token->validate($tokenVerifier)) {
            return false;
        }

        if ($nonce && $token->getClain("nonce") != $nonce) {
            return false;
        }

        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        return  $token->verify($signer, $this->api->getChannelSecret());
    }

    /**
     * Get claims of the token, such as: 
     * name, picture, email, exp (expiration), sub (user id from LINE).
     * 
     * @see https://developers.line.biz/en/docs/line-login/web/integrate-line-login/#payload
     * @return array
     */
    public function getTokenInfo()
    {
        $token = (new \Lcobucci\JWT\Parser())->parse($this->getAccessToken());

        return $token->getClaims();
    }

    /**
     * Verify this token
     * 
     * @return bool whether the Token is still valid and verified.
     */
    public function verify(): bool
    {
        try {
            $response = $this->api->post(self::$base_url . 'verify', [
                'access_token' => $this->getAccessToken()
            ]);
        } catch (\Exception $e) {
            //TODO: do some validation
            return false;
        }
        return true;
    }

    /**
     * Refresh this token. New token will be returned as new object.
     * 
     * @return Token object with new Refresh token and ID Token.
     */
    public function refresh(): self
    {
        $response = $this->api->post(self::$base_url . 'token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->getRefreshToken()
        ]);
        $data = \json_decode($response->getBody(), true);
        $token = new self($this->api, $data['access_token'], $data['refresh_token']);
        return $token;
    }

    /**
     * Get a token from refresh token.
     * 
     * This will automatically get new token from refresh token.
     * 
     * @return Token
     */
    public static function fromRefreshToken(string $refresh_token, Api $api): self
    {
        $temp = new self($api, null, $refresh_token);
        return $temp->refresh();
    }

    /**
     * Create new token instance from just only a id_token.
     * 
     * This will return token with no refresh token, and will raise BadMethodCall Exception if you try to
     * refresh it.
     * 
     * @return Token with no refresh token.
     */
    public static function fromIDToken(string $id_token, Api $api)
    {
        $temp = new self($api, $id_token, null);
        return $temp;
    }

    /**
     * Get token from auth code from Login handler.
     * 
     * @return Token
     */
    public static function fromAuthCode(string $auth_code, String $redir_uri, Api $api)
    {
        $response = $api->post(self::$base_url . 'token', [
            'grant_type' => 'authorization_code',
            'code' => $auth_code,
            'redirect_uri' => $redir_uri
        ]);
        $data = \json_decode($response->getBody(), true);
        $token = new self($api, $data['access_token'], $data['refresh_token']);
        return $token;
    }
}

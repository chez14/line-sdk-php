<?php
namespace LINE;
/**
 * Token management class.
 * 
 * @see https://developers.line.biz/en/docs/social-api/managing-access-tokens/
 */
class Token {
    protected
        $api,
        $id_token,
        $refresh_token;
    
    public function __construct($api, $id_token, $refresh_token) {
        $this->api = $api;
        $this->id_token = $id_token;
        $this->refresh_token = $refresh_token;
    }

    public function getAccessToken() {
        if(!$this->id_token) {
            throw new \BadMethodCallException("id_token is not supplied.");
        }
        return $this->id_token;
    }

    public function setAccessToken(string $id) {
        $this->id_token = $id;
    }

    public function getRefreshToken() {
        if(!$this->id_token) {
            throw new \BadMethodCallException("refresh_token is not supplied.");
        }
        return $this->id_token;
    }

    public function setRefreshToken() {
        $this->id_token = $id;
    }

    /**
     * @see https://developers.line.biz/en/docs/line-login/web/integrate-line-login/#spy-getting-an-access-token
     */
    protected function validate_token($token, string $nonce = null) {
        $token = (new Parser())->parse((string) $token);
        
        $tokenVerifier = new \Lcobucci\JWT\ValidationData();
        $tokenVerifier->setIssuer('https://access.line.me');
        $tokenVerifier->setAudience($this->api->getChannelID());
        if(!$token->validate($tokenVerifier)) {
            return false;
        }

        if($nonce && $token->getClain("nonce") != $nonce) {
            return false;
        }

        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        return  $token->verify($signer, $this->api->getChannelSecret());
    }

    /**
     * Verify this token
     * 
     */
    public function verify():bool {
        try {
            $response = $this->api->post('verify',[
                'access_token' => $this->getAccessToken()
            ]);
        } catch (\Exception $e) {
            //TODO: do some validation
            return false;
        }
        return true;
    }

    /**
     * Refresh this token
     */
    public function refresh():self {
        $response = $this->api->post('token',[
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->getRefreshToken(),
            'client_id' => $this->api->getChannelID(),
            'client_secret' => $this->api->getClientSecret()
        ]);
        $data = \json_decode($response->getBody(), true);
        $token = new self($this->api, $data['access_token'], $data['refresh_token']);
        return $token;
    }

    /**
     * Get a token from refresh token.
     */
    public static function fromRefreshToken(string $refresh_token, Api $api):self {
        $temp = new self($api, null, $refresh_token);
        return $temp->refresh();
    }
    
    /**
     * Create new token instance from just only a id_token.
     */
    public static function fromIDToken(string $id_token, Api $api) {
        $temp = new self($api, $id_token, null);
        return $temp;
    }
}
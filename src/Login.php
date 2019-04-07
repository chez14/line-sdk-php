<?php
namespace LINE;

class Login {
    public const
        SCOPE_PROFILE = "profile",
        SCOPE_OPENID = "openid",
        SCOPE_EMAIL = "email";
    
    protected
        $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    /**
     * Produce an authorization URL
     * 
     * @return string URl encoded string of that Auth link
     */
    public function get_authorization_url(string $redirect_uri, array $scope, string $state, string $nonce = null, string $prompt="consent", int $max_age=null, string $bot_prompt = null) {
        if(count($scope) <= 1) {
            throw new \InvalidArgumentException("Scope must have at least 1 scope.");
        }

        $request = [
            "redirect_url" => $redirect_uri,
            "client_id" => $this->api->getChannelID(),
            "scope" => \implode(" ",$scope),
            "state" => $state
        ];

        if($nonce) {
            $request['nonce'] = $nonce;
        }

        if($prompt) {
            $request['prompt'] = $prompt;
        }

        if($max_age) {
            $request['max_age'] = $max_age;
        }

        if($bot_prompt) {
            if(!\in_array($bot_prompt, ["normal", "aggressive"])) {
                throw new \InvalidArgumentException("Unknown value supplied to Bot Prompt. It must be either `normal` or `aggressive`, got " . $bot_prompt);
            }
            $request['bot_prompt'] = $bot_prompt;
        }


        return $this->api->api_url . "?" . http_build_query($request);
    }
}
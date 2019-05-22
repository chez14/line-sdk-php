<?php
namespace LINE;

class Login
{
    public const
        SCOPE_PROFILE = "profile",
        SCOPE_OPENID = "openid",
        SCOPE_EMAIL = "email";

    public
        $state_session = "LINE_SESS_AUTH_STATE";

    protected
        $api;

    public function __construct(Interfaces\ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Produce an authorization URL
     * 
     * @return string URl encoded string of that Auth link
     */
    public function get_authorization_url(string $redirect_uri, array $scope, string $state = null, string $nonce = null, string $response_type = 'code', string $prompt = "consent", int $max_age = null, string $bot_prompt = null)
    {
        if (count($scope) <= 1) {
            throw new \InvalidArgumentException("Scope must have at least 1 scope.");
        }

        if ($state === null) {
            $randomgen = (new \RandomLib\Factory())->getLowStrengthGenerator();
            $_SESSION[$this->state_session] = $randomgen->generateString(16, \RandomLib\Generator::CHAR_ALNUM);
            $state = $_SESSION[$this->state_session];
        }

        $request = [
            "response_type" => $response_type,
            "redirect_uri" => $redirect_uri,
            "client_id" => $this->api->getChannelID(),
            "scope" => \implode(" ", $scope),
            "state" => $state
        ];

        if ($nonce) {
            $request['nonce'] = $nonce;
        }

        if ($prompt) {
            $request['prompt'] = $prompt;
        }

        if ($max_age) {
            $request['max_age'] = $max_age;
        }

        if ($bot_prompt) {
            if (!\in_array($bot_prompt, ["normal", "aggressive"])) {
                throw new \InvalidArgumentException("Unknown value supplied to Bot Prompt. It must be either `normal` or `aggressive`, got " . $bot_prompt);
            }
            $request['bot_prompt'] = $bot_prompt;
        }


        return "https://access.line.me/oauth2/v2.1/authorize?" . http_build_query($request);
    }

    /**
     * Autoparse from callback url. Call this when you're in callback controller / callback handler.
     * Will throw exception automatically when error detected.
     * 
     */
    public function parse_from_request(
        string $return_uri,
        string $last_state = null,
        string $GET_code = "code",
        string $GET_state = "state",
        string $GET_friendship_status_changed = "friendship_status_changed",
        string $GET_error = "error",
        string $GET_error_description = "error_description"
    ) {

        if ($last_state === null) {
            $last_state = $_SESSION[$this->state_session];
        }

        if ($last_state != $_GET[$GET_state]) {
            throw new \InvalidArgumentException("Last state is not the same with given state. Got " . $_GET[$GET_state] . ". Expecting " . $last_state . ".");
        }

        if (array_key_exists($GET_error, $_GET)) {
            throw new Exceptions\LoginFailedExceptions($_GET[$GET_error_description], $_GET[$GET_error]);
        }

        $token = Token::fromAuthCode($GET_code, $return_uri, $this->api);
        return $token;
    }
}

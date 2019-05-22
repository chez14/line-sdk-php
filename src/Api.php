<?php
namespace LINE;

class Api implements Interfaces\ApiInterface
{
    public
        $api_url = "https://api.line.me/";

    protected
        $channel_secret,
        $channel_id,
        $token,
        $guzzle;

    /**
     * Create a new Instance of this API client.
     * 
     * @param $options you should supply channel_secret and channel_id as an array object to this.
     *  alternatively, you can add token too, to add token.
     * @throw InvalidArgumentException if some of those 
     */
    public function __construct(array $options)
    {
        $this->guzzle = new \GuzzleHttp\Client([
            "base_uri" => $this->api_url
        ]);

        // must-include:
        // - channel_secret
        // - channel_id

        if (!\key_exists('channel_secret', $options) || !$options['channel_secret']) {
            throw new \InvalidArgumentException("channel_secret is not supplied.");
        }
        if (!\key_exists('channel_id', $options) || !$options['channel_id']) {
            throw new \InvalidArgumentException("channel_id is not supplied.");
        }

        $this->setChannelID($options['channel_id']);
        $this->setChannelSecret($options['channel_secret']);

        // sets token
        if (key_exists('token', $options)) {
            if ($options['token'] instanceof \Token) {
                $this->setToken($options['token']);
            } else if (is_string($options['token'])) {
                $this->setToken(Token::fromIDToken($options['token'], $this));
            } else {
                throw new \InvalidArgumentException("token should be a string, or a LINE\Token object, got " . $options['token']);
            }
        }
    }

    public function getChannelID(): string
    {
        return $this->channel_id;
    }

    public function setChannelID(string $id): void
    {
        $this->channel_id = $id;
    }

    public function getChannelSecret(): string
    {
        return $this->channel_secret;
    }

    public function setChannelSecret(string $id): void
    {
        $this->channel_secret = $id;
    }

    public function getToken(): Token
    {
        return $this->token;
    }

    public function setToken(Token $token): void
    {
        $this->token = $token;
    }

    protected function generate_request_options($type, $param, $options, $auth_type)
    {
        $type = \strtolower($type);
        $supply_to = [
            "post" => 'form_params',
            "get" => 'query'
        ];

        if (!array_key_exists($type, $supply_to)) {
            throw new \InvalidArgumentException("Not supported type. Got " . $type);
        }

        // Supply the access token and etcs.
        $headers = [];
        if ($auth_type == "client") {
            try {
                $param = array_merge([
                    "client_id" => $this->getChannelID(),
                    "client_secret" => $this->getChannelSecret()
                ], $param);
            } catch (\Exception $e) {
                // soft error handling. If it's not present then let it go.
                // maybe, from $the option, channel id will be provided.
            }
        } else if ($auth_type == "header") {
            // No soft error handling.
            // Because it's set mannually from the param, meaning it's has consent from the developer.
            $headers['Authorization'] = "Bearer " . $this->getToken()->getAccessToken();
        }


        $request_options = array_merge([
            "headers" => $headers,
            $supply_to[$type] => $param
        ], $options);

        return $request_options;
    }

    /**
     * Do a public GET call to api.
     */
    public function get($url, $param = [], $options = [], $auth_type = "client")
    {
        $request_param = $this->generate_request_options('get', $param, $options, $auth_type);

        return $this->guzzle->request('GET', $url, $request_param);
    }

    /**
     * Do a public POST call to api
     */
    public function post($url, $param = [], $options = [], $auth_type = "client")
    {
        $request_param = $this->generate_request_options('post', $param, $options, $auth_type);

        return $this->guzzle->request('POST', $url, $request_param);
    }
}

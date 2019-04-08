<?php
namespace LINE;

class Api {
    public
        $api_url = "https://api.line.me/oauth2/v2.1/";

    protected
        $channel_secret,
        $channel_id,
        $token;


    public function getChannelID() {
        return $this->channel_id;
    }

    public function setChannelID($id) {
        $this->channel_id = $id;
    }

    public function getChannelSecret() {
        return $this->channel_secret;
    }
    
    public function setChannelSecret($id) {
        $this->channel_secret = $id;
    }

    public function getToken(Token $token) {
        return $this->token;
    }

    public function setToken(Token $token) {
        $this->token = $token;
    }

    /**
     * Do a public GET call to api.
     */
    public function get($url, $param = [], $options = []) {

    }

    public function post($url, $param = [], $options = []) {

    }


}
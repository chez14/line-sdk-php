<?php
namespace LINE;

class Api {
    public
        $api_url = "https://api.line.me/oauth2/v2.1/";

    protected
        $channel_secret,
        $channel_id;


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

}
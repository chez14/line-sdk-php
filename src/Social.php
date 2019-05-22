<?php
namespace LINE;

/**
 * Social API. This only supports to get profile, and get friendship status.
 */
class Social
{
    protected
        $api;

    public function __construct(Interfaces\ApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Get profile info.
     * 
     * @see https://developers.line.biz/en/reference/social-api/#get-user-profile
     * @return Array Object of displayName, userId, pictureUrl, statusMessage
     */
    public function getProfile()
    {
        $response = $this->api->get('v2/profile', [], [], 'header');
        return \json_decode($response->getBody(), true);
    }


    /**
     * Get Friendsgip status with bot linked to the LINE Login.
     * 
     * @see https://developers.line.biz/en/reference/social-api/#get-friendship-status
     * @return Array Object of friendFlag, with value: true if the user has added the bot as a friend and has not blocked the bot. 
     */
    public function getFriendshipStatus()
    {
        $response = $this->api->get('friendship/v1/status', [], [], 'header');
        return \json_decode($response->getBody(), true);
    }
}

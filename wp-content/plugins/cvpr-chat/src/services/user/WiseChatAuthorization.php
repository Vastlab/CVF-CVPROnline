<?php

/**
 * CVPR Chat user authorization service.
 */
class CVPRChatAuthorization {
    const SESSION_KEY_USER_CHANNEL_AUTHORIZATION = 'Cvpr_chat_user_channel_authorization';

    /**
     * @var CVPRChatUserSessionDAO
     */
    private $userSessionDAO;

    /**
     * CVPRChatAuthorization constructor.
     */
    public function __construct() {
        $this->userSessionDAO = CVPRChatContainer::getLazy('dao/user/CVPRChatUserSessionDAO');
    }

    /**
     * Determines whether current user has access for given channel.
     *
     * @param CVPRChatChannel $channel
     *
     * @return boolean
     */
    public function isUserAuthorizedForChannel($channel) {
        $grants = $this->userSessionDAO->get(self::SESSION_KEY_USER_CHANNEL_AUTHORIZATION);

        return is_array($grants) && array_key_exists($channel->getId(), $grants);
    }

    /**
     * Marks the current user as authorized for given channel.
     *
     * @param CVPRChatChannel $channel
     *
     * @return null
     */
    public function markAuthorizedForChannel($channel) {
        $grants = $this->userSessionDAO->get(self::SESSION_KEY_USER_CHANNEL_AUTHORIZATION);
        if (!is_array($grants)) {
            $grants = array();
        }

        $grants[$channel->getId()] = true;
        $this->userSessionDAO->set(self::SESSION_KEY_USER_CHANNEL_AUTHORIZATION, $grants);
    }
}
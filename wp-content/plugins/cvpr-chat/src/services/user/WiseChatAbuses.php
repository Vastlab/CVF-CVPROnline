<?php

/**
 * CVPR Chat user abuses
 */
class CVPRChatAbuses {
    const SESSION_KEY_ABUSES_COUNTER = 'Cvpr_chat_ban_detector_counter';

    /**
     * @var CVPRChatUserSessionDAO
     */
    private $userSessionDAO;

    /**
     * CVPRChatAbuses constructor.
     */
    public function __construct() {
        $this->userSessionDAO = CVPRChatContainer::getLazy('dao/user/CVPRChatUserSessionDAO');
    }

    /**
     * Increments and returns abuses counter.
     * The counter is stored in user's session.
     *
     * @return integer
     */
    public function incrementAndGetAbusesCounter() {
        $key = self::SESSION_KEY_ABUSES_COUNTER;
        $counter = 1;

        if ($this->userSessionDAO->contains($key)) {
           $counter += $this->userSessionDAO->get(self::SESSION_KEY_ABUSES_COUNTER);
        }
        $this->userSessionDAO->set(self::SESSION_KEY_ABUSES_COUNTER, $counter);

        return $counter;
    }

    /**
     * Clears abuses counter. The counter is stored in user's session.
     *
     * @return null
     */
    public function clearAbusesCounter() {
        $this->userSessionDAO->set(self::SESSION_KEY_ABUSES_COUNTER, 0);
    }
}
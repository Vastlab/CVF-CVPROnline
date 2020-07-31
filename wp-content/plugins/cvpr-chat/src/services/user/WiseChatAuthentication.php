<?php

/**
 * CVPR Chat authentication service.
 */
class CVPRChatAuthentication {
    const SESSION_KEY_ORIGINAL_USERNAME = 'Cvpr_chat_user_name_auto';
    const SESSION_KEY_USER_ID = 'Cvpr_chat_user_id';
    const SYSTEM_USER_NAME = 'System';

    /**
     * @var CVPRChatUsersDAO
     */
    private $usersDAO;

    /**
     * @var CVPRChatUserSessionDAO
     */
    private $userSessionDAO;

    /**
     * @var CVPRChatChannelUsersDAO
     */
    private $channelUsersDAO;

    /**
     * @var CVPRChatOptions
     */
    private $options;

    /**
     * CVPRChatAuthentication constructor.
     */
    public function __construct() {
        $this->usersDAO = CVPRChatContainer::get('dao/user/CVPRChatUsersDAO');
        $this->userSessionDAO = CVPRChatContainer::getLazy('dao/user/CVPRChatUserSessionDAO');
        $this->channelUsersDAO = CVPRChatContainer::getLazy('dao/CVPRChatChannelUsersDAO');
        $this->options = CVPRChatOptions::getInstance();
    }

    /**
     * Determines whether the current user is the authenticated chat user.
     *
     * @return boolean
     */
    public function isAuthenticated() {
        return $this->userSessionDAO->contains(self::SESSION_KEY_USER_ID) && intval($this->userSessionDAO->get(self::SESSION_KEY_USER_ID)) > 0;
    }

    /**
     * Returns authenticated user or null. The method is cached.
     *
     * @return CVPRChatUser|null
     */
    public function getUser() {
        static $cache = null;

        if ($this->isAuthenticated()) {
            if ($cache === null) {
                $cache = $this->usersDAO->get($this->userSessionDAO->get(self::SESSION_KEY_USER_ID));
            }

            return $cache;
        }

        return null;
    }

    /**
     * Returns non-persistent system user.
     *
     * @return CVPRChatUser
     */
    public function getSystemUser() {
        CVPRChatContainer::load('model/CVPRChatUser');

        $user = new CVPRChatUser();
        $user->setId(0);
        $user->setName(self::SYSTEM_USER_NAME);
        $user->setSessionId($this->userSessionDAO->getSessionId());
		$user->setIp($this->getServerAddress());
		
        return $user;
    }

    /**
     * Returns authenticated username or empty string.
     *
     * @return string
     */
    public function getUserNameOrEmptyString() {
        $user = $this->getUser();

        return $user !== null ? $user->getName() : '';
    }

    /**
     * Returns authenticated user ID or null.
     *
     * @return integer|null
     */
    public function getUserIdOrNull() {
        $user = $this->getUser();

        return $user !== null ? $user->getId() : null;
    }

    /**
     * Authenticates anonymously the current user.
     *
     * @return CVPRChatUser
     * @throws Exception
     */
    public function authenticateAnonymously() {
        if ($this->isAuthenticated()) {
            throw new Exception('Unsupported operation');
        }

        // generate new suffix for anonymous username:
        $userNameSuffix = $this->options->getUserNameSuffix() + 1;
        $this->options->setUserNameSuffix($userNameSuffix);
        $userName = $this->options->getOption('user_name_prefix', 'Anonymous').$userNameSuffix;

        return $this->createUserAndSave($userName);
    }

    /**
     * Authenticates user by username if no user is authenticated yet.
     *
     * @param string $userName
     *
     * @return CVPRChatUser
     * @throws Exception If username cannot be set due to errors
     */
    public function authenticate($userName) {
        if ($this->isAuthenticated()) {
            throw new Exception('Unsupported operation');
        }
        $userName = $this->validateUserName($userName);

        return $this->createUserAndSave($userName);
    }
    
    /**
     * Drops authentication.
     */
    public function dropAuthentication() {
        $this->userSessionDAO->drop(self::SESSION_KEY_USER_ID);
    }

    /**
     * Validates given username.
     *
     * @param string $userName
     *
     * @return string Validated username (trimmed and filtered)
     * @throws Exception If username is not valid
     */
    public function validateUserName($userName) {
        $userName = trim($userName);

        // check for valid characters:
        if (strlen($userName) == 0 || !preg_match('/^[\p{L}a-zA-Z0-9\-_ ’]+$/u', $userName)) {
            throw new Exception($this->options->getOption('message_error_1', 'Only letters, number, spaces, hyphens and underscores are allowed'));
        }

        // filter the new username:
        if ($this->options->isOptionEnabled('filter_bad_words')) {
            CVPRChatContainer::load('rendering/filters/pre/CVPRChatFilter');
            $userName = CVPRChatFilter::filter($userName);
        }

        // check if the new username is already occupied:
        $occupiedException = new Exception($this->options->getOption('message_error_2', 'This name is already occupied'));
        $prefix = $this->options->getOption('user_name_prefix', 'Anonymous');
        $disableUserNameCheck = $this->options->isOptionEnabled('disable_user_name_duplication_check', false);
        if (
            $this->getUserNameOrEmptyString() == $userName ||
            $this->usersDAO->getWpUserByDisplayName($userName) !== null ||
            $this->usersDAO->getWpUserByLogin($userName) !== null ||
            $this->channelUsersDAO->isUserNameOccupied($userName, $this->userSessionDAO->getSessionId(), $disableUserNameCheck) ||
            preg_match("/^{$prefix}/", $userName) ||
            $userName == $this->getSystemUser()->getName()
        ) {
            throw $occupiedException;
        }

        return $userName;
    }

    /**
     * Returns the original username if exists.
     *
     * @return string|null
     */
    public function getOriginalUserName() {
        if ($this->userSessionDAO->contains(self::SESSION_KEY_ORIGINAL_USERNAME)) {
            return $this->userSessionDAO->get(self::SESSION_KEY_ORIGINAL_USERNAME);
        } else {
            return null;
        }
    }

    /**
     * Sets the original username.
     *
     * @param string $userName
     */
    public function setOriginalUserName($userName) {
        $this->userSessionDAO->set(self::SESSION_KEY_ORIGINAL_USERNAME, $userName);
    }

    /**
     * @param string $userName
     *
     * @return CVPRChatUser
     */
    private function createUserAndSave($userName) {
        CVPRChatContainer::load('model/CVPRChatUser');

        // construct username and user object:
        $user = new CVPRChatUser();
        $user->setName($userName);
        $user->setSessionId($this->userSessionDAO->getSessionId());
        $user->setIp($this->getRemoteAddress());
        if ($this->options->isOptionEnabled('collect_user_stats', true)) {
            $this->fillWithGeoDetails($user);
        }

        // save user in DB and in the session:
        $this->usersDAO->save($user);
        $this->userSessionDAO->set(self::SESSION_KEY_USER_ID, $user->getId());

        return $user;
    }

    /**
     * @param CVPRChatUser $user
     */
    private function fillWithGeoDetails($user) {
        /** @var CVPRChatGeoService $geoService */
        $geoService = CVPRChatContainer::get('services/CVPRChatGeoService');
        $geoDetails = $geoService->getGeoDetails($this->getRemoteAddress());
        if ($geoDetails !== null) {
            $geoDetailsArray = $geoDetails->toArray();
            foreach ($geoDetailsArray as $key => $value) {
                $user->setDataProperty($key, $value);
            }
        }
    }

    /**
     * Returns remote address.
     *
     * @return string
     */
    private function getRemoteAddress() {
        return $_SERVER['REMOTE_ADDR'];
    }
    
    /**
     * Returns server address.
     *
     * @return string
     */
    private function getServerAddress() {
        return $_SERVER['SERVER_ADDR'];
    }
}
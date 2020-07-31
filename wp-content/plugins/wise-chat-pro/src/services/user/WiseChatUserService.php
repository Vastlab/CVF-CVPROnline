<?php

/**
 * WiseChat user services.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatUserService {
	const USERS_ACTIVITY_TIME_FRAME = 30;
	const USERS_PRESENCE_TIME_FRAME = 86400;
	const USERS_LIST_SESSION_KEY = 'wise_chat_current_users_list_channel_';
	const USERS_LIST_CATEGORY_NEW = '_new';
	const USERS_LIST_CATEGORY_ABSENT = '_absent';
	
	/**
	* @var WiseChatActions
	*/
	private $actions;

	/**
	* @var WiseChatMessagesDAO
	*/
	private $messagesDAO;

	/**
	* @var WiseChatUsersDAO
	*/
	private $usersDAO;

	/**
	 * @var WiseChatUserSessionDAO
	 */
	private $userSessionDAO;
	
	/**
	* @var WiseChatChannelUsersDAO
	*/
	private $channelUsersDAO;

	/**
	 * @var WiseChatAuthentication
	 */
	private $authentication;

	/**
	 * @var WiseChatUserEvents
	 */
	private $userEvents;

	/**
	 * @var WiseChatService
	 */
	private $service;
	
	/**
	* @var WiseChatOptions
	*/
	private $options;
	
	public function __construct() {
		$this->options = WiseChatOptions::getInstance();
		$this->usersDAO = WiseChatContainer::getLazy('dao/user/WiseChatUsersDAO');
		$this->userSessionDAO = WiseChatContainer::getLazy('dao/user/WiseChatUserSessionDAO');
		$this->actions = WiseChatContainer::getLazy('services/user/WiseChatActions');
		$this->channelUsersDAO = WiseChatContainer::getLazy('dao/WiseChatChannelUsersDAO');
		$this->messagesDAO = WiseChatContainer::getLazy('dao/WiseChatMessagesDAO');
		$this->authentication = WiseChatContainer::getLazy('services/user/WiseChatAuthentication');
		$this->userEvents = WiseChatContainer::getLazy('services/user/WiseChatUserEvents');
		$this->service = WiseChatContainer::getLazy('services/WiseChatService');
	}
	
	/**
	* Maintenance actions performed on the init phase.
	*
	* @return null
	*/
	public function initMaintenance() {
		$userSettingsDAO = WiseChatContainer::get('dao/user/WiseChatUserSettingsDAO');
		$userSettingsDAO->initialize();
	}
	
	/**
	* Maintenance actions performed at start-up.
	*
	* @param WiseChatChannel $channel
	*
	* @return null
	*/
	public function startUpMaintenance($channel) {
		$this->userEvents->resetEventTracker('usersList', $channel->getName());
	}
	
	/**
	* Maintenance actions performed periodically. The method authenticates user if there was
	* no authentication performed.
	*
	* @param WiseChatChannel $channel
	*
	* @return null
	*/
	public function periodicMaintenance($channel) {
		// check and authenticate user:
		if (!$this->authentication->isAuthenticated()) {
			$user = null;

			// check if there is a WordPress user logged in:
			$currentWPUser = $this->usersDAO->getCurrentWpUser();
			if ($currentWPUser !== null && strlen($currentWPUser->display_name) > 0) {
				// check if chat user with this WP ID exists already:
				$user = $this->usersDAO->getLatestByWordPressId($currentWPUser->ID);
				if ($user === null) {
					$user = $this->authentication->authenticateAnonymously();
					$this->authentication->setOriginalUserName($user->getName());
					$user->setWordPressId(intval($currentWPUser->ID));
				} else {
					$this->authentication->authenticateWithUser($user);
					$this->authentication->setOriginalUserName($this->authentication->getNextAnonymousUserName());
				}

				// update username and WP user ID:
				$user->setName($currentWPUser->display_name);
				$this->usersDAO->save($user);
			}

			// authenticate only if anonymous login is not prohibited:
			if ($user === null && $this->options->getIntegerOption('access_mode', 0) != 1) {
				$user = $this->authentication->authenticateAnonymously();
			}

			if ($user !== null) {
				$this->actions->publishAction(
					'refreshPlainUserName', array('name' => $user->getName()), $user
				);

				/**
				 * Fires once user has started its session in the chat.
				 *
				 * @since 2.3.2
				 *
				 * @param WiseChatUser $user The user object
				 */
				do_action("wc_user_session_started", $user);
			}
		}

		// signal presence in the channel:
		$this->markPresenceInChannel($channel);
		
		$this->refreshChannelUsersData();
	}
	
	/**
	* Refreshes channel users data.
	*
	* @return null
	*/
	public function refreshChannelUsersData() {
		$timeFrame = $this->options->getIntegerOption('user_name_lock_window_seconds', self::USERS_PRESENCE_TIME_FRAME);
		if ($timeFrame < 600) {
			$timeFrame = self::USERS_PRESENCE_TIME_FRAME;
		}
		$this->channelUsersDAO->deleteOlderByLastActivityTime($timeFrame);
		$this->channelUsersDAO->updateActiveForOlderByLastActivityTime(false, self::USERS_ACTIVITY_TIME_FRAME);
	}

	/**
	 * Checks if the current user has right to send a message.
	 *
	 * @return bool
	 */
	public function isSendingMessagesAllowed() {
		if ($this->usersDAO->isWpUserLogged()) {
			$targetRoles = (array) $this->options->getOption("read_only_for_roles", array());
			if (count($targetRoles) > 0) {
				$wpUser = $this->usersDAO->getCurrentWpUser();

				return !is_array($wpUser->roles) || count(array_intersect($targetRoles, $wpUser->roles)) == 0;
			} else {
				return true;
			}
		} else {
			return !$this->options->isOptionEnabled('read_only_for_anonymous', false);
		}
	}
	
	/**
	* If the user has logged in then replace anonymous username with WordPress user name.
	* If WordPress user logs out then the anonymous username is restored.
	*
	* @return null
	*/
	public function switchUser() {
		$currentWPUser = $this->usersDAO->getCurrentWpUser();

		if (!$this->authentication->isAuthenticated()) {
			if ($currentWPUser !== null && strlen($currentWPUser->display_name) > 0) {
				// check if chat user with this WP ID exists already:
				$user = $this->usersDAO->getLatestByWordPressId($currentWPUser->ID);

				if ($user === null) {
					$user = $this->authentication->authenticateAnonymously();
					$this->authentication->setOriginalUserName($user->getName());
					$user->setWordPressId(intval($currentWPUser->ID));
				} else {
					$this->authentication->authenticateWithUser($user);
					$this->authentication->setOriginalUserName($this->authentication->getNextAnonymousUserName());
				}

				// update username and WP user ID:
				$user->setName($currentWPUser->display_name);
				$this->usersDAO->save($user);

				/**
				 * Fires once user has started its session in the chat.
				 *
				 * @since 2.3.2
				 *
				 * @param WiseChatUser $user The user object
				 */
				do_action("wc_user_session_started", $user);
			}
			return;
		} else {
			$wasAuthenticated = false;
			$user = $this->authentication->getUser();
			if ($user === null) {
				$this->authentication->dropAuthentication();
				$user = $this->authentication->authenticateAnonymously();
				$wasAuthenticated = true;
			}
			$userName = $user->getName();

			if (!$this->authentication->isAuthenticatedExternally()) {
				if ($currentWPUser !== null) {
					$displayName = $currentWPUser->display_name;
					if (strlen($displayName) > 0 && $userName != $displayName) {
						if ($this->authentication->getOriginalUserName() === null) {
							$this->authentication->setOriginalUserName($userName);
						}

						// update username and WP user ID:
						$user->setName($displayName);
						$user->setWordPressId(intval($currentWPUser->ID));
						$this->usersDAO->save($user);

						$this->refreshUserName($user);
					}
				} else {
					$originalUserName = $this->authentication->getOriginalUserName();
					if ($originalUserName !== null && $userName != $originalUserName) {
						// the user becomes anonymous, so remove the user from all channels:
						if ($this->service->isChatAllowedForWPUsersOnly()) {
							$this->channelUsersDAO->deleteAllByUser($user);
						}

						// update username and WP user ID:
						$user->setName($originalUserName);
						$user->setWordPressId(null);
						$this->usersDAO->save($user);

						$this->refreshUserName($user);
					}
				}
			}

			if ($wasAuthenticated) {
				/**
				 * Fires once user has started its session in the chat.
				 *
				 * @since 2.3.2
				 *
				 * @param WiseChatUser $user The user object
				 */
				do_action("wc_user_session_started", $user);
			}
		}
	}
	
	/**
	* Sets a new name for current user.
	*
	* @param string $userName A new username to set
	*
	* @return string New username
	* @throws Exception On validation error
	*/
	public function changeUserName($userName) {
		if (
			!$this->options->isOptionEnabled('allow_change_user_name') ||
			$this->usersDAO->getCurrentWpUser() !== null ||
			$this->authentication->isAuthenticatedExternally() ||
			!$this->authentication->isAuthenticated()
		) {
			throw new Exception('Unsupported operation');
		}

		$userName = $this->authentication->validateUserName($userName);
		$user = $this->authentication->getUser();
		$oldName = $user->getName();

		// set new username and refresh it:
		$user->setName($userName);
		$this->usersDAO->save($user);
		$this->refreshNewUserName($user);
		$this->authentication->setOriginalUserName($userName);

		/**
		 * Fires once user has changed its name. It applies to anonymous users only.
		 *
		 * @since 2.3.2
		 *
		 * @param string $oldName The old name
		 * @param string $userName The new name
		 * @param WiseChatUser $user The user object
		 */
		do_action("wc_username_changed", $oldName, $userName, $user);

		return $userName;
	}
	
	/**
	* Sets text color for messages typed by the current user.
	*
	* @param string $color
	*
	* @throws Exception If an error occurred
	*/
	public function setUserTextColor($color) {
		if (!$this->authentication->isAuthenticated()) {
			throw new Exception('Unsupported operation');
		}
		if ($color != 'null' && !preg_match("/^#[a-fA-F0-9]{6}$/", $color)) {
			throw new Exception('Invalid color signature');
		}
		if ($color == 'null') {
			$color = '';
		}

		$user = $this->authentication->getUser();
		$user->setDataProperty('textColor', $color);
		$this->usersDAO->save($user);
		$this->userEvents->resetEventTracker('usersList');
		$this->actions->publishAction(
			'setMessagesProperty', array(
				'chatUserId' => $user->getId(),
				'propertyName' => 'textColor',
				'propertyValue' => $color
			)
		);

		/**
		 * Fires once user has set its color.
		 *
		 * @since 2.3.2
		 *
		 * @param string $color The color code
		 * @param WiseChatUser $user The user object
		 */
		do_action("wc_usercolor_set", $color, $user);
	}

	/**
	 * Sets a user property.
	 *
	 * @param string $property
	 * @param mixed $value
	 *
	 * @throws Exception If an error occurred
	 */
	public function setProperty($property, $value) {
		if (!$this->authentication->isAuthenticated()) {
			throw new Exception('Unsupported operation');
		}

		$user = $this->authentication->getUser();
		$user->setDataProperty($property, $value);
		$this->usersDAO->save($user);

		/**
		 * Fires once user property has been set.
		 *
		 * @since 2.3.2
		 *
		 * @param string $property Property name
		 * @param mixed $value Property value
		 * @param WiseChatUser $user The user object
		 */
		do_action("wc_userproperty_set", $property, $value, $user);
	}

	/**
	 * Returns absent users since the last check.
	 * Status is preserved in user session.
	 *
	 * @param WiseChatChannel $channel
	 *
	 * @return array Array of arrays containing id and name keys
	 */
	public function getAbsentUsersForChannel($channel) {
		if ($channel === null) {
			return array();
		}
		// get the last status:
		$lastUsersRaw = $this->getPersistedUsersList($channel, self::USERS_LIST_CATEGORY_ABSENT);
		// if the last users list is empty - return empty array:
		if (count($lastUsersRaw) === 0) {
			return array();
		}
		// check for absent users:
		$currentUsersMap = array();
		$users = $this->getUsersListForChannel($channel);
		foreach ($users as $user) {
			$currentUsersMap[$user->getId()] = true;
		}
		$absentUsers = array();
		foreach ($lastUsersRaw as $user) {
			if (!array_key_exists($user['id'], $currentUsersMap) && $this->authentication->getUserIdOrNull() != $user['id']) {
				$absentUsers[] = $user;
			}
		}
		return $absentUsers;
	}
	/**
	 * Returns new users since the last check.
	 * Status is preserved in user session.
	 *
	 * @param WiseChatChannel $channel
	 *
	 * @return array Array of arrays containing id and name keys
	 */
	public function getNewUsersForChannel($channel) {
		if ($channel === null) {
			return array();
		}
		// get the last status:
		$lastUsersRaw = $this->getPersistedUsersList($channel, self::USERS_LIST_CATEGORY_NEW);
		$users = $this->getUsersListForChannel($channel);
		if (count($users) === 0) {
			return array();
		}
		$lastUsersRawMap = array();
		foreach ($lastUsersRaw as $rawUser) {
			$lastUsersRawMap[$rawUser['id']] = $rawUser;
		}
		// check for new users:
		$newUsers = array();
		foreach ($users as $user) {
			if (!array_key_exists($user->getId(), $lastUsersRawMap) && $this->authentication->getUserIdOrNull() != $user->getId()) {
				$newUsers[] = array(
					'id' => $user->getId(),
					'name' => $user->getName()
				);
			}
		}
		return $newUsers;
	}
	/**
	 * Loads users list for the given channel and saves it in user session.
	 *
	 * @param WiseChatChannel $channel
	 * @param string $listCategory
	 */
	public function persistUsersListInSession($channel, $listCategory) {
		if ($channel === null) {
			return;
		}
		$usersRaw = array();
		$users = $this->getUsersListForChannel($channel);
		foreach ($users as $user) {
			$usersRaw[] = array(
				'id' => $user->getId(),
				'name' => $user->getName()
			);
		}
		$this->userSessionDAO->set(self::USERS_LIST_SESSION_KEY.$channel->getId().$listCategory, json_encode($usersRaw));
	}
	/**
	 * Clears users list for the given channel.
	 *
	 * @param WiseChatChannel $channel
	 * @param string $listCategory
	 */
	public function clearUsersListInSession($channel, $listCategory) {
		if ($channel === null) {
			return;
		}
		$this->userSessionDAO->drop(self::USERS_LIST_SESSION_KEY.$channel->getId().$listCategory);
	}

	/**
	 * Checks if the first given user can communicate with the second user.
	 *
	 * @param WiseChatUser $user
	 * @param WiseChatUser $associatedUser
	 * @return bool
	 */
	public function isUsersConnectionAvailable($user, $associatedUser) {
		if (!$this->options->isOptionEnabled('enable_buddypress', false)) {
			return true;
		}
		if (!$this->options->isOptionEnabled('users_list_bp_users_only', false)) {
			return true;
		}

		if ($user === null || $associatedUser === null) {
			return false;
		}

		if (!($user->getWordPressId() > 0) || !($associatedUser->getWordPressId() > 0)) {
			return false;
		}

		if ($user->getWordPressId() == $associatedUser->getWordPressId()) {
			return true;
		}

		if (function_exists('friends_check_friendship')) {
			return friends_check_friendship($user->getWordPressId(), $associatedUser->getWordPressId());
		}

		return false;
	}

	/**
	 * Returns users list for the given channel (persisted in session).
	 *
	 * @param WiseChatChannel $channel
	 * @param string $listCategory
	 * @return array
	 */
	private function getPersistedUsersList($channel, $listCategory) {
		if ($channel === null) {
			return array();
		}
		$lastUsersRawJSON = $this->userSessionDAO->get(self::USERS_LIST_SESSION_KEY.$channel->getId().$listCategory);
		$lastUsersRaw = array();
		if ($lastUsersRawJSON !== null) {
			$lastUsersRaw = json_decode($lastUsersRawJSON, true);
			if (!is_array($lastUsersRaw)) {
				$lastUsersRaw = array();
			}
		}
		return $lastUsersRaw;
	}
	/**
	 * Returns current users list in the given channel.
	 *
	 * @param WiseChatChannel $channel
	 *
	 * @return WiseChatUser[]
	 */
	public function getUsersListForChannel($channel) {
		$hideRoles = $this->options->getOption('users_list_hide_roles', array());
		$channelUsers = $this->channelUsersDAO->getAllActiveByChannelId($channel->getId());
		$usersList = array();
		foreach ($channelUsers as $channelUser) {
			if ($channelUser->getUser() == null) {
				continue;
			}
			// do not render anonymous users:
			if ($this->service->isChatAllowedForWPUsersOnly() && !($channelUser->getUser()->getWordPressId() > 0)) {
				continue;
			}
			// hide chosen roles:
			if (is_array($hideRoles) && count($hideRoles) > 0 && $channelUser->getUser()->getWordPressId() > 0) {
				$wpUser = $this->usersDAO->getWpUserByID($channelUser->getUser()->getWordPressId());
				if (is_array($wpUser->roles) && count(array_intersect($hideRoles, $wpUser->roles)) > 0) {
					continue;
				}
			}
			// hide anonymous users:
			if ($this->options->isOptionEnabled('users_list_hide_anonymous', false) && !($channelUser->getUser()->getWordPressId() > 0)) {
				continue;
			}
			$usersList[] = $channelUser->getUser();
		}
		return $usersList;
	}

	/**
	 * Calculates hash for given user ID. Hash are unique across sites (multisite safe).
	 *
	 * @param string $userId
	 * @return string
	 */
	public static function getUserHash($userId) {
		return sha1(wp_salt().get_current_blog_id().$userId);
	}

	/**
	 * Handles WordPress user profile changes.
	 *
	 * @param integer $wpUserId
	 * @param stdClass $wpUserOldData
	 */
	public function onWpUserProfileUpdate($wpUserId, $wpUserOldData) {
		if (property_exists($wpUserOldData, 'data') && property_exists($wpUserOldData->data, 'display_name')) {
			$wpUser = $this->usersDAO->getWpUserByID($wpUserId);
			if ($wpUser !== null && $wpUser->ID > 0 && $wpUser->display_name != $wpUserOldData->data->display_name) {
				$this->usersDAO->updateNameByWordPressId($wpUser->display_name, $wpUser->ID);
				$this->messagesDAO->updateUserNameByWordPressUserId($wpUser->display_name, $wpUser->ID);
			}
		}
	}
	
	/**
	* Marks presence of the current user in the given channel.
	*
	* @param WiseChatChannel $channel
	*
	* @return null
	*/
	private function markPresenceInChannel($channel) {
		$user = $this->authentication->getUser();
		if ($user !== null) {
			$channelUser = $this->channelUsersDAO->getByUserIdAndChannelId($user->getId(), $channel->getId());

			if ($channelUser === null) {
				$channelUser = new WiseChatChannelUser();
				$channelUser->setActive(true);
				$channelUser->setLastActivityTime(time());
				$channelUser->setUserId($user->getId());
				$channelUser->setChannelId($channel->getId());
				$this->channelUsersDAO->save($channelUser);
			} else {
				$channelUser->setActive(true);
				$channelUser->setLastActivityTime(time());
				$this->channelUsersDAO->save($channelUser);
			}
		}
	}
	
	/**
	* Refreshes username on user interface. Resets event tracker for users list and
	* publishes an action that refreshes username in plain places.
	*
	* @param WiseChatUser $user
	*
	* @return null
	*/
	private function refreshUserName($user) {
		$this->userEvents->resetEventTracker('usersList');
		$this->actions->publishAction('refreshPlainUserName', array('name' => $user->getName()), $user);
		if ($this->options->isOptionEnabled('enable_private_messages')) {
			$this->actions->publishAction('refreshPlainUserNameByHash', array('hash' => self::getUserHash($user->getId()), 'name' => $user->getName()));
		}
	}
	
	/**
	* Refreshes username after setting a new one.
	*
	* @param WiseChatUser $user
	*
	* @return null
	*/
	private function refreshNewUserName($user) {
        WiseChatContainer::load('dao/criteria/WiseChatMessagesCriteria');

        $this->refreshUserName($user);
		$updateCriteria = WiseChatMessagesCriteria::build()->setUserId($user->getId());
		if ($this->options->isOptionEnabled('enable_private_messages')) {
			$updateCriteria->setRecipientOrSenderId($user->getId());
		}
        $this->messagesDAO->updateUserNameByCriteria(
			$user->getName(), $updateCriteria
		);

        /** @var WiseChatRenderer $renderer */
        $renderer = WiseChatContainer::get('rendering/WiseChatRenderer');

		$criteria = new WiseChatMessagesCriteria();
		$criteria->setUserId($user->getId());
		if ($this->options->isOptionEnabled('enable_private_messages')) {
			$criteria->setRecipientOrSenderId($user->getId());
		}
		$messages = $this->messagesDAO->getAllByCriteria($criteria);
		if (count($messages) > 0) {
			$messagesIds = array();
			$renderedUserName = null;
			foreach ($messages as $message) {
				$messagesIds[] = $message->getId();
				if ($renderedUserName === null) {
					$renderedUserName = $renderer->getRenderedUserName($message);
				}
			}

			$this->actions->publishAction(
				'replaceUserNameInMessages', array(
					'renderedUserName' => $renderedUserName,
					'messagesIds' => $messagesIds
				)
			);
		}
	}
}
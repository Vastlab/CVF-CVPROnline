<?php

/**
 * Wise Chat message rendering class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatRenderer {
	
	/**
	* @var WiseChatMessagesService
	*/
	private $messagesService;

	/**
	 * @var WiseChatService
	 */
	private $service;

	/**
	 * @var WiseChatUserService
	 */
	private $userService;
	
	/**
	* @var WiseChatUsersDAO
	*/
	private $usersDAO;
	
	/**
	* @var WiseChatChannelUsersDAO
	*/
	private $channelUsersDAO;

	/**
	 * @var WiseChatAuthentication
	 */
	private $authentication;

	/**
	 * @var WiseChatExternalAuthentication
	 */
	private $externalAuthentication;

	/**
	 * @var WiseChatHttpRequestService
	 */
	private $httpRequestService;

	/**
	 * @var WiseChatPrivateMessagesRulesService
	 */
	private $privateMessagesRulesService;
	
	/**
	* @var WiseChatOptions
	*/
	private $options;
	
	/**
	* @var WiseChatTemplater
	*/
	private $templater;

	/**
	 * @var WiseChatCssRenderer
	 */
	private $cssRenderer;
	
	public function __construct() {
		$this->options = WiseChatOptions::getInstance();
		$this->messagesService = WiseChatContainer::get('services/WiseChatMessagesService');
		$this->service = WiseChatContainer::getLazy('services/WiseChatService');
		$this->userService = WiseChatContainer::getLazy('services/user/WiseChatUserService');
		$this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
		$this->channelUsersDAO = WiseChatContainer::get('dao/WiseChatChannelUsersDAO');
		$this->authentication = WiseChatContainer::getLazy('services/user/WiseChatAuthentication');
		$this->externalAuthentication = WiseChatContainer::getLazy('services/user/WiseChatExternalAuthentication');
		$this->httpRequestService = WiseChatContainer::getLazy('services/WiseChatHttpRequestService');
		$this->privateMessagesRulesService = WiseChatContainer::getLazy('services/WiseChatPrivateMessagesRulesService');
		$this->cssRenderer = WiseChatContainer::get('rendering/WiseChatCssRenderer');
		WiseChatContainer::load('WiseChatThemes');
		WiseChatContainer::load('rendering/WiseChatTemplater');
		WiseChatContainer::load('services/user/WiseChatUserService');

		$this->templater = new WiseChatTemplater($this->options->getPluginBaseDir());
	}

    /**
     * Returns rendered password authorization page.
     *
	 * @param WiseChatChannel $channel
     * @param string|null $authorizationError
     *
     * @return string HTML source
     * @throws Exception
     */
	public function getRenderedPasswordAuthorization($channel, $authorizationError = null) {
		$this->templater->setTemplateFile(WiseChatThemes::getInstance()->getPasswordAuthorizationTemplate());
		$chatId = $this->service->getChatID();
		
		$jsOptions = array(
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
		);

		$data = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
			'themeStyles' => $this->options->getBaseDir().WiseChatThemes::getInstance()->getCss(),
			'windowTitle' => $this->options->getEncodedOption('window_title', ''),
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'showWindowTitle' => strlen($this->options->getEncodedOption('window_title', '')) > 0 || $this->options->getIntegerOption('mode', 0) === 1,
			'messageChannelPasswordAuthorizationHint' => $this->options->getEncodedOption(
				'message_channel_password_authorization_hint', 'This channel is protected. Enter your password:'
			),
			'messageLogin' => $this->options->getEncodedOption('message_login', 'Log in'),
			'authorizationError' => $authorizationError,
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			
			'jsOptionsEncoded' => htmlspecialchars(json_encode($jsOptions), ENT_QUOTES, 'UTF-8'),
		);
		
		return $this->templater->render($data);
	}

	/**
	 * Returns rendered external authorization page.
	 *
	 * @param WiseChatChannel $channel
	 * @param string|null $authenticationError
	 *
	 * @return string HTML source
	 * @throws Exception
	 */
	public function getRenderedExternalAuthentication($channel, $authenticationError = null) {
		$this->templater->setTemplateFile(WiseChatThemes::getInstance()->getExternalAuthenticationTemplate());
		$chatId = $this->service->getChatID();

		$facebookRedirectURL = null;
		$twitterRedirectURL = null;
		$googleRedirectURL = null;
		try {
			if ($this->options->isOptionEnabled('facebook_login_enabled', false)) {
				$facebookRedirectURL = $this->externalAuthentication->getFacebookRedirectLoginURL();
			}
			if ($this->options->isOptionEnabled('twitter_login_enabled', false)) {
				$twitterRedirectURL = $this->externalAuthentication->getTwitterRedirectLoginURL();
			}
			if ($this->options->isOptionEnabled('google_login_enabled', false)) {
				$googleRedirectURL = $this->externalAuthentication->getGoogleRedirectLoginURL();
			}
		} catch (Exception $e) {
			$authenticationError = $e->getMessage();
		}
		
		$jsOptions = array(
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
		);

		$data = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'baseDir' => $this->options->getBaseDir(),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
			'themeStyles' => $this->options->getBaseDir().WiseChatThemes::getInstance()->getCss(),
			'windowTitle' => $this->options->getEncodedOption('window_title', ''),
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'showWindowTitle' => strlen($this->options->getEncodedOption('window_title', '')) > 0 || $this->options->getIntegerOption('mode', 0) === 1,
			'loginUsing' => $this->options->getEncodedOption('message_login_using', 'Log in using'),
			'loginAnonymously' => $this->options->getEncodedOption('message_login_anonymously', 'Log in anonymously'),

			'anonymousLogin' => $this->options->isOptionEnabled('anonymous_login_enabled', true),
			'anonymousLoginURL' => $this->httpRequestService->getCurrentURLWithParameter('wcAnonymousLogin', 'an'),

			'facebook' => $this->options->isOptionEnabled('facebook_login_enabled', false),
			'facebookRedirectURL' => $facebookRedirectURL,

			'twitter' => $this->options->isOptionEnabled('twitter_login_enabled', false),
			'twitterRedirectURL' => $twitterRedirectURL,

			'google' => $this->options->isOptionEnabled('google_login_enabled', false),
			'googleRedirectURL' => $googleRedirectURL,

			'authenticationError' => $authenticationError,

			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),

			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			
			'jsOptionsEncoded' => htmlspecialchars(json_encode($jsOptions), ENT_QUOTES, 'UTF-8'),
		);

		return $this->templater->render($data);
	}
	
	/**
	* Returns rendered access-denied page.
	*
	* @param WiseChatChannel $channel
	* @param object $errorMessage
	* @param object $cssClass
	*
	* @return string HTML source
	*/
	public function getRenderedAccessDenied($channel, $errorMessage, $cssClass) {
		$this->templater->setTemplateFile(WiseChatThemes::getInstance()->getAccessDeniedTemplate());
		$chatId = $this->service->getChatID();
		
		$jsOptions = array(
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
		);

		$data = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
			'themeStyles' => $this->options->getBaseDir().WiseChatThemes::getInstance()->getCss(),
			'windowTitle' => $this->options->getEncodedOption('window_title', ''),
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'showWindowTitle' => strlen($this->options->getEncodedOption('window_title', '')) > 0 || $this->options->getIntegerOption('mode', 0) === 1,
			'errorMessage' => $errorMessage,
			'cssClass' => $cssClass,
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'jsOptionsEncoded' => htmlspecialchars(json_encode($jsOptions), ENT_QUOTES, 'UTF-8'),
		);
		
		return $this->templater->render($data);
	}

	/**
	 * Returns the form which allows to enter username.
	 *
	 * @param WiseChatChannel $channel
	 * @param string|null $errorMessage
	 *
	 * @return string HTML source
	 * @throws Exception
	 */
	public function getRenderedUserNameForm($channel, $errorMessage = null) {
		$this->templater->setTemplateFile(WiseChatThemes::getInstance()->getUserNameFormTemplate());
		$chatId = $this->service->getChatID();
		
		$jsOptions = array(
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
		);
		
		$data = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'isDefaultTheme' => strlen($this->options->getEncodedOption('theme', '')) === 0,
			'themeStyles' => $this->options->getBaseDir().WiseChatThemes::getInstance()->getCss(),
			'windowTitle' => $this->options->getEncodedOption('window_title', ''),
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'showWindowTitle' => strlen($this->options->getEncodedOption('window_title', '')) > 0 || $this->options->getIntegerOption('mode', 0) === 1,
			'errorMessage' => $errorMessage,
			'messageLogin' => $this->options->getEncodedOption('message_login', 'Log in'),
			'messageEnterUserName' => $this->options->getEncodedOption('message_enter_user_name', 'Enter your username'),
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'jsOptionsEncoded' => htmlspecialchars(json_encode($jsOptions), ENT_QUOTES, 'UTF-8'),
		);

		return $this->templater->render($data);
	}

	/**
	 * Returns rendered message for specified user.
	 *
	 * @param WiseChatMessage $message
	 * @param integer|null $userId
	 *
	 * @return string HTML source
	 * @throws Exception
	 */
	public function getRenderedMessage($message, $userId) {
		$this->templater->setTemplateFile(WiseChatThemes::getInstance()->getMessageTemplate());

		$textColorAffectedParts = array();
		$isTextColorSet = false;

		// text color defined by role:
		$textColor = $this->getTextColorDefinedByUserRole($message->getUser());
		if (strlen($textColor) > 0) {
			$isTextColorSet = true;
			$textColorAffectedParts = array('messageUserName');
		}

		// custom color (higher priority):
		if ($this->options->isOptionEnabled('allow_change_text_color') && $message->getUser() !== null && strlen($message->getUser()->getDataProperty('textColor')) > 0) {
			$isTextColorSet = true;
			$textColorAffectedParts = (array)$this->options->getOption("text_color_parts", array('message', 'messageUserName'));
			$textColor = $message->getUser()->getDataProperty('textColor');
		}

		$classes = '';
		$wpUser = $this->usersDAO->getWpUserByID($message->getWordPressUserId());
		if ($this->options->isOptionEnabled('css_classes_for_user_roles', false)) {
			$classes = $this->getCssClassesForUserRoles($message->getUser(), $wpUser);
		}

		$allowedToGetTheContent = $this->isUserAllowedToSeeTheContentOfMessage($message);

		$data = array(
			'baseDir' => $this->options->getBaseDir(),
			'cssClasses' => $classes,
			'messageId' => $message->getId(),
			'messageUser' => $message->getUserName(),
			'messageChatUserId' => $message->getUserId(),
			'isAuthorWpUser' => $wpUser !== null,
			'isAuthorCurrentUser' => $userId == $message->getUserId(),
			'messageTimeUTC' => gmdate('c', $message->getTime()),
			'renderedUserName' => $this->getRenderedUserName($message),
			'avatarUrl' => $this->getUserAvatarForMessage($message, $this->options->isOptionEnabled('show_avatars', false)),
			'allowedToGetTheContent' => $allowedToGetTheContent,
			'hidden' => $this->options->isOptionEnabled('new_messages_hidden', false) && $message->isHidden(),
			'messageContent' => $allowedToGetTheContent ? $this->getRenderedMessageContent($message) : '',
			'isTextColorSetForMessage' => $isTextColorSet && in_array('message', $textColorAffectedParts),
			'isTextColorSetForUserName' => $isTextColorSet && in_array('messageUserName', $textColorAffectedParts),
			'textColor' => $textColor,
			'messageApproveMessage' => $this->options->getEncodedOption('message_approve_message', 'Approve the message'),
			'messageDeleteMessage' => $this->options->getEncodedOption('message_delete_message', 'Delete the message'),
			'messageEditMessage' => $this->options->getEncodedOption('message_edit_message', 'Edit the message'),
			'messageBanThisUser' => $this->options->getEncodedOption('message_ban_this_user', 'Ban this user'),
			'messageKickThisUser' => $this->options->getEncodedOption('message_kick_this_user', 'Kick this user'),
			'messageReportSpam' => $this->options->getEncodedOption('message_report_spam', 'Report spam')
		);
		
		return $this->templater->render($data);
	}

	/**
	 * Returns text color if the color is defined for user's role.
	 *
	 * @param WiseChatUser $user
	 * @return string|null
	 */
	private function getTextColorDefinedByUserRole($user) {
		$textColor = null;
		$userRoleToColorMap = $this->options->getOption('text_color_user_roles', array());

		if ($user !== null && $user->getWordPressId() > 0) {
			$wpUser = $this->usersDAO->getWpUserByID($user->getWordPressId());
			if (is_array($wpUser->roles)) {
				$commonRoles = array_intersect($wpUser->roles, array_keys($userRoleToColorMap));
				if (count($commonRoles) > 0 && array_key_exists(0, $commonRoles) && array_key_exists($commonRoles[0], $userRoleToColorMap)) {
					$userRoleColor = trim($userRoleToColorMap[$commonRoles[0]]);
					if (strlen($userRoleColor) > 0) {
						$textColor = $userRoleColor;
					}
				}
			}
		}

		return $textColor;
	}

	/**
	 * Checks if the current user can get the message content.
	 *
	 * @param WiseChatMessage $message
	 *
	 * @return boolean
	 */
	private function isUserAllowedToSeeTheContentOfMessage($message) {
		if ($this->options->isOptionEnabled('new_messages_hidden', false) === false) {
			return true;
		}

		if (!$message->isHidden()) {
			return true;
		}

		$wpUser = $this->usersDAO->getCurrentWpUser();
		if ($wpUser !== null) {
			$targetRoles = (array) $this->options->getOption("show_hidden_messages_roles", 'administrator');
			if ((is_array($wpUser->roles) && count(array_intersect($targetRoles, $wpUser->roles)) > 0)) {
				return true;
			}
		} else {
			return false;
		}
	}

	/**
	 * @param WiseChatMessage $message
	 * @param boolean $enabled
	 *
	 * @return string|null
	 */
	private function getUserAvatarForMessage($message, $enabled) {
		if ($enabled) {
			if (strlen($message->getAvatarUrl()) > 0) {
				return $message->getAvatarUrl();
			} else {
				return $this->getUserAvatar($message->getUser(), $enabled, $message->getWordPressUserId());
			}
		}

		return null;
	}

	/**
	 * @param WiseChatUser $user
	 * @param boolean $enabled
	 * @param integer $priorityWordPressId
	 *
	 * @return string|null
	 */
	private function getUserAvatar($user, $enabled, $priorityWordPressId = null) {
		$imageSrc = null;

		if ($enabled) {
			if ($user !== null && strlen($user->getExternalId()) > 0) {
				$imageSrc = $user->getAvatarUrl();
			} else if ($priorityWordPressId > 0 || ($user !== null && $user->getWordPressId() !== null)) {
				$imageTag = $priorityWordPressId > 0 ? get_avatar($priorityWordPressId) : get_avatar($user->getWordPressId());
				
				$doc = new DOMDocument();
				@$doc->loadHTML($imageTag);
				$imageTags = $doc->getElementsByTagName('img');
				foreach($imageTags as $tag) {
					$imageSrc = $tag->getAttribute('src');
				}
			} else {
				$imageSrc = $this->options->getIconsURL().'user.png';
			}
		}

		return $imageSrc;
	}

	/**
	 * Append offline users to the list.
	 *
	 * @param WiseChatChannelUser[] $channelUsers
	 * @param WiseChatChannel $channel
	 * @param integer $bpGroupID
	 * @return WiseChatChannelUser[]
	 */
	private function appendOfflineUsers($channelUsers, $channel, $bpGroupID = null) {
		// collect map of channel users:
		$channelWPUsersMap = array();
		foreach ($channelUsers as $channelUser) {
			if ($channelUser->getUser() !== null) {
				$channelWPUsersMap[$channelUser->getUser()->getWordPressId()] = $channelUser;
			}
		}

		// append offline users:
		$usersCacheTime = $this->options->getIntegerOption('users_cache_time', 1200);
		if ($usersCacheTime > 0) {
			if (false === ($wpUsers = get_transient('wise_chat_pro_wp_users_cache'))) {
				$wpUsers = get_users('orderby=displayname');
				set_transient('wise_chat_pro_wp_users_cache', $wpUsers, $usersCacheTime);
			}
		} else {
			$wpUsers = get_users('orderby=displayname');
		}

		foreach ($wpUsers as $key => $wpUser) {
			if (array_key_exists($wpUser->ID, $channelWPUsersMap)) {
				continue;
			}

			// exclude user if it does not belong to BP group:
			if ($bpGroupID !== null && $this->options->isOptionEnabled('enable_buddypress', false) && function_exists('groups_is_user_member')) {
				if (!groups_is_user_member($wpUser->ID, $bpGroupID)) {
					continue;
				}
			}

			$chatUser = $this->usersDAO->getLatestByWordPressId($wpUser->ID);
			$channelUser = null;
			if ($chatUser === null) {
				// create an in-memory user:
				$chatUser = new WiseChatUser();
				$chatUser->setId('v' . $wpUser->ID);
				$chatUser->setName($wpUser->display_name);
				$chatUser->setWordPressId($wpUser->ID);
			} else {
				$channelUser = $this->channelUsersDAO->getInactiveByUserIdAndChannelId($chatUser->getId(), $channel->getId());
			}

			if ($channelUser === null) {
				// create in-memory channel-user association:
				$channelUser = new WiseChatChannelUser();
				$channelUser->setChannelId($channel->getId());
				$channelUser->setUser($chatUser);
				$channelUser->setActive(false);
				$channelUser->setLastActivityTime(time());
				$channelUser->setUserId($chatUser->getId());
			} else {
				$channelUser->setUserId($chatUser->getId());
				$channelUser->setUser($chatUser);
			}

			$channelUsers[] = $channelUser;
		}


		return $channelUsers;
	}
	
	/**
	* Returns rendered users list in the given channel.
	*
	* @param WiseChatChannel $channel
	* @param boolean $displayCurrentUserWhenEmpty
	* @param integer $bpGroupID
	*
	* @return string HTML source
	*/
	public function getRenderedUsersList($channel, $displayCurrentUserWhenEmpty = true, $bpGroupID = null) {
		$hideRoles = $this->options->getOption('users_list_hide_roles', array());
		$channelUsers = $this->channelUsersDAO->getAllActiveByChannelId($channel->getId());
		$isCurrentUserPresent = false;
		$userId = $this->authentication->getUserIdOrNull();

		if ($this->options->isOptionEnabled('users_list_offline_enable', false)) {
			$channelUsers = $this->appendOfflineUsers($channelUsers, $channel, $bpGroupID);
		}

		$usersList = array();
		foreach ($channelUsers as $channelUser) {
			if ($channelUser->getUser() == null) {
				continue;
			}

			if (!$this->userService->isUsersConnectionAvailable($this->authentication->getUser(), $channelUser->getUser())) {
				continue;
			}

			// do not render anonymous users:
			if ($this->service->isChatAllowedForWPUsersOnly() && !($channelUser->getUser()->getWordPressId() > 0)) {
				continue;
			}

			// hide chosen roles:
			$wpUser = null;
			if (is_array($hideRoles) && count($hideRoles) > 0 && $channelUser->getUser()->getWordPressId() > 0) {
				$wpUser = $this->usersDAO->getWpUserByID($channelUser->getUser()->getWordPressId());
				if (is_array($wpUser->roles) && count(array_intersect($hideRoles, $wpUser->roles)) > 0) {
					continue;
				}
			}

            // TB.. do not render offline users
				if (! $channelUser->isActive()) {
                    continue;
                }
            
			// do not render anonymous users if it is not WP user and externally logged in:
			if ($this->options->isOptionEnabled('users_list_hide_anonymous', false) &&
				!($channelUser->getUser()->getWordPressId() > 0) &&
				strlen($channelUser->getUser()->getExternalType()) == 0
			) {
				continue;
			}

			$styles = '';

			// text color defined by role:
			$textColor = $this->getTextColorDefinedByUserRole($channelUser->getUser());

			// custom text color:
			if ($this->options->isOptionEnabled('allow_change_text_color')) {
				$textColorProposal = $channelUser->getUser()->getDataProperty('textColor');
				if (strlen($textColorProposal) > 0) {
					$textColor = $textColorProposal;
				}
			}
			if (strlen($textColor) > 0) {
				$styles = sprintf('style="color: %s"', $textColor);
			}

			$avatarHtml = '';
			if ($this->options->isOptionEnabled('show_users_list_avatars', false)) {
				$avatarHtml = sprintf('<img src="%s" class="wcUserListAvatar" />', $this->getUserAvatar($channelUser->getUser(), true));
			}

			$userClassName = '';
			if ($userId == $channelUser->getUserId()) {
				$isCurrentUserPresent = true;
				$userClassName = 'wcCurrentUser';
			}

			// add roles as css classes:
			if ($this->options->isOptionEnabled('css_classes_for_user_roles', false)) {
				$userClassName .= ' '.$this->getCssClassesForUserRoles($channelUser->getUser(), $wpUser);
				$userClassName = trim($userClassName);
			}

			$activityFlagHtml = '';
			if ($this->options->isOptionEnabled('show_users_online_offline_mark', true)) {
				$activityFlagHtml = '<span class="wcUserActivityFlag"></span>';
				if ($channelUser->isActive()) {
					$userClassName .= ' wcUserActive';
				} else {
					$userClassName .= ' wcUserInactive';
				}
			}

            $flag = '';
            if ($this->options->isOptionEnabled('collect_user_stats', true) && $this->options->isOptionEnabled('show_users_flags', false)) {
                $countryCode = $channelUser->getUser()->getDataProperty('countryCode');
                $country = $channelUser->getUser()->getDataProperty('country');
                if (strlen($countryCode) > 0) {
                    $flagURL = $this->options->getFlagURL(strtolower($countryCode));
                    $flag = " <img src='{$flagURL}' class='wcUsersListFlag wcIcon' alt='{$countryCode}' title='{$country}'/>";
                }
            }
            $cityAndCountry = '';
            if ($this->options->isOptionEnabled('collect_user_stats', true) && $this->options->isOptionEnabled('show_users_city_and_country', false)) {
                $cityAndCountryArray = array();
                $city = $channelUser->getUser()->getDataProperty('city');
                if (strlen($city) > 0) {
                    $cityAndCountryArray[] = $city;
                }

                $countryCode = $channelUser->getUser()->getDataProperty('countryCode');
                if (strlen($countryCode) > 0) {
                    $cityAndCountryArray[] = $countryCode;
                }

                if (count($cityAndCountryArray) > 0) {
                    $cityAndCountry = ' <span class="wcUsersListCity">'.implode(', ', $cityAndCountryArray).'</span>';
                }
            }

			$publicID = $this->getUserPublicIdForChannel($channelUser->getUser(), $channel);
			$userIdHash = WiseChatUserService::getUserHash($channelUser->getUser()->getId());
			$encodedName = htmlspecialchars($channelUser->getUser()->getName(), ENT_QUOTES, 'UTF-8', false);
			$infoWindow = $this->options->isOptionEnabled('show_users_list_info_windows', true) ? $this->getRenderedInfoWindow($channelUser->getUser()) : '';
			if ($this->options->isOptionEnabled('enable_private_messages', false) || !$this->options->isOptionEnabled('users_list_linking', false)) {
				$isAllowed = $this->privateMessagesRulesService->isMessageDeliveryAllowed($this->authentication->getUser(), $channelUser->getUser()) ? 1 : 0;

				$usersList[] = sprintf(
					'<a href="javascript://" data-info-window="%s" data-allowed="%d" data-public-id="%s" data-hash="%s" data-wp-id="%s" data-name="%s" class="wcUserInChannel %s" %s>%s %s</a>',
					htmlentities($infoWindow), $isAllowed, $publicID, $userIdHash, $channelUser->getUser()->getWordPressId(), $encodedName, $userClassName, $styles, $activityFlagHtml . $avatarHtml . $encodedName,
					$flag . $cityAndCountry
				);
			} else if ($this->options->isOptionEnabled('users_list_linking', false)) {
				$usersList[] = $this->getRenderedUserNameInternal(
					$encodedName, $channelUser->getUser()->getWordPressId(), $channelUser->getUser(), 'wcUserInChannel ' . $userClassName,
					$activityFlagHtml . $avatarHtml . $encodedName . ' ' . $flag . $cityAndCountry, true
				);
			}
		}
		
		if ($displayCurrentUserWhenEmpty && !$isCurrentUserPresent && $this->authentication->isAuthenticated()) {
			$hidden = false;
			if (is_array($hideRoles) && count($hideRoles) > 0 && $this->authentication->getUser()->getWordPressId() > 0) {
				$wpUser = $this->usersDAO->getWpUserByID($this->authentication->getUser()->getWordPressId());
				if (is_array($wpUser->roles) && count(array_intersect($hideRoles, $wpUser->roles)) > 0) {
					$hidden = true;
				}
			}

			if (!$hidden && (
					!$this->options->isOptionEnabled('users_list_hide_anonymous', false) ||
					$this->authentication->getUser()->getWordPressId() > 0 ||
					$this->authentication->isAuthenticatedExternally()
				)
			) {
				$publicID = $this->getUserPublicIdForChannel($this->authentication->getUser(), $channel);
				$userIdHash = WiseChatUserService::getUserHash($this->authentication->getUser()->getId());
				array_unshift(
					$usersList, sprintf(
						'<a href="javascript://"  data-info-window="" data-public-id="%s" data-hash="%s" data-wp-id="%s" data-name="%s" class="wcUserInChannel wcCurrentUser">%s</a>',
						$publicID, $userIdHash, $this->authentication->getUser()->getWordPressId(), $this->authentication->getUserNameOrEmptyString(), $this->authentication->getUserNameOrEmptyString()
					)
				);
			}
		}

		$html = implode("\n", $usersList);

		/**
		 * Filters HTML code of the users list.
		 *
		 * @since 2.3.2
		 *
		 * @param string $html A fully rendered HTML code of the users list
		 * @param WiseChatChannel $channel The channel
		 * @param WiseChatChannelUser[] $channelUsers The users to render
	 	*/
		return apply_filters('wc_users_list_html', $html, $channel, $channelUsers);
	}

	/**
	 * @param WiseChatUser $user
	 *
	 * @return string
	 */
	private function getRenderedInfoWindow($user) {
		global $wp_roles;

		$templateDefault = "{avatar}\n".
			"{profileLink}<br />\n".
			"{role}<br />\n".
			"{privateMessageButton}";
		$template = $this->options->getOption('users_list_info_windows_template', $templateDefault);
		$variables = array();

		// avatar:
		$variables['avatar'] =
			$this->options->isOptionEnabled('show_users_list_avatars', false)
			? sprintf('<img src="%s" class="wcUserListInfoWindowAvatar" />', $this->getUserAvatar($user, true))
			: '';

		// profile link:
		$profileLink = $this->getUserProfileLink($user);
		$variables['profileLink'] =
			$profileLink !== null
				? sprintf('<a href="%s" rel="noopener noreferrer nofollow" class="wcUserListInfoWindowUserName wcUserListInfoWindowAvatarProfileLink">%s</a>', $profileLink, $user->getName())
				: sprintf('<span class="wcUserListInfoWindowUserName">%s</span>', $user->getName());
		$variables['profileLinkInNewWindow'] = str_replace('<a', '<a target="_blank"', $variables['profileLink']);

		// profile URL:
		$variables['profileURL'] = $profileLink !== null ? $profileLink : '';

		// role
		$wpUserMeta = $user->getWordPressId() > 0 ? get_userdata($user->getWordPressId()) : null;
		$wpUserRoles = $wpUserMeta !== null ? $wpUserMeta->roles : null;
		if ($wpUserRoles !== null && is_array($wpUserRoles) && is_array($wp_roles->roles)) {
			foreach ($wpUserRoles as $key => $role) {
				$wpUserRoles[$key] = array_key_exists($role, $wp_roles->roles) ? $wp_roles->roles[$role]['name'] : $role;
			}

			$variables['role'] = reset($wpUserRoles);
			$variables['roles'] = implode(', ', $wpUserRoles);
		} else if (strlen($user->getExternalType()) > 0) {
			switch ($user->getExternalType()) {
				case 'fb':
					$variables['role'] = $variables['roles'] = $this->options->getOption('message_facebook_user', 'Facebook user');
					break;
				case 'tw':
					$variables['role'] = $variables['roles'] = $this->options->getOption('message_twitter_user', 'Twitter user');
					break;
				case 'go':
					$variables['role'] = $variables['roles'] = $this->options->getOption('message_google_user', 'Google user');
					break;
				default:
					$variables['role'] = $variables['roles'] = $this->options->getOption('message_anonymous_user', 'Anonymous user');
			}

		} else {
			$variables['role'] = $variables['roles'] = $this->options->getOption('message_anonymous_user', 'Anonymous user');
		}
		$variables['role'] = sprintf('<span class="wcUserListInfoWindowRoles">%s</span>', $variables['role']);
		$variables['roles'] = sprintf('<span class="wcUserListInfoWindowRoles">%s</span>', $variables['roles']);

		// private message button:
		$userIdHash = WiseChatUserService::getUserHash($user->getId());
		$variables['privateMessageButton'] =
			$this->options->isOptionEnabled('enable_private_messages', false) && $this->authentication->getUserIdOrNull() != $user->getId()
				? sprintf(
					'<button class="wcUserListInfoWindowPrivateMessageButton" data-hash="%s">%s</button>', $userIdHash, $this->options->getOption('message_send_a_message', 'Send a message')
				)
				: '';

		// basic variables:
		$variables['id'] = '';
		$variables['username'] = '';
		$variables['displayname'] = '';
		if ($user->getWordPressId() > 0) {
			$wpUser = $this->usersDAO->getWpUserByID($user->getWordPressId());
			if ($wpUser !== null) {
				$variables['id'] = $wpUser->ID;
				$variables['username'] = $wpUser->user_login;
				$variables['displayname'] = $wpUser->display_name;
			}
		}

		$html = $this->getTemplatedString($variables, $template, false);

		/**
		 * Filters HTML code of the user info window popup displayed when hovering username on the users list.
		 *
		 * @since 2.3.2
		 *
		 * @param string $html A fully rendered HTML code of the user info window
		 * @param WiseChatUser $user The user
		 */
		return apply_filters('wc_user_info_window_html', $html, $user);
	}

	/**
	 * Returns user's public ID. It is an encrypted combination of user's ID and channel's ID.
	 *
	 * @param WiseChatUser $user
	 * @param WiseChatChannel $channel
	 * @return string
	 */
	public function getUserPublicIdForChannel($user, $channel) {
		$publicIdData = array($user->getId(), $channel->getId());

		return base64_encode(WiseChatCrypt::encrypt(serialize($publicIdData)));
	}

	/**
	 * Returns rendered user name for given message.
	 *
	 * @param WiseChatMessage $message
	 *
	 * @return string HTML source
	 */
	public function getRenderedUserName($message) {
		return $this->getRenderedUserNameInternal($message->getUserName(), $message->getWordPressUserId(), $message->getUser());
	}
	
	/**
	* Returns rendered user name.
	*
	* @param string $userName
	* @param integer $wordPressUserId
	* @param WiseChatUser $user
	* @param string $className
	* @param string $customUserName
	* @param boolean $makeAlwaysLink
	*
	* @return string HTML source
	*/
	public function getRenderedUserNameInternal($userName, $wordPressUserId, $user, $className = '', $customUserName = null, $makeAlwaysLink = false) {
		$formattedUserName = $userName;
		$displayMode = $this->options->getIntegerOption('link_wp_user_name', 0);
		$styles = '';
		$textColorAffectedParts = array();

		// text color defined by role:
		$textColor = $this->getTextColorDefinedByUserRole($user);
		if (strlen($textColor) > 0) {
			$textColorAffectedParts = array('messageUserName');
		}

		// custom color (higher priority):
		if ($this->options->isOptionEnabled('allow_change_text_color') && $user !== null && strlen($user->getDataProperty('textColor')) > 0) {
			$textColorAffectedParts = (array)$this->options->getOption("text_color_parts", array('message', 'messageUserName'));
			$textColor = $user->getDataProperty('textColor');
		}

		if (strlen($textColor) > 0 && in_array('messageUserName', $textColorAffectedParts)) {
			$styles = sprintf('style="color: %s"', $textColor);
		}


		if ($displayMode === 1) {
			$userNameLink = $this->getUserProfileLink($user, $userName, $wordPressUserId);

			if ($customUserName != null) {
				$formattedUserName = $customUserName;
			}
			
			if ($userNameLink != null) {
				$formattedUserName = sprintf(
					"<a href='%s' target='_blank' class='%s' rel='noopener noreferrer nofollow' %s>%s</a>", $userNameLink, $className, $styles, $formattedUserName
				);
			} else if ($makeAlwaysLink) {
				$formattedUserName = sprintf(
					"<a href='javascript://' class='%s' %s>%s</a>", $className, $styles, $formattedUserName
				);
			}
		} else if ($displayMode === 2) {
            $replyTag = '@'.$formattedUserName.':';
            $title = htmlspecialchars($this->options->getOption('message_insert_into_message', 'Insert into message').': '.$replyTag, ENT_COMPAT);

			if ($customUserName != null) {
				$formattedUserName = $customUserName;
			}

            $formattedUserName = sprintf(
                "<a href='javascript://' class='wcMessageUserReplyTo %s' data-name='%s' %s title='%s'>%s</a>", $className, $userName, $styles, $title, $formattedUserName
            );
        } else if ($makeAlwaysLink) {
			if ($customUserName != null) {
				$formattedUserName = $customUserName;
			}

			$formattedUserName = sprintf(
				"<a href='javascript://' class='%s' %s>%s</a>", $className, $styles, $formattedUserName
			);
		}
		
		return $formattedUserName;
	}

	/**
	 * @param WiseChatUser $user
	 * @param string $userName
	 * @param integer $wordPressUserId
	 *
	 * @return string
	 */
	private function getUserProfileLink($user, $userName = null, $wordPressUserId = null) {
		$linkUserNameTemplate = $this->options->getOption('link_user_name_template', null);
		if ($wordPressUserId == null && $user != null) {
			$wordPressUserId = $user->getWordPressId();
		}
		if ($userName == null && $user != null) {
			$userName = $user->getName();
		}
		$wpUser = $wordPressUserId != null ? $this->usersDAO->getWpUserByID($wordPressUserId) : null;

		$variableId = '';
		$variableUserName = $variableDisplayName = $userName;
		if ($user !== null && strlen($user->getExternalType()) > 0) {
			$variableId = $user->getExternalId();
		} else if ($wpUser !== null) {
			$variableId = $wpUser->ID;
			$variableUserName = $wpUser->user_login;
			$variableDisplayName = $wpUser->display_name;
		}

		$profileLink = null;
		if ($linkUserNameTemplate != null) {
			$variables = array(
				'id' => $variableId,
				'username' => $variableUserName,
				'displayname' => $variableDisplayName
			);

			$profileLink = $this->getTemplatedString($variables, $linkUserNameTemplate);
		} else if ($user !== null && strlen($user->getExternalType()) > 0) {
			$profileLink = $user->getProfileUrl();
		} else if ($wpUser !== null) {
			$profileLink = get_author_posts_url($wpUser->ID);
		}

		return $profileLink;
	}
	
	/**
	* Returns rendered channel statistics.
	*
	* @param WiseChatChannel $channel
	*
	* @return string HTML source
	*/
	public function getRenderedChannelStats($channel) {
		if ($channel === null) {
			return 'ERROR: channel does not exist';
		}

		$variables = array(
			'channel' => $channel->getName(),
			'messages' => $this->messagesService->getNumberByChannelName($channel->getName()),
			'users' => $this->channelUsersDAO->getAmountOfUsersInChannel($channel->getId())
		);
	
		return $this->getTemplatedString($variables, $this->options->getOption('template', 'ERROR: TEMPLATE NOT SPECIFIED'));
	}
	
	/**
	* Returns rendered message content.
	*
	* @param WiseChatMessage $message
	*
	* @return string HTML source
	*/
	private function getRenderedMessageContent($message) {
		$formattedMessage = htmlspecialchars($message->getText(), ENT_QUOTES, 'UTF-8');

        /** @var WiseChatLinksPostFilter $linksFilter */
        $linksFilter = WiseChatContainer::get('rendering/filters/post/WiseChatLinksPostFilter');
		$formattedMessage = $linksFilter->filter(
            $formattedMessage,
            $this->options->isOptionEnabled('allow_post_links')
        );

        /** @var WiseChatAttachmentsPostFilter $attachmentsFilter */
        $attachmentsFilter = WiseChatContainer::get('rendering/filters/post/WiseChatAttachmentsPostFilter');
		$formattedMessage = $attachmentsFilter->filter(
			$formattedMessage,
            $this->options->isOptionEnabled('enable_attachments_uploader'),
            $this->options->isOptionEnabled('allow_post_links')
		);

        /** @var WiseChatImagesPostFilter $imagesFilter */
        $imagesFilter = WiseChatContainer::get('rendering/filters/post/WiseChatImagesPostFilter');
        $formattedMessage = $imagesFilter->filter(
			$formattedMessage,
            $this->options->isOptionEnabled('allow_post_images'),
            $this->options->isOptionEnabled('allow_post_links')
		);

        /** @var WiseChatYouTubePostFilter $youTubeFilter */
        $youTubeFilter = WiseChatContainer::get('rendering/filters/post/WiseChatYouTubePostFilter');
		$formattedMessage = $youTubeFilter->filter(
			$formattedMessage,
            $this->options->isOptionEnabled('enable_youtube'),
            $this->options->isOptionEnabled('allow_post_links'),
			$this->options->getIntegerOption('youtube_width', 186),
            $this->options->getIntegerOption('youtube_height', 105)
		);
		
		if ($this->options->isOptionEnabled('enable_twitter_hashtags')) {
            /** @var WiseChatHashtagsPostFilter $hashTagsFilter */
            $hashTagsFilter = WiseChatContainer::get('rendering/filters/post/WiseChatHashtagsPostFilter');
			$formattedMessage = $hashTagsFilter->filter($formattedMessage);
		}

		$emoticonsSet = $this->options->getIntegerOption('emoticons_enabled', 1);
		if ($emoticonsSet > 0 || $this->options->isOptionEnabled('custom_emoticons_enabled', false)) {
            /** @var WiseChatEmoticonsFilter $emoticonsFilter */
            $emoticonsFilter = WiseChatContainer::get('rendering/filters/post/WiseChatEmoticonsFilter');
            $formattedMessage = $emoticonsFilter->filter($formattedMessage, $emoticonsSet);
		}
		
		$formattedMessage = str_replace("\n", '<br />', $formattedMessage);
		
		return $formattedMessage;
	}
	
	private function getTemplatedString($variables, $template, $encodeValues = true) {
		foreach ($variables as $key => $value) {
			$template = str_replace("{".$key."}", $encodeValues ? urlencode($value) : $value, $template);
		}
		
		return $template;
	}

	/**
	 * Returns CSS classes for user roles.
	 *
	 * @param WiseChatUser $user
	 *
	 * @return string
	 */
	private function getCssClassesForUserRoles($user, $wpUser = null) {
		$classes = array();

		if ($user === null) {
			if ($wpUser !== null && is_array($wpUser->roles)) {
				foreach ($wpUser->roles as $role) {
					$classes[] = 'wcUserRole-' . $role;
				}
			} else {
				$classes[] = 'wcUserRoleAnonymous';
			}
		} else {
			if ($user->getWordPressId() > 0) {
				if ($wpUser === null) {
					$wpUser = $this->usersDAO->getWpUserByID($user->getWordPressId());
				}
				if (is_array($wpUser->roles)) {
					foreach ($wpUser->roles as $role) {
						$classes[] = 'wcUserRole-' . $role;
					}
				}
			} else if (strlen($user->getExternalType()) > 0) {
				$classes[] = 'wcUserRoleExternal-' . $user->getExternalType();
			} else {
				$classes[] = 'wcUserRoleAnonymous';
			}
		}

		return implode(' ', $classes);
	}
}

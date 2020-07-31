<?php

/**
 * WiseChat core class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChat {
	
	/**
	* @var WiseChatOptions
	*/
	private $options;
	
	/**
	* @var WiseChatUsersDAO
	*/
	private $usersDAO;
	
	/**
	* @var WiseChatUserSettingsDAO
	*/
	private $userSettingsDAO;
	
	/**
	* @var WiseChatChannelUsersDAO
	*/
	private $channelUsersDAO;
	
	/**
	* @var WiseChatActionsDAO
	*/
	private $actionsDAO;

	/**
	 * @var WiseChatEmoticonsDAO
	 */
	private $emoticonsDAO;
	
	/**
	* @var WiseChatRenderer
	*/
	private $renderer;
	
	/**
	* @var WiseChatCssRenderer
	*/
	private $cssRenderer;
	
	/**
	* @var WiseChatBansService
	*/
	private $bansService;
	
	/**
	* @var WiseChatUserService
	*/
	private $userService;
	
	/**
	* @var WiseChatMessagesService
	*/
	private $messagesService;
	
	/**
	* @var WiseChatService
	*/
	private $service;
	
	/**
	* @var WiseChatAttachmentsService
	*/
	private $attachmentsService;

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
	* @var array
	*/
	private $shortCodeOptions;
	
	public function __construct() {
		$this->options = WiseChatOptions::getInstance();
		$this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
		$this->userSettingsDAO = WiseChatContainer::get('dao/user/WiseChatUserSettingsDAO');
		$this->channelUsersDAO = WiseChatContainer::get('dao/WiseChatChannelUsersDAO');
		$this->actionsDAO = WiseChatContainer::get('dao/WiseChatActionsDAO');
		$this->emoticonsDAO = WiseChatContainer::getLazy('dao/WiseChatEmoticonsDAO');
		$this->renderer = WiseChatContainer::get('rendering/WiseChatRenderer');
		$this->cssRenderer = WiseChatContainer::get('rendering/WiseChatCssRenderer');
		$this->bansService = WiseChatContainer::get('services/WiseChatBansService');
		$this->userService = WiseChatContainer::get('services/user/WiseChatUserService');
		$this->messagesService = WiseChatContainer::get('services/WiseChatMessagesService');
		$this->service = WiseChatContainer::get('services/WiseChatService');
		$this->attachmentsService = WiseChatContainer::get('services/WiseChatAttachmentsService');
		$this->authentication = WiseChatContainer::getLazy('services/user/WiseChatAuthentication');
		$this->externalAuthentication = WiseChatContainer::getLazy('services/user/WiseChatExternalAuthentication');
		$this->httpRequestService = WiseChatContainer::getLazy('services/WiseChatHttpRequestService');
		WiseChatContainer::load('WiseChatCrypt');
		WiseChatContainer::load('WiseChatThemes');
		WiseChatContainer::load('rendering/WiseChatTemplater');
		
		$this->userService->initMaintenance();
		$this->shortCodeOptions = array();
	}
	
	/*
	* Enqueues all necessary resources (scripts or styles).
	*/
	public function registerResources() {
		$pluginBaseURL = $this->options->getBaseDir();

		wp_enqueue_script('wisechat_utils', $pluginBaseURL.'js/wisechat/utils.js', array('jquery'));
		wp_enqueue_script('wisechat_engines', $pluginBaseURL.'js/wisechat/engines.js', array('jquery'));
		wp_enqueue_script('wisechat_settings', $pluginBaseURL.'js/wisechat/settings.js', array('jquery'));
		wp_enqueue_script('wisechat_pm', $pluginBaseURL.'js/wisechat/pm.js', array('jquery'));
		wp_enqueue_script('wisechat_ui_core', $pluginBaseURL.'js/wisechat/ui-core.js', array('jquery'));
		wp_enqueue_script('wisechat_ui_controls', $pluginBaseURL.'js/wisechat/ui-controls.js', array('jquery'));
		wp_enqueue_script('wisechat_ui_sidebar', $pluginBaseURL.'js/wisechat/ui-sidebar.js', array('jquery'));
		wp_enqueue_script('wisechat_maintenance', $pluginBaseURL.'js/wisechat/maintenance.js', array('jquery'));
		wp_enqueue_script('wisechat_core', $pluginBaseURL.'js/wisechat/core.js', array('jquery'));

		if ($this->options->isOptionEnabled('allow_change_text_color')) {
			wp_enqueue_script('wise_chat_3rdparty_jscolorPicker', $pluginBaseURL.'js/3rdparty/jquery.colorPicker.min.js', array('jquery'));
			wp_enqueue_style('wise_chat_3rdparty_jscolorPicker', $pluginBaseURL.'css/3rdparty/colorPicker.css');
		}

		wp_enqueue_script('wise_chat_3rdparty_momentjs', $pluginBaseURL.'js/3rdparty/moment.patched.min.js', array('jquery'));
	}

	/**
	 * Shortcode backend function: [wise-chat]
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function getRenderedShortcode($attributes) {
		if (!is_array($attributes)) {
			$attributes = array();
		}
		$attributes['channel'] = $this->service->getValidChatChannelName(
			array_key_exists('channel', $attributes) ? $attributes['channel'] : ''
		);
		
		$this->options->replaceOptions($attributes);
		$this->shortCodeOptions = $attributes;
   
		return $this->getRenderedChat($attributes['channel']);
	}

	/**
	 * Shortcode backend function: [wise-chat]
	 *
	 * @param array $attributes
	 * @return string
	 */
	public function getRenderedChannelUsersShortcode($attributes) {
		if (!is_array($attributes)) {
			$attributes = array();
		}
		$attributes['channel'] = $this->service->getValidChatChannelName(
			array_key_exists('channel', $attributes) ? $attributes['channel'] : ''
		);
		$attributes['chat_height'] = '';
		$attributes['users_list_offline_enable'] = '0';
		$this->options->replaceOptions($attributes);
		$this->shortCodeOptions = $attributes;
		$chatId = $this->service->getChatID();
		$channel = $this->service->createAndGetChannel($this->service->getValidChatChannelName($attributes['channel']));
		$this->userService->refreshChannelUsersData();
		$templater = new WiseChatTemplater($this->options->getPluginBaseDir());
		$templater->setTemplateFile(WiseChatThemes::getInstance()->getChannelUsersWidgetTemplate());
		$data = array(
			'chatId' => $chatId,
			'baseDir' => $this->options->getBaseDir(),
			'title' => $attributes['title'],
			'themeStyles' => $this->options->getBaseDir().WiseChatThemes::getInstance()->getCss(),
			'usersList' => $this->renderer->getRenderedUsersList($channel, false),
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),
			'messageUsersListEmpty' => $this->options->getEncodedOption('message_users_list_empty', 'No users in the channel'),
		);
		$data = array_merge($data, $this->userSettingsDAO->getAll());
		if ($this->authentication->isAuthenticated()) {
			$data = array_merge($data, $this->authentication->getUser()->getData());
		}

		$html = $templater->render($data);

		/**
		 * Filters HTML outputted by the channel users widget.
		 *
		 * @since 2.3.2
		 *
		 * @param string $html A HTML code outputted by the channel users widget
		 * @param array $data The rendering input data
		 */
		return apply_filters('wc_chat_channel_users_html', $html, $data);
	}

	/**
	 * Returns chat HTML for given channel.
	 *
	 * @param string|null $channelName
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getRenderedChat($channelName = null) {
		$this->requireComposerClassLoader();

		$channel = $this->service->createAndGetChannel($this->service->getValidChatChannelName($channelName));

		// saves users list in session for this channel (it will be updated in maintenance task):
		if ($this->options->isOptionEnabled('enable_leave_notification', true) || strlen($this->options->getOption('leave_sound_notification')) > 0) {
			$this->userService->clearUsersListInSession($channel, WiseChatUserService::USERS_LIST_CATEGORY_ABSENT);
		}
		if ($this->options->isOptionEnabled('enable_join_notification', true) || strlen($this->options->getOption('join_sound_notification')) > 0) {
			$this->userService->persistUsersListInSession($channel, WiseChatUserService::USERS_LIST_CATEGORY_NEW);
		}

		if ($this->service->isIpKicked()) {
			return $this->renderer->getRenderedAccessDenied(
				$channel, $this->options->getOption('message_error_12', 'You are blocked from using the chat'), 'wcAccessDenied'
			);
		}

		if ($this->service->isChatRestrictedForAnonymousUsers()) {
			return $this->renderer->getRenderedAccessDenied(
				$channel, $this->options->getOption('message_error_4', 'Only logged in users are allowed to enter the chat'), 'wcAccessDenied'
			);
		}

		if ($this->service->isChatRestrictedForCurrentUserRole()) {
			return $this->renderer->getRenderedAccessDenied(
				$channel, $this->options->getOption('message_error_11', 'You are not allowed to enter the chat.'), 'wcAccessDenied'
			);
		}
		
		if (!$this->service->isChatOpen()) {
			return $this->renderer->getRenderedAccessDenied(
				$channel, $this->options->getOption('message_error_5', 'The chat is closed now'), 'wcChatClosed'
			);
		}
		
		if ($this->service->isChatChannelFull($channel)) {
			return $this->renderer->getRenderedAccessDenied(
				$channel, $this->options->getOption('message_error_6', 'The chat is full now. Try again later.'), 'wcChatFull'
			);
		}
		
		if ($this->service->isChatChannelsLimitReached($channel)) {
			return $this->renderer->getRenderedAccessDenied(
				$channel, $this->options->getOption('message_error_10', 'You cannot enter the chat due to the limit of channels you can participate simultaneously.'), 'wcChatChannelLimitFull'
			);
		}

		$redirectURL = null;
		if ($this->service->hasUserToBeForcedToEnterName()) {
			if ($this->getPostParam('wcUserNameSelection') !== null) {
				try {
					$user = $this->authentication->authenticate($this->getPostParam('wcUserName'));

					/**
					 * Fires once user has started its session in the chat.
					 *
					 * @since 2.3.2
					 *
					 * @param WiseChatUser $user The user object
					 */
					do_action("wc_user_session_started", $user);

					$redirectURL = $this->httpRequestService->getCurrentURL();
				} catch (Exception $e) {
					return $this->renderer->getRenderedUserNameForm($channel, $e->getMessage());
				}
			} else {
				return $this->renderer->getRenderedUserNameForm($channel);
			}
		} else if ($this->service->hasUserToBeAuthenticatedExternally()) {
			if ($this->getParam('wcExternalLogin')) {
				try {
					$redirectURL = $this->externalAuthentication->authenticate($this->getParam('wcExternalLogin'));
				} catch (Exception $e) {
					return $this->renderer->getRenderedExternalAuthentication($channel, $e->getMessage());
				}
			} else if ($this->getParam('wcAnonymousLogin') && $this->options->isOptionEnabled('anonymous_login_enabled', true)) {
				try {
					$user = $this->authentication->authenticateAnonymously();

					/**
					 * Fires once user has started its session in the chat.
					 *
					 * @since 2.3.2
					 *
					 * @param WiseChatUser $user The user object
					 */
					do_action("wc_user_session_started", $user);

					$redirectURL = $this->httpRequestService->getCurrentURLWithoutParameters(array('wcAnonymousLogin'));
				} catch (Exception $e) {
					return $this->renderer->getRenderedExternalAuthentication($channel, $e->getMessage());
				}
			} else {
				return $this->renderer->getRenderedExternalAuthentication($channel);
			}
		}
		
		if ($this->service->hasUserToBeAuthorizedInChannel($channel)) {
			if ($this->getPostParam('wcChannelAuthorization') !== null) {
				if (!$this->service->authorize($channel, $this->getPostParam('wcChannelPassword'))) {
					return $this->renderer->getRenderedPasswordAuthorization($channel, $this->options->getOption('message_error_9', 'Invalid password.'));
				} else {
					$redirectURL = $this->httpRequestService->getCurrentURL();
				}
			} else {
				return $this->renderer->getRenderedPasswordAuthorization($channel);
			}
		}

		$chatId = $this->service->getChatID();
		
		$this->userService->startUpMaintenance($channel);
		$this->bansService->startUpMaintenance();
		$this->messagesService->startUpMaintenance($channel);

		$messages = $this->messagesService->getAllByChannelNameAndOffset($channel->getName());
		$renderedMessages = '';
		$lastId = 0;
		foreach ($messages as $message) {
			// omit non-admin messages:
			if ($message->isAdmin() && !$this->usersDAO->isWpUserAdminLogged()) {
				continue;
			}
			if (!($message->getRecipientId() > 0)) {
				$renderedMessages .= $this->renderer->getRenderedMessage($message, $this->authentication->getUserIdOrNull());
			}
			
			if ($lastId < $message->getId()) {
				$lastId = $message->getId();
			}
		}
		
		$lastAction = $this->actionsDAO->getLast();
		$jsOptions = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'channelName' => $channel->getName(),
			'nowTime' => gmdate('c', time()),
			'theme' => $this->options->getEncodedOption('theme', ''),
			'lastId' => $lastId,
			'isMultisite' => is_multisite(),
			'blogId' => get_current_blog_id(),
			'checksum' => $this->getCheckSum(),
			'lastActionId' => $lastAction !== null ? $lastAction->getId() : 0,
			'baseDir' => $this->options->getBaseDir(),
            'emoticonsBaseURL' => $this->options->getEmoticonsBaseURL(),
			'apiEndpointBase' => $this->getEndpointBase(),
			'apiMessagesEndpointBase' => $this->getMessagesEndpointBase(),
			'apiWPEndpointBase' => $this->getWPEndpointBase(),
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'messagesRefreshTime' => intval($this->options->getEncodedOption('messages_refresh_time', 3000)),
			'messagesOrder' => $this->options->getEncodedOption('messages_order', '') == 'descending' ? 'descending' : 'ascending',
			'enableTitleNotifications' => $this->options->isOptionEnabled('enable_title_notifications'),
			'enablePrivateMessages' => $this->options->isOptionEnabled('enable_private_messages'),
			'privateMessageConfirmation' => $this->options->isOptionEnabled('private_message_confirmation', true),
			'soundNotification' => $this->options->getEncodedOption('sound_notification'),
			'messagesTimeMode' => $this->options->getEncodedOption('messages_time_mode'),
			'messagesDateFormat' => trim($this->options->getEncodedOption('messages_date_format')),
			'messagesTimeFormat' => trim($this->options->getEncodedOption('messages_time_format')),
			'channelUsersLimit' => $this->options->getIntegerOption('channel_users_limit', 0),
			'messages' => array(
				'message_sending' => $this->options->getEncodedOption('message_sending', 'Sending ...'),
				'hint_message' => $this->options->getEncodedOption('hint_message'),
				'messageSecAgo' => $this->options->getEncodedOption('message_sec_ago', 'sec. ago'),
				'messageMinAgo' => $this->options->getEncodedOption('message_min_ago', 'min. ago'),
				'messageYesterday' => $this->options->getEncodedOption('message_yesterday', 'yesterday'),
				'messageUnsupportedTypeOfFile' => $this->options->getEncodedOption('message_error_7', 'Unsupported type of file.'),
				'messageSizeLimitError' => $this->options->getEncodedOption('message_error_8', 'The size of the file exceeds allowed limit.'),
				'messageInputTitle' => $this->options->getEncodedOption('message_input_title', 'Use Shift+ENTER in order to move to the next line.'),
				'messageHasLeftTheChannel' => $this->options->getEncodedOption('message_has_left_the_channel', 'has left the channel'),
				'messageSpamReportQuestion' => $this->options->getEncodedOption('message_text_1', 'Are you sure you want to report the message as spam?'),
				'messageHasJoinedTheChannel' => $this->options->getEncodedOption('message_has_joined_the_channel', 'has joined the channel'),
				'messageYes' => $this->options->getEncodedOption('message_yes', 'Yes'),
				'messageNo' => $this->options->getEncodedOption('message_no', 'No'),
				'messageOk' => $this->options->getEncodedOption('message_ok', 'OK'),
				'messageIgnoreUser' => $this->options->getEncodedOption('message_ignore_user', 'Ignore this user'),
				'messageInformation' => $this->options->getEncodedOption('message_information', 'Information'),
				'messageInvitation' => $this->options->getEncodedOption('message_invitation', 'Invitation'),
				'messageMaximize' => $this->options->getEncodedOption('message_maximize', 'Maximize'),
				'messageMinimize' => $this->options->getEncodedOption('message_minimize', 'Minimize'),
				'messageClose' => $this->options->getEncodedOption('message_close', 'Close'),
				'messageNoRecentChats' => $this->options->getEncodedOption('message_no_recent_chats', 'No recent chats'),
				'messageUserNotFoundInChat' => $this->options->getEncodedOption('message_user_not_found_in_chat', 'The user is not in the chat'),
				'messageInfo1' => $this->options->getEncodedOption('message_info_1', 'This user is ignored by you. Would you like to stop ignoring this user?'),
				'messageInfo2' => $this->options->getEncodedOption('message_info_2', 'invites you to the private chat. Do you accept it?'),
				'messageInfo3' => $this->options->getEncodedOption('message_info_3', 'The message has been posted, but first it must be approved by the administrator.'),
				'messageError13' => $this->options->getEncodedOption('message_error_13', 'The length of the message exceeds allowed limit.'),
				'messageError14' => $this->options->getEncodedOption('message_error_14', 'You are not allowed to send private messages to this user.'),
			),
			'userSettings' => $this->userSettingsDAO->getAll(),
			'attachmentsValidFileFormats' => $this->attachmentsService->getAllowedFormats(),
			'attachmentsSizeLimit' => $this->attachmentsService->getSizeLimit(),
			'imagesSizeLimit' => $this->options->getIntegerOption('images_size_limit', 3145728),
			'autoHideUsersList' => $this->options->isOptionEnabled('autohide_users_list', true),
			'autoHideUsersListWidth' => $this->options->getIntegerOption('autohide_users_list_width', 350),
			'showUsersList' => $this->options->isOptionEnabled('show_users'),
			'showUsersListInfoWindows' => $this->options->isOptionEnabled('show_users_list_info_windows', true),
			'multilineSupport' => $this->options->isOptionEnabled('multiline_support'),
			'messagesInline' => $this->options->isOptionEnabled('messages_inline', false),
			'messageMaxLength' => $this->options->getIntegerOption('message_max_length', 100),
			'debugMode' => $this->options->isOptionEnabled('enabled_debug', false),
			'errorMode' => $this->options->isOptionEnabled('enabled_errors', false),
			'emoticonsSet' => $this->options->getIntegerOption('emoticons_enabled', 1),
			'enableLeaveNotification' => $this->options->isOptionEnabled('enable_leave_notification', true),
			'enableJoinNotification' => $this->options->isOptionEnabled('enable_join_notification', true),
			'leaveSoundNotification' => $this->options->getEncodedOption('leave_sound_notification'),
			'joinSoundNotification' => $this->options->getEncodedOption('join_sound_notification'),
			'mentioningSoundNotification' => $this->options->getEncodedOption('mentioning_sound_notification'),
			'textColorAffectedParts' => (array) $this->options->getOption("text_color_parts", array('message', 'messageUserName')),
			'rights' => array(
				'deleteMessages' => $this->options->isOptionEnabled('enable_message_actions') && $this->usersDAO->hasCurrentWpUserRight('delete_message'),
				'editMessages' => $this->options->isOptionEnabled('enable_message_actions') && $this->usersDAO->hasCurrentWpUserRight('edit_message'),
				'banUsers' => $this->options->isOptionEnabled('enable_message_actions') && $this->usersDAO->hasCurrentWpUserRight('ban_user'),
				'editOwnMessages' => $this->options->isOptionEnabled('enable_edit_own_messages', false),
			),
			'allowToReceiveMessages' => !$this->options->isOptionEnabled('write_only', false),
			'enableApprovalConfirmation' => $this->options->isOptionEnabled('enable_approval_confirmation', true),
			'approvingMessagesMode' => $this->options->getIntegerOption('approving_messages_mode', 1),
			'fbUsersListTopOffset' => $this->options->getIntegerOption('fb_users_list_top_offset', 0),
			'fbBottomOffset' => $this->options->getIntegerOption('fb_bottom_offset', 0),
			'fbBottomThreshold' => $this->options->getIntegerOption('fb_bottom_offset_threshold', 0),
			'fbMinimizeOnStart' => $this->options->isOptionEnabled('fb_minimize_on_start', false),
			'fbDisableChannel' => $this->options->isOptionEnabled('fb_disable_channel', false) &&
				$this->options->isOptionEnabled('show_users', false) && $this->options->isOptionEnabled('enable_private_messages', false),
			'customEmoticonsPopupWidth' => $this->options->getIntegerOption('custom_emoticons_popup_width', 0),
			'customEmoticonsPopupHeight' => $this->options->getIntegerOption('custom_emoticons_popup_height', 0),
			'customEmoticonsEmoticonMaxWidthInPopup' => $this->options->getIntegerOption('custom_emoticons_emoticon_max_width_in_popup', 0),
			'allowToSendMessages' => $this->userService->isSendingMessagesAllowed() || $this->authentication->isAuthenticatedExternally(),
			'chatHeight' => $this->options->getEncodedOption('chat_height', '200px'),
		);

		foreach ($jsOptions['messages'] as $key => $jsOption) {
			$jsOptions['messages'][$key] = html_entity_decode((string) $jsOption, ENT_QUOTES, 'UTF-8');
		}

		if ($this->options->isOptionEnabled('custom_emoticons_enabled', false)) {
			$jsOptions['emoticons'] = array();
			foreach ($this->emoticonsDAO->getAll() as $emoticon) {
				$attachmentId = wp_get_attachment_url($emoticon->getAttachmentId());
				if ($attachmentId !== false) {
					$jsOptions['emoticons'][] = array(
						'id' => $emoticon->getId(),
						'url' => $attachmentId,
						'alias' => $emoticon->getAlias()
					);
				}
			}
		}
		
		$templater = new WiseChatTemplater($this->options->getPluginBaseDir());
		$templater->setTemplateFile(WiseChatThemes::getInstance()->getMainTemplate());

		$totalUsers = 0;
		if ($this->options->isOptionEnabled('counter_without_anonymous', false)) {
			$totalUsers = $this->channelUsersDAO->getAmountOfLoggedInUsersInChannel($channel->getId());
		} else {
			$totalUsers = $this->channelUsersDAO->getAmountOfUsersInChannel($channel->getId());
		}

		// detect BP group ID:
		$bpGroupID = null;
		if ($this->options->isOptionEnabled('enable_buddypress', false) && function_exists("bp_is_group") && bp_is_group()) {
			$bpGroupID = bp_get_group_id();
		}

		$data = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'baseDir' => $this->options->getBaseDir(),
			'redirectURL' => $redirectURL,
			'messages' => $renderedMessages,
			'themeStyles' => $this->options->getBaseDir().WiseChatThemes::getInstance()->getCss(),
			'sidebarMode' => $this->options->getIntegerOption('mode', 0) === 1,
			'showMessageSubmitButton' => $this->options->isOptionEnabled('show_message_submit_button'),
            'showEmoticonInsertButton' => $this->options->isOptionEnabled('show_emoticon_insert_button', true),
			'messagesInline' => $this->options->isOptionEnabled('messages_inline', false),
			'messageSubmitButtonCaption' => $this->options->getEncodedOption('message_submit_button_caption', 'Send'),
			'showUsersList' => $this->options->isOptionEnabled('show_users'),
			'showUsersListSearchBox' => $this->options->isOptionEnabled('show_users_list_search_box', true),
			'usersList' => $this->options->isOptionEnabled('show_users') ? $this->renderer->getRenderedUsersList($channel, true, $bpGroupID) : '',
			'showUsersCounter' => $this->options->isOptionEnabled('show_users_counter'),
			'channelUsersLimit' => $this->options->getIntegerOption('channel_users_limit', 0),
			'totalUsers' => $totalUsers,
			'showUserName' => $this->options->isOptionEnabled('show_user_name'),
			'currentUserName' => htmlentities($this->authentication->getUserNameOrEmptyString(), ENT_QUOTES, 'UTF-8', false),
			'isCurrentUserNameNotEmpty' => $this->authentication->isAuthenticated(),
			'allowPrivateMessages' => $this->options->isOptionEnabled('enable_private_messages', false),
			
			'inputControlsTopLocation' => $this->options->getEncodedOption('input_controls_location') == 'top',
			'inputControlsBottomLocation' => $this->options->getEncodedOption('input_controls_location') == '',
			
			'showCustomizationsPanel' => 
				$this->options->isOptionEnabled('allow_change_user_name') && !($this->authentication->getUser() !== null && $this->authentication->getUser()->getWordPressId() > 0) && !$this->authentication->isAuthenticatedExternally() ||
				$this->options->isOptionEnabled('allow_mute_sound') && strlen($this->options->getEncodedOption('sound_notification')) > 0 || 
				$this->options->isOptionEnabled('allow_change_text_color') ||
				$this->options->isOptionEnabled('allow_disable_user_notifications') && $this->options->isOptionEnabled('enable_private_messages', false) && $this->authentication->getUser() !== null && $this->authentication->getUser()->getWordPressId() > 0,
				
			'allowChangeUserName' => $this->options->isOptionEnabled('allow_change_user_name') && !($this->authentication->getUser() !== null && $this->authentication->getUser()->getWordPressId() > 0) && !$this->authentication->isAuthenticatedExternally(),
			'userNameLengthLimit' => $this->options->getIntegerOption('user_name_length_limit', 25),
			'allowMuteSound' => $this->options->isOptionEnabled('allow_mute_sound') && strlen($this->options->getEncodedOption('sound_notification')) > 0,
			'allowChangeTextColor' => $this->options->isOptionEnabled('allow_change_text_color'),
			'allowControlUserNotifications' => $this->options->isOptionEnabled('allow_control_user_notifications') && $this->options->isOptionEnabled('enable_private_messages', false) && $this->authentication->getUser() !== null && $this->authentication->getUser()->getWordPressId() > 0,

            'allowToSendMessages' => $this->userService->isSendingMessagesAllowed() || $this->authentication->isAuthenticatedExternally(),
            'allowToReceiveMessages' => !$this->options->isOptionEnabled('write_only', false),

			'messageCustomize' => $this->options->getEncodedOption('message_customize', 'Customize'),
			'messageName' => $this->options->getEncodedOption('message_name', 'Name'),
			'messageSave' => $this->options->getEncodedOption('message_save', 'Save'),
			'messageReset' => $this->options->getEncodedOption('message_reset', 'Reset'),
			'messageMuteSounds' => $this->options->getEncodedOption('message_mute_sounds', 'Mute sounds'),
			'messageEnableNotifications' => $this->options->getEncodedOption('message_enable_notifications', 'Enable E-mail Notifications'),
			'messageTextColor' => $this->options->getEncodedOption('message_text_color', 'Text color'),
			'messageTotalUsers' => $this->options->getEncodedOption('message_total_users', 'Total users'),
			'messagePictureUploadHint' => $this->options->getEncodedOption('message_picture_upload_hint', 'Upload a picture'),
			'messageAttachFileHint' => $this->options->getEncodedOption('message_attach_file_hint', 'Attach a file'),
            'messageInsertEmoticon' => $this->options->getEncodedOption('message_insert_emoticon', 'Insert an emoticon'),
			'messageInputTitle' => $this->options->getEncodedOption('message_input_title', 'Use Shift+ENTER in order to move to the next line.'),
            'windowTitle' => $this->options->getEncodedOption('window_title', ''),
			'showWindowTitle' => strlen($this->options->getEncodedOption('window_title', '')) > 0 || $this->options->getIntegerOption('mode', 0) === 1 || $this->options->isOptionEnabled('users_list_offline_enable', false),
			'showRecentChatsIndicatorClassic' => $this->options->isOptionEnabled('users_list_offline_enable', false) && $this->options->isOptionEnabled('enable_private_messages', false) && $this->options->getIntegerOption('mode', 0) === 0,
			'showRecentChatsIndicatorFB' => $this->options->isOptionEnabled('users_list_offline_enable', false) && $this->options->isOptionEnabled('enable_private_messages', false) && $this->options->getIntegerOption('mode', 0) === 1,

            'enableAttachmentsPanel' => $this->options->isOptionEnabled('enable_images_uploader') || $this->options->isOptionEnabled('enable_attachments_uploader'),
            'enableImagesUploader' => $this->options->isOptionEnabled('enable_images_uploader'),
            'enableAttachmentsUploader' => $this->options->isOptionEnabled('enable_attachments_uploader'),
            'attachmentsExtensionsList' => $this->attachmentsService->getAllowedExtensionsList(),

            'multilineSupport' => $this->options->isOptionEnabled('multiline_support'),
            'hintMessage' => $this->options->getEncodedOption('hint_message'),
            'messageMaxLength' => $this->options->getIntegerOption('message_max_length', 100),

			'jsOptions' => json_encode($jsOptions),
			'jsOptionsEncoded' => htmlspecialchars(json_encode($jsOptions), ENT_QUOTES, 'UTF-8'),
            'messagesOrder' => $this->options->getEncodedOption('messages_order', '') == 'descending' ? 'descending' : 'ascending',
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),

			'showUsersListTitle' => $this->options->isOptionEnabled('fb_show_users_list_title', true) && $this->options->getIntegerOption('mode', 0) === 1,
			'showMinimizeUsersListOption' => $this->options->isOptionEnabled('fb_minimize_users_list_option', false) && $this->options->getIntegerOption('mode', 0) === 1,
			'usersListTitle' => $this->options->getEncodedOption('users_list_title', 'Users List'),
			'usersListSearchHint' => $this->options->getEncodedOption('users_list_search_hint', 'Search ...'),
		);
		
		$data = array_merge($data, $this->userSettingsDAO->getAll());
		if ($this->authentication->isAuthenticated()) {
			$userData = $this->authentication->getUser()->getData();

			// a special case for 'enableNotifications' user option:
			if (!array_key_exists('disableNotifications', $userData) || $userData['disableNotifications'] !== true) {
				$userData['enableNotifications'] = true;
			}

			$data = array_merge($data, $userData);
		}

		/**
		 * Filters the data used to render HTML code of the chat window.
		 *
		 * @since 2.3.2
		 *
		 * @param array $data The rendering input data
		 */
		$data = apply_filters('wc_chat_html_data', $data);

		$html = $templater->render($data);

		/**
		 * Filters HTML code of the chat window.
		 *
		 * @since 2.3.2
		 *
		 * @param string $html A fully rendered HTML code of the chat window
		 * @param array $data The rendering input data
		 */
		return apply_filters('wc_chat_html', $html, $data);
	}

    /**
     * @return string
     */
    private function getCheckSum() {
		$checkSumData = is_array($this->shortCodeOptions) ? $this->shortCodeOptions : array();
		$checkSumData['_sid'] = session_id();
		$checkSumData['_sn'] = session_name();
		$checkSumData['ts'] = time();
		if ($this->options->isOptionEnabled('enable_buddypress', false) && function_exists("bp_is_group") && bp_is_group()) {
			$checkSumData['_bpg'] = bp_get_group_id();
		}

        return base64_encode(WiseChatCrypt::encrypt(serialize($checkSumData)));
    }

    /**
     * @return string
     */
	private function getEndpointBase() {
		$endpointBase = get_site_url().'/wp-admin/admin-ajax.php';
		if (in_array($this->options->getEncodedOption('ajax_engine', null), array('lightweight', 'ultralightweight'))) {
			$endpointBase = get_site_url().'/wp-content/plugins/wise-chat-pro/src/endpoints/';
		}
		
		return $endpointBase;
	}

	/**
	 * @return string
	 */
	private function getMessagesEndpointBase() {
		if ($this->options->getEncodedOption('ajax_engine', null) === 'ultralightweight') {
			$endpointBase = get_site_url().'/wp-content/plugins/wise-chat-pro/src/endpoints/ultra/index.php';
		} else {
			$endpointBase = $this->getEndpointBase();
		}
		return $endpointBase;
	}

 	/**
     * @return string
     */
	private function getWPEndpointBase() {
		return get_site_url().'/wp-admin/admin-ajax.php';
	}

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
	private function getPostParam($name, $default = null) {
		return array_key_exists($name, $_POST) ? $_POST[$name] : $default;
	}

	/**
	 * @param string $name
	 * @param mixed|null $default
	 * @return mixed
	 */
	private function getParam($name, $default = null) {
		return array_key_exists($name, $_GET) ? $_GET[$name] : $default;
	}

	/**
	 * Loads Composer class loader only if necessary.
	 */
	private function requireComposerClassLoader() {
		if ($this->service->isExternalLoginEnabled()) {
			require_once(dirname(__DIR__).'/vendor/autoload.php');
		}
	}
}
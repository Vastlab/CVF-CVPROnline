<?php

/**
 * CVPRChat core class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChat {
	
	/**
	* @var CVPRChatOptions
	*/
	private $options;
	
	/**
	* @var CVPRChatUsersDAO
	*/
	private $usersDAO;
	
	/**
	* @var CVPRChatUserSettingsDAO
	*/
	private $userSettingsDAO;
	
	/**
	* @var CVPRChatChannelUsersDAO
	*/
	private $channelUsersDAO;
	
	/**
	* @var CVPRChatActionsDAO
	*/
	private $actionsDAO;
	
	/**
	* @var CVPRChatRenderer
	*/
	private $renderer;
	
	/**
	* @var CVPRChatCssRenderer
	*/
	private $cssRenderer;
	
	/**
	* @var CVPRChatBansService
	*/
	private $bansService;
	
	/**
	* @var CVPRChatUserService
	*/
	private $userService;
	
	/**
	* @var CVPRChatMessagesService
	*/
	private $messagesService;
	
	/**
	* @var CVPRChatService
	*/
	private $service;
	
	/**
	* @var CVPRChatAttachmentsService
	*/
	private $attachmentsService;

	/**
	 * @var CVPRChatAuthentication
	 */
	private $authentication;

	/**
	 * @var CVPRChatHttpRequestService
	 */
	private $httpRequestService;
	
	/**
	* @var array
	*/
	private $shortCodeOptions;
	
	public function __construct() {
		$this->options = CVPRChatOptions::getInstance();
		$this->usersDAO = CVPRChatContainer::get('dao/user/CVPRChatUsersDAO');
		$this->userSettingsDAO = CVPRChatContainer::get('dao/user/CVPRChatUserSettingsDAO');
		$this->channelUsersDAO = CVPRChatContainer::get('dao/CVPRChatChannelUsersDAO');
		$this->actionsDAO = CVPRChatContainer::get('dao/CVPRChatActionsDAO');
		$this->renderer = CVPRChatContainer::get('rendering/CVPRChatRenderer');
		$this->cssRenderer = CVPRChatContainer::get('rendering/CVPRChatCssRenderer');
		$this->bansService = CVPRChatContainer::get('services/CVPRChatBansService');
		$this->userService = CVPRChatContainer::get('services/user/CVPRChatUserService');
		$this->messagesService = CVPRChatContainer::get('services/CVPRChatMessagesService');
		$this->service = CVPRChatContainer::get('services/CVPRChatService');
		$this->attachmentsService = CVPRChatContainer::get('services/CVPRChatAttachmentsService');
		$this->authentication = CVPRChatContainer::getLazy('services/user/CVPRChatAuthentication');
		$this->httpRequestService = CVPRChatContainer::getLazy('services/CVPRChatHttpRequestService');
		CVPRChatContainer::load('CVPRChatCrypt');
		CVPRChatContainer::load('CVPRChatThemes');
		CVPRChatContainer::load('rendering/CVPRChatTemplater');
		
		$this->userService->initMaintenance();
		$this->shortCodeOptions = array();
	}
	
	/*
	* Enqueues all necessary resources (scripts or styles).
	*/
	public function registerResources() {
		$pluginBaseURL = $this->options->getBaseDir();
		
		wp_enqueue_script('Cvpr_chat_messages_history', $pluginBaseURL.'js/utils/messages_history.js', array('jquery'));
		wp_enqueue_script('Cvpr_chat_messages', $pluginBaseURL.'js/ui/messages.js', array('jquery'));
		wp_enqueue_script('Cvpr_chat_settings', $pluginBaseURL.'js/ui/settings.js', array('jquery'));
		wp_enqueue_script('Cvpr_chat_maintenance_executor', $pluginBaseURL.'js/maintenance/executor.js', array('jquery'));
		wp_enqueue_script('Cvpr_chat_core', $pluginBaseURL.'js/Cvpr_chat.js', array('jquery'));
		
		if ($this->options->isOptionEnabled('allow_change_text_color', true)) {
			wp_enqueue_script('Cvpr_chat_3rdparty_jscolorPicker', $pluginBaseURL.'js/3rdparty/jquery.colorPicker.min.js', array('jquery'));
			wp_enqueue_style('Cvpr_chat_3rdparty_jscolorPicker', $pluginBaseURL.'css/3rdparty/colorPicker.css');
		}

		wp_enqueue_script('Cvpr_chat_3rdparty_momentjs', $pluginBaseURL.'js/3rdparty/moment.patched.min.js', array('jquery'));
	}

	/**
	 * Shortcode backend function: [Cvpr-chat]
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
	 * Shortcode backend function: [Cvpr-chat]
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

		$this->options->replaceOptions($attributes);
		$this->shortCodeOptions = $attributes;

		$chatId = $this->service->getChatID();
		$channel = $this->service->createAndGetChannel($this->service->getValidChatChannelName($attributes['channel']));
		$this->userService->refreshChannelUsersData();

		$templater = new CVPRChatTemplater($this->options->getPluginBaseDir());
		$templater->setTemplateFile(CVPRChatThemes::getInstance()->getChannelUsersWidgetTemplate());

		$data = array(
			'chatId' => $chatId,
			'baseDir' => $this->options->getBaseDir(),
			'title' => $attributes['title'],
			'themeStyles' => $this->options->getBaseDir().CVPRChatThemes::getInstance()->getCss(),
			'usersList' => $this->renderer->getRenderedUsersList($channel, false),
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition(),
			'messageUsersListEmpty' => $this->options->getEncodedOption('message_users_list_empty', 'No users in the channel'),
		);
		$data = array_merge($data, $this->userSettingsDAO->getAll());
		if ($this->authentication->isAuthenticated()) {
			$data = array_merge($data, $this->authentication->getUser()->getData());
		}

		return $templater->render($data);
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
		$redirectURL = null;
		$channel = $this->service->createAndGetChannel($this->service->getValidChatChannelName($channelName));

		// saves users list in session for this channel (it will be updated in maintenance task):
		if ($this->options->isOptionEnabled('enable_leave_notification', true) || strlen($this->options->getOption('leave_sound_notification')) > 0) {
			$this->userService->clearUsersListInSession($channel, CVPRChatUserService::USERS_LIST_CATEGORY_ABSENT);
		}
		if ($this->options->isOptionEnabled('enable_join_notification', true) || strlen($this->options->getOption('join_sound_notification')) > 0) {
			$this->userService->persistUsersListInSession($channel, CVPRChatUserService::USERS_LIST_CATEGORY_NEW);
		}

		if ($this->service->isIpKicked()) {
			return $this->renderer->getRenderedAccessDenied(
				$this->options->getOption('message_error_12', 'You are blocked from using the chat'), 'wcAccessDenied'
			);
		}

		if ($this->service->isChatRestrictedForAnonymousUsers()) {
			return $this->renderer->getRenderedAccessDenied(
				$this->options->getOption('message_error_4', 'Only logged in users are allowed to enter the chat'), 'wcAccessDenied'
			);
		}

		if ($this->service->isChatRestrictedForCurrentUserRole()) {
			return $this->renderer->getRenderedAccessDenied(
				$this->options->getOption('message_error_11', 'You are not allowed to enter the chat.'), 'wcAccessDenied'
			);
		}
		
		if (!$this->service->isChatOpen()) {
			return $this->renderer->getRenderedAccessDenied(
				$this->options->getOption('message_error_5', 'The chat is closed now'), 'wcChatClosed'
			);
		}
		
		if ($this->service->isChatChannelFull($channel)) {
			return $this->renderer->getRenderedAccessDenied(
				$this->options->getOption('message_error_6', 'The chat is full now. Try again later.'), 'wcChatFull'
			);
		}
		
		if ($this->service->isChatChannelsLimitReached($channel)) {
			return $this->renderer->getRenderedAccessDenied(
				$this->options->getOption('message_error_10', 'You cannot enter the chat due to the limit of channels you can participate simultaneously.'), 'wcChatChannelLimitFull'
			);
		}

		if ($this->service->hasUserToBeForcedToEnterName()) {
			if ($this->getPostParam('wcUserNameSelection') !== null) {
				try {
					$this->authentication->authenticate($this->getPostParam('wcUserName'));
					$redirectURL = $this->httpRequestService->getCurrentURL();
				} catch (Exception $e) {
					return $this->renderer->getRenderedUserNameForm($e->getMessage());
				}
			} else {
				return $this->renderer->getRenderedUserNameForm();
			}
		}
		
		if ($this->service->hasUserToBeAuthorizedInChannel($channel)) {
			if ($this->getPostParam('wcChannelAuthorization') !== null) {
				if (!$this->service->authorize($channel, $this->getPostParam('wcChannelPassword'))) {
					return $this->renderer->getRenderedPasswordAuthorization($this->options->getOption('message_error_9', 'Invalid password.'));
				} else {
					$redirectURL = $this->httpRequestService->getCurrentURL();
				}
			} else {
				return $this->renderer->getRenderedPasswordAuthorization();
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
				
			$renderedMessages .= $this->renderer->getRenderedMessage($message);
			
			if ($lastId < $message->getId()) {
				$lastId = $message->getId();
			}
		}
		
		$lastAction = $this->actionsDAO->getLast();
		$jsOptions = array(
			'chatId' => $chatId,
			'channelId' => $channel->getId(),
			'nowTime' => gmdate('c', time()),
			'lastId' => $lastId,
			'checksum' => $this->getCheckSum(),
			'lastActionId' => $lastAction !== null ? $lastAction->getId() : 0,
			'baseDir' => $this->options->getBaseDir(),
            'emoticonsBaseURL' => $this->options->getEmoticonsBaseURL(),
			'apiWPEndpointBase' => $this->getWPEndpointBase(),
			'apiEndpointBase' => $this->getEndpointBase(),
			'apiMessagesEndpointBase' => $this->getMessagesEndpointBase(),
			'messagesRefreshTime' => intval($this->options->getEncodedOption('messages_refresh_time', 3000)),
			'messagesOrder' => $this->options->getEncodedOption('messages_order', '') == 'descending' ? 'descending' : 'ascending',
			'enableTitleNotifications' => $this->options->isOptionEnabled('enable_title_notifications'),
			'soundNotification' => $this->options->getEncodedOption('sound_notification'),
			'messagesTimeMode' => $this->options->getEncodedOption('messages_time_mode', 'elapsed'),
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
				'messageHasJoinedTheChannel' => $this->options->getEncodedOption('message_has_joined_the_channel', 'has joined the channel'),
				'messageSpamReportQuestion' => $this->options->getEncodedOption('message_text_1', 'Are you sure you want to report the message as spam?'),
			),
			'userSettings' => $this->userSettingsDAO->getAll(),
			'attachmentsValidFileFormats' => $this->attachmentsService->getAllowedFormats(),
			'attachmentsSizeLimit' => $this->attachmentsService->getSizeLimit(),
			'imagesSizeLimit' => $this->options->getIntegerOption('images_size_limit', 3145728),
			'autoHideUsersList' => $this->options->isOptionEnabled('autohide_users_list', false),
			'autoHideUsersListWidth' => $this->options->getIntegerOption('autohide_users_list_width', 300),
			'showUsersList' => $this->options->isOptionEnabled('show_users'),
			'multilineSupport' => $this->options->isOptionEnabled('multiline_support'),
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
		);

		foreach ($jsOptions['messages'] as $key => $jsOption) {
			$jsOptions['messages'][$key] = html_entity_decode( (string) $jsOption, ENT_QUOTES, 'UTF-8');
		}
		
		$templater = new CVPRChatTemplater($this->options->getPluginBaseDir());
		$templater->setTemplateFile(CVPRChatThemes::getInstance()->getMainTemplate());

		$totalUsers = 0;
		if ($this->options->isOptionEnabled('counter_without_anonymous', true)) {
			$totalUsers = $this->channelUsersDAO->getAmountOfLoggedInUsersInChannel($channel->getId());
		} else {
			$totalUsers = $this->channelUsersDAO->getAmountOfUsersInChannel($channel->getId());
		}

		$data = array(
			'chatId' => $chatId,
			'baseDir' => $this->options->getBaseDir(),
			'redirectURL' => $redirectURL,
			'messages' => $renderedMessages,
			'themeStyles' => $this->options->getBaseDir().CVPRChatThemes::getInstance()->getCss(),
			'showMessageSubmitButton' => $this->options->isOptionEnabled('show_message_submit_button', true),
            'showEmoticonInsertButton' => $this->options->isOptionEnabled('show_emoticon_insert_button', true),
			'messagesInline' => $this->options->isOptionEnabled('messages_inline', false),
			'messageSubmitButtonCaption' => $this->options->getEncodedOption('message_submit_button_caption', 'Send'),
			'showUsersList' => $this->options->isOptionEnabled('show_users'),
			'usersList' => $this->options->isOptionEnabled('show_users') ? $this->renderer->getRenderedUsersList($channel) : '',
			'showUsersCounter' => $this->options->isOptionEnabled('show_users_counter'),
			'channelUsersLimit' => $this->options->getIntegerOption('channel_users_limit', 0),
			'totalUsers' => $totalUsers,
			'showUserName' => $this->options->isOptionEnabled('show_user_name', true),
			'currentUserName' => htmlentities($this->authentication->getUserNameOrEmptyString(), ENT_QUOTES, 'UTF-8'),
			'isCurrentUserNameNotEmpty' => $this->authentication->isAuthenticated(),
			
			'inputControlsTopLocation' => $this->options->getEncodedOption('input_controls_location') == 'top',
			'inputControlsBottomLocation' => $this->options->getEncodedOption('input_controls_location') == '',
			
			'showCustomizationsPanel' => 
				$this->options->isOptionEnabled('allow_change_user_name', true) && !$this->usersDAO->isWpUserLogged() ||
				$this->options->isOptionEnabled('allow_mute_sound') && strlen($this->options->getEncodedOption('sound_notification')) > 0 || 
				$this->options->isOptionEnabled('allow_change_text_color', true),
				
			'allowChangeUserName' => $this->options->isOptionEnabled('allow_change_user_name', true) && !$this->usersDAO->isWpUserLogged(),
			'userNameLengthLimit' => $this->options->getIntegerOption('user_name_length_limit', 25),
			'allowMuteSound' => $this->options->isOptionEnabled('allow_mute_sound') && strlen($this->options->getEncodedOption('sound_notification')) > 0,
			'allowChangeTextColor' => $this->options->isOptionEnabled('allow_change_text_color', true),

            'allowToSendMessages' => $this->userService->isSendingMessagesAllowed(),
				
			'messageCustomize' => $this->options->getEncodedOption('message_customize', 'Customize'),
			'messageName' => $this->options->getEncodedOption('message_name', 'Name'),
			'messageSave' => $this->options->getEncodedOption('message_save', 'Save'),
			'messageReset' => $this->options->getEncodedOption('message_reset', 'Reset'),
			'messageMuteSounds' => $this->options->getEncodedOption('message_mute_sounds', 'Mute sounds'),
			'messageTextColor' => $this->options->getEncodedOption('message_text_color', 'Text color'),
			'messageTotalUsers' => $this->options->getEncodedOption('message_total_users', 'Total users'),
			'messagePictureUploadHint' => $this->options->getEncodedOption('message_picture_upload_hint', 'Upload a picture'),
			'messageAttachFileHint' => $this->options->getEncodedOption('message_attach_file_hint', 'Attach a file'),
            'messageInsertEmoticon' => $this->options->getEncodedOption('message_insert_emoticon', 'Insert an emoticon'),
			'messageInputTitle' => $this->options->getEncodedOption('message_input_title', 'Use Shift+ENTER in order to move to the next line.'),
            'windowTitle' => $this->options->getEncodedOption('window_title', 'CVPR Chat'),

            'enableAttachmentsPanel' => $this->options->isOptionEnabled('enable_images_uploader', true) || $this->options->isOptionEnabled('enable_attachments_uploader', true),
            'enableImagesUploader' => $this->options->isOptionEnabled('enable_images_uploader', true),
            'enableAttachmentsUploader' => $this->options->isOptionEnabled('enable_attachments_uploader', true),
            'attachmentsExtensionsList' => $this->attachmentsService->getAllowedExtensionsList(),

            'multilineSupport' => $this->options->isOptionEnabled('multiline_support'),
            'hintMessage' => $this->options->getEncodedOption('hint_message'),
            'messageMaxLength' => $this->options->getIntegerOption('message_max_length', 100),

			'jsOptions' => json_encode($jsOptions),
			'jsOptionsEncoded' => htmlspecialchars(json_encode($jsOptions), ENT_QUOTES, 'UTF-8'),
            'messagesOrder' => $this->options->getEncodedOption('messages_order', '') == 'descending' ? 'descending' : 'ascending',
			'cssDefinitions' => $this->cssRenderer->getCssDefinition($chatId),
			'customCssDefinitions' => $this->cssRenderer->getCustomCssDefinition()
		);
		
		$data = array_merge($data, $this->userSettingsDAO->getAll());
		if ($this->authentication->isAuthenticated()) {
			$data = array_merge($data, $this->authentication->getUser()->getData());
		}

		return $templater->render($data);
	}

    /**
     * @return string
     */
    private function getCheckSum() {
		$this->shortCodeOptions['ts'] = time();
        return base64_encode(CVPRChatCrypt::encrypt(serialize($this->shortCodeOptions)));
    }

	/**
	 * @return string
	 */
	private function getWPEndpointBase() {
		return get_site_url().'/wp-admin/admin-ajax.php';
	}

    /**
     * @return string
     */
	private function getEndpointBase() {
		$endpointBase = get_site_url().'/wp-admin/admin-ajax.php';
		if (in_array($this->options->getEncodedOption('ajax_engine', null), array('lightweight', 'ultralightweight'))) {
			$endpointBase = plugin_dir_url(__FILE__).'endpoints/';
		}
		
		return $endpointBase;
	}

	/**
     * @return string
     */
	private function getMessagesEndpointBase() {
		if ($this->options->getEncodedOption('ajax_engine', null) === 'ultralightweight') {
			$endpointBase = plugin_dir_url(__FILE__).'endpoints/ultra/index.php';
		} else {
			$endpointBase = $this->getEndpointBase();
		}

		return $endpointBase;
	}

    /**
     * @param string $name
     * @param mixed|null $default
     * @return mixed
     */
	private function getPostParam($name, $default = null) {
		return array_key_exists($name, $_POST) ? $_POST[$name] : $default;
	}
}

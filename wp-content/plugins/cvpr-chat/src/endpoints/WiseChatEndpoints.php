<?php

/**
 * CVPR Chat endpoints class
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatEndpoints {
	
	/**
	* @var CVPRChatChannelsDAO
	*/
	private $channelsDAO;
	
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
	* @var CVPRChatBansDAO
	*/
	private $bansDAO;

	/**
	 * @var CVPRChatActions
	 */
	protected $actions;
	
	/**
	* @var CVPRChatRenderer
	*/
	private $renderer;
	
	/**
	* @var CVPRChatBansService
	*/
	private $bansService;

	/**
	 * @var CVPRChatKicksService
	 */
	private $kicksService;
	
	/**
	* @var CVPRChatMessagesService
	*/
	private $messagesService;
	
	/**
	* @var CVPRChatUserService
	*/
	private $userService;
	
	/**
	* @var CVPRChatService
	*/
	private $service;

	/**
	 * @var CVPRChatAuthentication
	 */
	private $authentication;

	/**
	 * @var CVPRChatUserEvents
	 */
	private $userEvents;

	/**
	 * @var CVPRChatAuthorization
	 */
	private $authorization;
	
	/**
	* @var CVPRChatOptions
	*/
	private $options;
	
	private $arePostSlashesStripped = false;

	public function __construct() {
		$this->options = CVPRChatOptions::getInstance();

		$this->authentication = CVPRChatContainer::getLazy('services/user/CVPRChatAuthentication');
		$this->userEvents = CVPRChatContainer::getLazy('services/user/CVPRChatUserEvents');
		$this->authorization = CVPRChatContainer::getLazy('services/user/CVPRChatAuthorization');
		$this->usersDAO = CVPRChatContainer::getLazy('dao/user/CVPRChatUsersDAO');
		$this->userSettingsDAO = CVPRChatContainer::getLazy('dao/user/CVPRChatUserSettingsDAO');
		$this->channelUsersDAO = CVPRChatContainer::getLazy('dao/CVPRChatChannelUsersDAO');
		$this->actions = CVPRChatContainer::getLazy('services/user/CVPRChatActions');
		$this->channelsDAO = CVPRChatContainer::getLazy('dao/CVPRChatChannelsDAO');
		$this->bansDAO = CVPRChatContainer::getLazy('dao/CVPRChatBansDAO');
		$this->renderer = CVPRChatContainer::getLazy('rendering/CVPRChatRenderer');
		$this->bansService = CVPRChatContainer::getLazy('services/CVPRChatBansService');
		$this->kicksService = CVPRChatContainer::getLazy('services/CVPRChatKicksService');
		$this->messagesService = CVPRChatContainer::getLazy('services/CVPRChatMessagesService');
		$this->userService = CVPRChatContainer::getLazy('services/user/CVPRChatUserService');
		$this->service = CVPRChatContainer::getLazy('services/CVPRChatService');
		
		CVPRChatContainer::load('CVPRChatCrypt');
	}
	
	/**
	* Returns messages to render in the chat window.
	*/
	public function messagesEndpoint() {
		$this->jsonContentType();
		$this->confirmUserAuthenticationOrEndRequest();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();

		$response = array();
		try {
			$this->checkGetParams(array('channelId', 'lastId'));
			$lastId = intval($this->getGetParam('lastId', 0));
			$channelId = $this->getGetParam('channelId');

			$this->checkIpNotKicked();
			$this->checkUserAuthorization();
			$this->checkChatOpen();
			$channel = $this->channelsDAO->get($channelId);
			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			$response['nowTime'] = gmdate('c', time());
			$response['result'] = array();

			// get and render messages:
			$messages = $this->messagesService->getAllByChannelNameAndOffset($channel->getName(), $lastId > 0 ? $lastId : null);
			foreach ($messages as $message) {
				// omit non-admin messages:
				if ($message->isAdmin() && !$this->usersDAO->isWpUserAdminLogged()) {
					continue;
				}

				$messageToJson = array();
				$messageToJson['text'] = $this->renderer->getRenderedMessage($message);
				$messageToJson['id'] = $message->getId();

				$response['result'][] = $messageToJson;
			}
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}
    
		echo json_encode($response);
		die();
	}
	
	/**
	* New message endpoint.
	*/
	public function messageEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();


        $channelId = trim($this->getPostParam('channelId'));
		$message = trim($this->getPostParam('message'));
		$attachments = $this->getPostParam('attachments');
		if (!is_array($attachments)) {
			$attachments = array();
		}

		$response = array();
		try {
			$this->checkIpNotKicked();
			$this->checkUserAuthentication();
			$this->checkUserAuthorization();
            $this->checkUserWriteAuthorization();
			$this->checkChatOpen();

			$channel = $this->channelsDAO->get($channelId);
			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			if (strlen($message) == 0 && count($attachments) == 0) {
				throw new Exception('Missing required fields');
			}

			$user = $this->authentication->getUser();

			/** @var CVPRChatCommandsResolver $CvprChatCommandsResolver */
			$CvprChatCommandsResolver = CVPRChatContainer::get('commands/CVPRChatCommandsResolver');

			// resolve a command if it is recognized:
			$isCommandResolved = $CvprChatCommandsResolver->resolve(
				$user, $this->authentication->getSystemUser(), $channel, $message
			);

			// add a regular message:
			if (!$isCommandResolved) {
				if (count($attachments) > 0) {
					$this->messagesService->addMessageWithAttachments($user, $channel, $message, $attachments);
				} else {
					$this->messagesService->addMessage($user, $channel, $message);
				}
			}

			$response['result'] = 'OK';
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}

		echo json_encode($response);
		die();
	}
	
	/**
	* Endpoint for messages deletion.
	*/
	public function messageDeleteEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();

		$response = array();
		try {
			$this->checkIpNotKicked();
			$this->checkChatOpen();
			$this->checkUserAuthentication();
			$this->checkUserRight('delete_message');
			$this->checkPostParams(array('channelId', 'messageId'));

            $channelId = trim($this->getPostParam('channelId'));
			$messageId = trim($this->getPostParam('messageId'));
			$channel = $this->channelsDAO->get($channelId);

			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			$this->messagesService->deleteById($messageId);
			$this->actions->publishAction('deleteMessage', array('id' => $messageId, 'channel' => $channel->getName()));

			$response['result'] = 'OK';
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}

		echo json_encode($response);
		die();
	}
	
	/**
	* Endpoint for banning users by message ID.
	*/
	public function userBanEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();

		$response = array();
		try {
			$this->checkIpNotKicked();
			$this->checkChatOpen();
			$this->checkUserAuthentication();
			$this->checkUserRight('ban_user');
			$this->checkPostParams(array('channelId', 'messageId'));

            $channelId = trim($this->getPostParam('channelId'));
			$messageId = trim($this->getPostParam('messageId'));
			$channel = $this->channelsDAO->get($channelId);

			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			$duration = $this->options->getIntegerOption('moderation_ban_duration', 1440);
			$this->bansService->banByMessageId($messageId, $channel, $duration.'m');
			$this->messagesService->addMessage($this->authentication->getSystemUser(), $channel, "User has been banned for $duration minutes", true);

			$response['result'] = 'OK';
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}
		
		echo json_encode($response);
		die();
	}

	/**
	 * Endpoint for kicking users by message ID.
	 */
	public function userKickEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();

		$response = array();
		try {
			$this->checkIpNotKicked();
			$this->checkChatOpen();
			$this->checkUserAuthentication();
			$this->checkUserRight('kick_user');
			$this->checkPostParams(array('channelId', 'messageId'));

			$channelId = trim($this->getPostParam('channelId'));
			$messageId = trim($this->getPostParam('messageId'));
			$channel = $this->channelsDAO->get($channelId);

			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			$this->kicksService->kickByMessageId($messageId);
			$this->messagesService->addMessage($this->authentication->getSystemUser(), $channel, "User has been kicked", true);

			$response['result'] = 'OK';
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}

		echo json_encode($response);
		die();
	}

	/**
	 * Endpoint to report spam messages by message ID.
	 */
	public function spamReportEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();

		$response = array();
		try {
			$this->checkIpNotKicked();
			$this->checkChatOpen();
			$this->checkUserAuthentication();

			if (!$this->options->isOptionEnabled('spam_report_enable_all', true)) {
				$this->checkUserRight('spam_report');
			}
			$this->checkPostParams(array('channelId', 'messageId'));

			$channelId = trim($this->getPostParam('channelId'));
			$messageId = trim($this->getPostParam('messageId'));
			$url = trim($this->getPostParam('url'));
			$channel = $this->channelsDAO->get($channelId);

			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			$this->messagesService->reportSpam($channelId, $messageId, $url);

			$response['result'] = 'OK';
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}

		echo json_encode($response);
		die();
	}
	
	/**
	* Endpoint for periodic (every 10-20 seconds) maintenance services like:
	* - user authentication
	* - getting the list of actions to execute on the client side
	* - getting the list of events to listen on the client side
	* - maintenance actions in messages, bans, users, etc.
	*/
	public function maintenanceEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();

		$response = array();
		try {
			$this->checkChatOpen();
			$this->checkUserAuthorization();

			$this->checkGetParams(array('channelId', 'lastActionId'));

            $channelId = $this->getGetParam('channelId');
			$channel = $this->channelsDAO->get($channelId);

			$this->checkChannel($channel);
			$this->checkChannelAuthorization($channel);

			// periodic maintenance:
			$this->userService->periodicMaintenance($channel);
			$this->messagesService->periodicMaintenance($channel);
			$this->bansService->periodicMaintenance();

			// load actions:
			$lastActionId = intval($this->getGetParam('lastActionId', 0));
			$user = $this->authentication->getUser();
			$response['actions'] = $this->actions->getJSONReadyActions($lastActionId, $user);

			// load events:
			$response['events'] = array();
			if ($this->userEvents->shouldTriggerEvent('usersList', $channel->getName())) {
				if ($this->options->isOptionEnabled('show_users')) {
					$response['events'][] = array(
						'name' => 'refreshUsersList',
						'data' => $this->renderer->getRenderedUsersList($channel)
					);
				}

				if ($this->options->isOptionEnabled('show_users_counter')) {
					$totalUsers = 0;
					if ($this->options->isOptionEnabled('counter_without_anonymous', true)) {
						$totalUsers = $this->channelUsersDAO->getAmountOfLoggedInUsersInChannel($channel->getId());
					} else {
						$totalUsers = $this->channelUsersDAO->getAmountOfUsersInChannel($channel->getId());
					}

					$response['events'][] = array(
						'name' => 'refreshUsersCounter',
						'data' => array(
							'total' => $totalUsers
						)
					);
				}

				// load absent users:
				if ($this->options->isOptionEnabled('enable_leave_notification', true) || strlen($this->options->getOption('leave_sound_notification')) > 0) {
					$response['events'][] = array(
						'name' => 'reportAbsentUsers',
						'data' => array(
							'users' => $this->userService->getAbsentUsersForChannel($channel)
						)
					);
					$this->userService->persistUsersListInSession($channel, CVPRChatUserService::USERS_LIST_CATEGORY_ABSENT);
				}
				// load new users:
				if ($this->options->isOptionEnabled('enable_join_notification', true) || strlen($this->options->getOption('join_sound_notification')) > 0) {
					$response['events'][] = array(
						'name' => 'reportNewUsers',
						'data' => array(
							'users' => $this->userService->getNewUsersForChannel($channel)
						)
					);
					$this->userService->persistUsersListInSession($channel, CVPRChatUserService::USERS_LIST_CATEGORY_NEW);
				}
			}

			$response['events'][] = array(
				'name' => 'userData',
				'data' => array(
					'name' => $user->getName()
				)
			);
			$response['events'][] = array(
				'name' => 'checkSum',
				'data' => $this->generateCheckSum()
			);

		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}

		echo json_encode($response);
		die();
	}
	
	/**
	* Endpoint for user's settings.
	*/
	public function settingsEndpoint() {
		$this->jsonContentType();
		$this->verifyXhrRequest();
		$this->verifyCheckSum();
    
		$response = array();
		try {
			$this->checkIpNotKicked();
			$this->checkChatOpen();
			$this->checkUserAuthentication();
			$this->checkUserAuthorization();
            $this->checkUserWriteAuthorization();

			$this->checkPostParams(array('property', 'value'));
			$property = $this->getPostParam('property');
			$value = $this->getPostParam('value');

			switch ($property) {
				case 'userName':
					$this->checkPostParams(array('channelId'));
					$channel = $this->channelsDAO->get($this->getPostParam('channelId'));
					$this->checkChannel($channel);
					$this->checkChannelAuthorization($channel);
					$userNameLengthLimit = $this->options->getIntegerOption('user_name_length_limit', 25);
					if ($userNameLengthLimit > 0) {
						$value = substr($value, 0, $userNameLengthLimit);
					}
					$response['value'] = $this->userService->changeUserName($value);
					break;
				case 'textColor':
					$this->userService->setUserTextColor($value);
					break;
				default:
					$this->userSettingsDAO->setSetting($property, $value);
			}
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			$response['error'] = $exception->getMessage();
			$this->sendBadRequestStatus();
		}
		
		echo json_encode($response);
		die();
	}
	
	/**
	* Endpoint that prepares an image for further upload: 
	* - basic checks
	* - resizing
	* - fixing orientation
	*
	* @notice GIFs are returned unchanged because of the lack of proper resizing abilities
	*
	* @return null
	*/
	public function prepareImageEndpoint() {
		$this->verifyCheckSum();
		
		try {
			$this->checkIpNotKicked();
			$this->checkChatOpen();
			$this->checkUserAuthentication();
			$this->checkUserAuthorization();
            $this->checkUserWriteAuthorization();

			$this->checkPostParams(array('data'));
			$data = $this->getPostParam('data');
			
			$imagesService = CVPRChatContainer::get('services/CVPRChatImagesService');
			$decodedImageData = $imagesService->decodePrefixedBase64ImageData($data);
			if ($decodedImageData['mimeType'] == 'image/gif') {
				echo $data;
			} else {
				$preparedImageData = $imagesService->getPreparedImage($decodedImageData['data']);
				echo $imagesService->encodeBase64WithPrefix($preparedImageData, $decodedImageData['mimeType']);
			}
		} catch (CVPRChatUnauthorizedAccessException $exception) {
			echo json_encode(array('error' => $exception->getMessage()));
			$this->sendUnauthorizedStatus();
		} catch (Exception $exception) {
			echo json_encode(array('error' => $exception->getMessage()));
			$this->sendBadRequestStatus();
		}
		
		die();
	}
	
	private function getPostParam($name, $default = null) {
		if (!$this->arePostSlashesStripped) {
			$_POST = stripslashes_deep($_POST);
			$this->arePostSlashesStripped = true;
		}
	
		return array_key_exists($name, $_POST) ? $_POST[$name] : $default;
	}
	
	private function getGetParam($name, $default = null) {
		return array_key_exists($name, $_GET) ? $_GET[$name] : $default;
	}
	
	private function getParam($name, $default = null) {
		$getParam = $this->getGetParam($name);
		if ($getParam === null) {
			return $this->getPostParam($name, $default);
		}
		
		return $getParam;
	}

	/**
	 * @param array $params
	 * @throws Exception
	 */
	private function checkGetParams($params) {
		foreach ($params as $param) {
			if (strlen(trim($this->getGetParam($param))) === 0) {
				throw new Exception('Required parameters are missing');
			}
		}
	}

	/**
	 * @param array $params
	 * @throws Exception
	 */
	private function checkPostParams($params) {
		foreach ($params as $param) {
			if (strlen(trim($this->getPostParam($param))) === 0) {
				throw new Exception('Required parameters are missing');
			}
		}
	}

	/**
	 * Checks if user is authenticated.
	 *
	 * @throws CVPRChatUnauthorizedAccessException
	 */
	private function checkUserAuthentication() {
		if (!$this->authentication->isAuthenticated()) {
			throw new CVPRChatUnauthorizedAccessException('Not authenticated');
		}
	}

	private function confirmUserAuthenticationOrEndRequest() {
		if (!$this->authentication->isAuthenticated()) {
			$this->sendBadRequestStatus();
			die('{ }');
		}
	}

	/**
	 * @throws CVPRChatUnauthorizedAccessException
	 */
	private function checkUserAuthorization() {
		if ($this->service->isChatRestrictedForAnonymousUsers()) {
			throw new CVPRChatUnauthorizedAccessException('Access denied');
		}
		if ($this->service->isChatRestrictedForCurrentUserRole()) {
			throw new CVPRChatUnauthorizedAccessException('Access denied');
		}
	}

	/**
	 * @throws CVPRChatUnauthorizedAccessException
	 */
	private function checkIpNotKicked() {
		if (isset($_SERVER['REMOTE_ADDR']) && $this->kicksService->isIpAddressKicked($_SERVER['REMOTE_ADDR'])) {
			throw new CVPRChatUnauthorizedAccessException('Access denied');
		}
	}

    /**
     * @throws CVPRChatUnauthorizedAccessException
     */
    private function checkUserWriteAuthorization() {
        if (!$this->userService->isSendingMessagesAllowed()) {
            throw new CVPRChatUnauthorizedAccessException('No write permission');
        }
    }

	/**
	 * @throws Exception
	 */
	private function checkChatOpen() {
		if (!$this->service->isChatOpen()) {
			throw new Exception($this->options->getEncodedOption('message_error_5', 'The chat is closed now'));
		}
	}

	/**
	 * @param CVPRChatChannel $channel
	 * @throws Exception
	 */
	private function checkChannel($channel) {
		if ($channel === null) {
			throw new Exception('Channel does not exist');
		}
	}

	/**
	 * @param CVPRChatChannel $channel
	 * @throws CVPRChatUnauthorizedAccessException
	 */
	private function checkChannelAuthorization($channel) {
		if (
			$channel !== null &&
			strlen($channel->getPassword()) > 0 &&
			!$this->authorization->isUserAuthorizedForChannel($channel)
		) {
			throw new CVPRChatUnauthorizedAccessException('Not authorized in this channel');
		}
	}

	private function generateCheckSum() {
		$checksum = $this->getParam('checksum');

		if ($checksum !== null) {
			$decoded = unserialize(CVPRChatCrypt::decrypt(base64_decode($checksum)));
			if (is_array($decoded)) {
				$decoded['ts'] = time();

				return base64_encode(CVPRChatCrypt::encrypt(serialize($decoded)));
			}
		}

		return null;
	}

	private function verifyCheckSum() {
		$checksum = $this->getParam('checksum');

		if ($checksum !== null) {
			$decoded = unserialize(CVPRChatCrypt::decrypt(base64_decode($checksum)));
			if (is_array($decoded)) {
				$timestamp = array_key_exists('ts', $decoded) ? $decoded['ts'] : time();
				$validityTime = $this->options->getIntegerOption('ajax_validity_time', 1440) * 60;
				if ($timestamp + $validityTime < time()) {
					$this->sendNotFoundStatus();
					die();
				}

				$this->options->replaceOptions($decoded);
			}
		}
	}

	private function verifyXhrRequest() {
		if (!$this->options->isOptionEnabled('enabled_xhr_check', true)) {
			return true;
		}

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return true;
		} else {
			$this->sendNotFoundStatus();
			die();
		}
	}

	private function checkUserRight($rightName) {
		if (!$this->usersDAO->hasCurrentWpUserRight($rightName)) {
			throw new CVPRChatUnauthorizedAccessException('Not enough privileges to execute this request');
		}
	}

	private function sendBadRequestStatus() {
		header('HTTP/1.0 400 Bad Request', true, 400);
	}

	private function sendUnauthorizedStatus() {
		header('HTTP/1.0 401 Unauthorized', true, 401);
	}

	private function sendNotFoundStatus() {
		header('HTTP/1.0 404 Not Found', true, 404);
	}

	private function jsonContentType() {
		header('Content-Type: application/json; charset='.get_option('blog_charset'));
	}
}

/**
 * Class CVPRChatUnauthorizedAccessException
 */
class CVPRChatUnauthorizedAccessException extends Exception { }
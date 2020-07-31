<?php

/**
 * CVPR Chat main services class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatService {

	/**
	* @var CVPRChatUsersDAO
	*/
	private $usersDAO;
	
	/**
	* @var CVPRChatChannelUsersDAO
	*/
	private $channelUsersDAO;
	
	/**
	* @var CVPRChatChannelsDAO
	*/
	private $channelsDAO;
	
	/**
	* @var CVPRChatUserService
	*/
	private $userService;

	/**
	 * @var CVPRChatKicksService
	 */
	private $kicksService;

	/**
	 * @var CVPRChatAuthentication
	 */
	private $authentication;

	/**
	 * @var CVPRChatAuthorization
	 */
	private $authorization;
	
	/**
	* @var CVPRChatOptions
	*/
	private $options;
	
	public function __construct() {
		CVPRChatContainer::load('model/CVPRChatChannel');
		$this->options = CVPRChatOptions::getInstance();
		$this->channelsDAO = CVPRChatContainer::get('dao/CVPRChatChannelsDAO');
		$this->usersDAO = CVPRChatContainer::get('dao/user/CVPRChatUsersDAO');
		$this->channelUsersDAO = CVPRChatContainer::get('dao/CVPRChatChannelUsersDAO');
		$this->userService = CVPRChatContainer::get('services/user/CVPRChatUserService');
		$this->kicksService = CVPRChatContainer::getLazy('services/CVPRChatKicksService');
		$this->authentication = CVPRChatContainer::getLazy('services/user/CVPRChatAuthentication');
		$this->authorization = CVPRChatContainer::getLazy('services/user/CVPRChatAuthorization');
	}

	/**
	 * Validates channel name and returns it.
	 *
	 * @param string $channelName
	 * @return string
	 */
	public function getValidChatChannelName($channelName) {
		return $channelName === null || $channelName === '' ? 'global' : $channelName;
	}

	/**
	 * Creates a channel if it does not exist and returns it.
	 * If channel exists it is just returned.
	 *
	 * @param string $channelName
	 *
	 * @return CVPRChatChannel
	 */
	public function createAndGetChannel($channelName) {
		$channel = $this->channelsDAO->getByName($channelName);
		if ($channel === null) {
			$channel = new CVPRChatChannel();
			$channel->setName($channelName);
			$this->channelsDAO->save($channel);
		}

		return $channel;
	}
	
	/**
	* Returns unique ID for the plugin.
	*
	* @return string
	*/
	public function getChatID() {
		return 'wc'.md5(uniqid('', true));
	}
	
	/**
	* Determines whether the chat is restricted for anonymous users.
	*
	* @return boolean
	*/
	public function isChatRestrictedForAnonymousUsers() {
		return $this->options->getOption('access_mode') == 1 && !$this->usersDAO->isWpUserLogged();
	}

	/**
	 * Determines whether IP is kicked.
	 *
	 * @return boolean
	 */
	public function isIpKicked() {
		return isset($_SERVER['REMOTE_ADDR']) && $this->kicksService->isIpAddressKicked($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Determines whether the chat is allowed only for logged in WP users.
	 *
	 * @return boolean
	 */
	public function isChatAllowedForWPUsersOnly() {
		return $this->options->getOption('access_mode') == 1;
	}

	/**
	 * Determines whether the chat is restricted for user roles.
	 *
	 * @return boolean
	 */
	public function isChatRestrictedForCurrentUserRole() {
		if ($this->options->getOption('access_mode') == 1 && $this->usersDAO->isWpUserLogged()) {
			$targetRoles = (array) $this->options->getOption('access_roles', null);
			if ($targetRoles === null) {
				return false;
			}
			if (!is_array($targetRoles) || count($targetRoles) == 0) {
				return true;
			}

			$wpUser = $this->usersDAO->getCurrentWpUser();
			if (!is_array($wpUser->roles) || count($wpUser->roles) == 0) {
				return true;
			}

			return count(array_intersect($targetRoles, $wpUser->roles)) == 0;
		} else {
			return false;
		}
	}
	
	/**
	* Determines whether the chat is open according to the settings.
	*
	* @return boolean
	*/
	public function isChatOpen() {
		if ($this->options->isOptionEnabled('enable_opening_control', false)) {
			$chatOpeningDays = $this->options->getOption('opening_days');
			if (is_array($chatOpeningDays) && !in_array(date('l'), $chatOpeningDays)) {
				return false;
			}
			
			$chatOpeningHours = $this->options->getOption('opening_hours');
			if (is_array($chatOpeningHours)) {
				$openingHour = isset($chatOpeningHours['opening']) ? $chatOpeningHours['opening'] : '00:00';
				$openingMode = isset($chatOpeningHours['openingMode']) ? $chatOpeningHours['openingMode'] : '24h';
				$startHourDate = null;
				if ($openingMode != '24h') {
					$startHourDate = DateTime::createFromFormat('Y-m-d h:i a', date('Y-m-d') . ' ' . $openingHour . ' ' . $openingMode);
				} else {
					$startHourDate = DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' ' . $openingHour);
				}

				$closingHour = isset($chatOpeningHours['closing']) ? $chatOpeningHours['closing'] : '23:59';
				$closingMode = isset($chatOpeningHours['closingMode']) ? $chatOpeningHours['closingMode'] : '24h';
				$endHourDate = null;
				if ($closingMode != '24h') {
					$endHourDate = DateTime::createFromFormat('Y-m-d h:i a', date('Y-m-d') . ' ' . $closingHour . ' ' . $closingMode);
				} else {
					$endHourDate = DateTime::createFromFormat('Y-m-d H:i', date('Y-m-d') . ' ' . $closingHour);
				}

				if ($startHourDate != null && $endHourDate != null) {
					$nowDate = new DateTime();

					$nowU = $nowDate->format('U');
					$startHourDateU = $startHourDate->format('U');
					$endHourDateU = $endHourDate->format('U');

					if ($startHourDateU <= $endHourDateU) {
						if ($nowU < $startHourDateU || $nowU > $endHourDateU) {
							return false;
						}
					} else {
						if ($nowU > $endHourDateU && $nowU < $startHourDateU) {
							return false;
						}
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	* Determines if the chat is full according to the users limit in the channel.
	*
	* @param CVPRChatChannel $channel
	*
	* @return boolean
	*/
	public function isChatChannelFull($channel) {
		$limit = $this->options->getIntegerOption('channel_users_limit', 0);
		if ($limit > 0) {
			$this->userService->refreshChannelUsersData();
			$amountOfCurrentUsers = $channel != null ? $this->channelUsersDAO->getAmountOfUsersInChannel($channel->getId()) : 0;
			$user = $this->authentication->getUser();
			
			if ($user === null || $channel === null || $this->channelUsersDAO->getActiveByUserIdAndChannelId($user->getId(), $channel->getId()) === null) {
				$amountOfCurrentUsers++;
			}
			
			if ($amountOfCurrentUsers > $limit) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	* Determines whether the current user has to be authorized.
	*
	* @param CVPRChatChannel $channel
	*
	* @return boolean
	*/
	public function hasUserToBeAuthorizedInChannel($channel) {
		return strlen($channel->getPassword()) > 0 && !$this->authorization->isUserAuthorizedForChannel($channel);
	}

	/**
	 * Determines if the current user has to enter his/her name.
	 *
	 * @return bool
	 */
	public function hasUserToBeForcedToEnterName() {
		return $this->options->isOptionEnabled('force_user_name_selection') && !$this->authentication->isAuthenticated();
	}
	
	/**
	* Authorizes the current user in the given channel.
	*
	* @param CVPRChatChannel $channel
	* @param string $password
	*
	* @return boolean
	*/
	public function authorize($channel, $password) {
		if ($channel->getPassword() === md5($password)) {
			$this->authorization->markAuthorizedForChannel($channel);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* Determines if the number of channels that current user participates has been reached.
	*
	* @param CVPRChatChannel $channel
	*
	* @return boolean
	*/
	public function isChatChannelsLimitReached($channel) {
		$limit = $this->options->getIntegerOption('channels_limit', 0);
		if ($limit > 0) {
			$this->userService->refreshChannelUsersData();
			$amountOfChannels = $this->channelUsersDAO->getAmountOfActiveBySessionId(session_id());
			$user = $this->authentication->getUser();
			
			if ($user === null || $channel === null || $this->channelUsersDAO->getActiveByUserIdAndChannelId($user->getId(), $channel->getId()) === null) {
				$amountOfChannels++;
			}
			
			if ($amountOfChannels > $limit) {
				return true;
			}
		}
		
		return false;
	}
}
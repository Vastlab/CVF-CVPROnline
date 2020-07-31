<?php

/**
 * CVPRChat kicks services.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatKicksService {

	/**
	 * @var CVPRChatActions
	 */
	protected $actions;

	/**
	 * @var CVPRChatKicksDAO
	 */
	private $kicksDAO;

	/**
	 * @var CVPRChatUsersDAO
	 */
	private $usersDAO;

	/**
	 * @var CVPRChatMessagesDAO
	 */
	private $messagesDAO;

	/**
	 * @var CVPRChatOptions
	 */
	private $options;

	public function __construct() {
		CVPRChatContainer::load('model/CVPRChatKick');
		$this->options = CVPRChatOptions::getInstance();
		$this->kicksDAO = CVPRChatContainer::getLazy('dao/CVPRChatKicksDAO');
		$this->messagesDAO = CVPRChatContainer::getLazy('dao/CVPRChatMessagesDAO');
		$this->usersDAO = CVPRChatContainer::getLazy('dao/user/CVPRChatUsersDAO');
		$this->actions = CVPRChatContainer::getLazy('services/user/CVPRChatActions');
	}

	/**
	 * Kicks the user by message ID.
	 *
	 * @param integer $messageId
	 *
	 * @throws Exception If the message or user was not found
	 */
	public function kickByMessageId($messageId) {
		$message = $this->messagesDAO->get($messageId);
		if ($message === null) {
			throw new Exception('Message was not found');
		}

		$user = $this->usersDAO->get($message->getUserId());
		if ($user !== null) {
			$this->kickIpAddress($user->getIp(), $user->getName());
			$this->actions->publishAction('reload', array(), $user);

			return;
		}

		throw new Exception('User was not found');
	}

	/**
	 * Creates and saves a new kick on IP address if the IP was not kicked previously.
	 *
	 * @param string $ip Given IP address
	 * @param string $userName
	 *
	 * @return boolean Returns true the kick was created
	 */
	public function kickIpAddress($ip, $userName) {
		if ($this->kicksDAO->getByIp($ip) === null) {
			$kick = new CVPRChatKick();
			$kick->setCreated(time());
			$kick->setLastUserName($userName);
			$kick->setIp($ip);
			$this->kicksDAO->save($kick);

			return true;
		}

		return false;
	}

	/**
	 * Checks if given IP address is kicked,
	 *
	 * @param string $ip
	 * @return bool
	 */
	public function isIpAddressKicked($ip) {
		return $this->kicksDAO->getByIp($ip) !== null;
	}

}
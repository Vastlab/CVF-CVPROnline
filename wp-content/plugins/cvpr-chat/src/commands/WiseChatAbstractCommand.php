<?php

/**
 * CVPRChat abstract command.
 *
 * @author Kainex <contact@kaine.pl>
 */
abstract class CVPRChatAbstractCommand {

	/**
	* @var CVPRChatChannel
	*/
	protected $channel;
	
	/**
	* @var string
	*/
	protected $arguments;
	
	/**
	* @var CVPRChatMessagesDAO
	*/
	protected $messagesDAO;
	
	/**
	* @var CVPRChatUsersDAO
	*/
	protected $usersDAO;
	
	/**
	* @var CVPRChatChannelUsersDAO
	*/
	protected $channelUsersDAO;
	
	/**
	* @var CVPRChatBansDAO
	*/
	protected $bansDAO;

	/**
	 * @var CVPRChatAuthentication
	 */
	protected $authentication;

	/**
	 * @var CVPRChatBansService
	 */
	protected $bansService;

	/**
	 * @var CVPRChatMessagesService
	 */
	private $messagesService;

	/**
	 * @param CVPRChatChannel $channel
	 * @param array $arguments
	 */
	public function __construct($channel, $arguments) {
		$this->messagesDAO = CVPRChatContainer::get('dao/CVPRChatMessagesDAO');
		$this->bansDAO = CVPRChatContainer::get('dao/CVPRChatBansDAO');
		$this->usersDAO = CVPRChatContainer::get('dao/user/CVPRChatUsersDAO');
		$this->channelUsersDAO = CVPRChatContainer::get('dao/CVPRChatChannelUsersDAO');
		$this->authentication = CVPRChatContainer::getLazy('services/user/CVPRChatAuthentication');
		$this->bansService = CVPRChatContainer::get('services/CVPRChatBansService');
		$this->messagesService = CVPRChatContainer::get('services/CVPRChatMessagesService');
		$this->arguments = $arguments;
		$this->channel = $channel;
	}
	
	protected function addMessage($message) {
		$this->messagesService->addMessage($this->authentication->getSystemUser(), $this->channel, $message, true);
	}

    /**
     * Executes command using arguments.
     *
     * @return null
     */
    abstract public function execute();
}
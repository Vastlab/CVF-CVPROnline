<?php

/**
 * WiseChat pending chats services.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatPendingChatsService {

	 const MESSAGE_SUMMARY_MAX_LENGTH = 50;

	/**
	 * @var WiseChatPendingChatsDAO
	 */
	private $pendingChatsDAO;

	/**
	 * @var WiseChatRenderer
	 */
	private $renderer;

	/**
	 * @var WiseChatUsersDAO
	 */
	private $usersDAO;

	/**
	 * @var WiseChatMessagesDAO
	 */
	private $messagesDAO;

	/**
	 * @var WiseChatOptions
	 */
	private $options;

	public function __construct() {
		WiseChatContainer::load('model/WiseChatPendingChat');
		$this->options = WiseChatOptions::getInstance();
		$this->pendingChatsDAO = WiseChatContainer::getLazy('dao/WiseChatPendingChatsDAO');
		$this->renderer = WiseChatContainer::get('rendering/WiseChatRenderer');
		$this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
		$this->messagesDAO = WiseChatContainer::get('dao/WiseChatMessagesDAO');
	}

	/**
	 * Creates new pending chat for given message and channel.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 */
	public function addPendingChat($message, $channel) {
		$pendingChat = new WiseChatPendingChat();
		$pendingChat->setChannelId($channel->getId());
		$pendingChat->setUserId($message->getUserId());
		$pendingChat->setRecipientId($message->getRecipientId());
		$pendingChat->setMessageId($message->getId());
		$pendingChat->setTime(time());
		$pendingChat->setChecked(false);
		$this->pendingChatsDAO->save($pendingChat);
	}

	/**
	 * Returns unchecked pending chat for given message and channel.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 *
	 * @return boolean
	 */
	public function containsUncheckedPendingChats($message, $channel) {
		return count($this->pendingChatsDAO->getAllUncheckedForUserRecipientAndChannel($message->getUserId(), $message->getRecipientId(), $channel->getId())) > 0;
	}

	/**
	 * Returns unchecked pending chats for user and channel.
	 *
	 * @param WiseChatUser $user
	 * @param WiseChatChannel $channel
	 *
	 * @return array
	 */
	public function getUncheckedPendingChats($user, $channel) {
		if ($user === null) {
			return array();
		}

		$duplicationCheck = array();
		$chats = array();
		$pendingChats = $this->pendingChatsDAO->getAllUncheckedForRecipientAndChannel($user->getId(), $channel->getId());
		foreach ($pendingChats as $pendingChat) {
			$user = $this->usersDAO->get($pendingChat->getUserId());
			if ($user === null) {
				continue;
			}

			$hash = WiseChatUserService::getUserHash($user->getId());
			if (array_key_exists($hash, $duplicationCheck)) {
				continue;
			}

			$message = $this->messagesDAO->get($pendingChat->getMessageId());
			$messageSummary = $message !== null ? strip_tags($message->getText()) : '';
			if (strlen($messageSummary) > self::MESSAGE_SUMMARY_MAX_LENGTH) {
				$messageSummary = substr($messageSummary, 0, self::MESSAGE_SUMMARY_MAX_LENGTH).' ...';
			}

			$chats[] = array(
				'id' => $this->renderer->getUserPublicIdForChannel($user, $channel),
				'name' => $user->getName(),
				'hash' => $hash,
				'message' => $messageSummary,
				'date' => $message !== null ? gmdate('c', $message->getTime()) : ''
			);
			$duplicationCheck[$hash] = true;
		}

		return $chats;
	}

	/**
	 * Sets pending chats as checked.
	 *
	 * @param string $userPublicId Sender encrypted ID
	 * @param WiseChatUser $recipient
	 * @param WiseChatChannel $channel
	 */
	public function setPendingChatChecked($userPublicId, $recipient, $channel) {
		$decryptedData = unserialize(WiseChatCrypt::decrypt(base64_decode($userPublicId)));

		if (is_array($decryptedData) && $recipient !== null && $channel !== null) {
			$userId = intval($decryptedData[0]);

			$pendingChats = $this->pendingChatsDAO->getAllUncheckedForUserRecipientAndChannel($userId, $recipient->getId(), $channel->getId());
			foreach ($pendingChats as $pendingChat) {
				$pendingChat->setChecked(true);
				$this->pendingChatsDAO->save($pendingChat);
			}
		}

	}
}
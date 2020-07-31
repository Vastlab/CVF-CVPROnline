<?php

/**
 * Wise Chat pending chats DAO.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatPendingChatsDAO {

	/**
	 * @var WiseChatUsersDAO
	 */
	private $usersDAO;

	/**
	 * @var WiseChatOptions
	 */
	private $options;

	public function __construct() {
		WiseChatContainer::load('model/WiseChatPendingChat');
		$this->options = WiseChatOptions::getInstance();
		$this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
	}

	/**
	 * Creates or updates a pending chat.
	 *
	 * @param WiseChatPendingChat $pendingChat
	 *
	 * @return WiseChatPendingChat
	 * @throws Exception On validation error
	 */
	public function save($pendingChat) {
		global $wpdb;

		// low-level validation:
		if ($pendingChat->getUserId() === null) {
			throw new Exception('User ID is required');
		}
		if ($pendingChat->getRecipientId() === null) {
			throw new Exception('Recipient ID is required');
		}
		if ($pendingChat->getMessageId() === null) {
			throw new Exception('Message ID is required');
		}
		if ($pendingChat->getChannelId() === null) {
			throw new Exception('Channel ID is required');
		}

		// prepare pending chat data:
		$table = WiseChatInstaller::getPendingChatsTable();
		$columns = array(
			'channel_id' => $pendingChat->getChannelId(),
			'user_id' => $pendingChat->getUserId(),
			'recipient_id' => $pendingChat->getRecipientId(),
			'message_id' => $pendingChat->getMessageId(),
			'checked' => $pendingChat->isChecked() === true ? '1' : '0',
			'time' => $pendingChat->getTime()
		);

		// update or insert:
		if ($pendingChat->getId() !== null) {
			$wpdb->update($table, $columns, array('id' => $pendingChat->getId()), '%s', '%d');
		} else {
			if ($columns['time'] === null) {
				$columns['time'] = time();
			}
			$wpdb->insert($table, $columns);
			$pendingChat->setId($wpdb->insert_id);
		}

		return $pendingChat;
	}

	/**
	 * Returns unchecked pending chats for sender, recipient and channel.
	 *
	 * @param integer $userId Message sender
	 * @param integer $recipientId Message recipient
	 * @param integer $channelId
	 *
	 * @return WiseChatPendingChat[]
	 */
	public function getAllUncheckedForUserRecipientAndChannel($userId, $recipientId, $channelId) {
		global $wpdb;

		$table = WiseChatInstaller::getPendingChatsTable();
		$sql = sprintf(
			'SELECT * FROM %s WHERE checked = "0" AND user_id = %d AND channel_id = %d AND recipient_id = %d ORDER BY ID DESC;',
			$table, intval($userId), intval($channelId), intval($recipientId)
		);

		$chats = array();
		$results = $wpdb->get_results($sql);
		if (is_array($results)) {
			foreach ($results as $result) {
				$chats[] = $this->populateData($result);
			}
		}

		return $chats;
	}

	/**
	 * Returns all unchecked pending chats for recipient and channel.
	 *
	 * @param integer $recipientId
	 * @param integer $channelId
	 *
	 * @return WiseChatPendingChat[]
	 */
	public function getAllUncheckedForRecipientAndChannel($recipientId, $channelId) {
		global $wpdb;

		$table = WiseChatInstaller::getPendingChatsTable();
		$sql = sprintf(
			'SELECT * FROM %s WHERE checked = "0" AND channel_id = %d AND recipient_id = %d ORDER BY ID DESC;',
			$table, intval($channelId), intval($recipientId)
		);

		$chats = array();
		$results = $wpdb->get_results($sql);
		if (is_array($results)) {
			foreach ($results as $result) {
				$chats[] = $this->populateData($result);
			}
		}

		return $chats;
	}

	/**
	 * Converts stdClass object into WiseChatPendingChat object.
	 *
	 * @param stdClass $pendingChatRaw
	 *
	 * @return WiseChatPendingChat
	 */
	private function populateData($pendingChatRaw) {
		$pendingChat = new WiseChatPendingChat();
		if ($pendingChatRaw->id > 0) {
			$pendingChat->setId(intval($pendingChatRaw->id));
		}
		if ($pendingChatRaw->user_id > 0) {
			$pendingChat->setUserId(intval($pendingChatRaw->user_id));
		}
		if ($pendingChatRaw->recipient_id > 0) {
			$pendingChat->setRecipientId(intval($pendingChatRaw->recipient_id));
		}
		if ($pendingChatRaw->channel_id > 0) {
			$pendingChat->setChannelId(intval($pendingChatRaw->channel_id));
		}
		if ($pendingChatRaw->message_id > 0) {
			$pendingChat->setMessageId(intval($pendingChatRaw->message_id));
		}
		$pendingChat->setChecked($pendingChatRaw->checked == '1');
		if ($pendingChatRaw->time > 0) {
			$pendingChat->setTime(intval($pendingChatRaw->time));
		}

		return $pendingChat;
	}

}
<?php

/**
 * Wise Chat sent notifications DAO.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatSentNotificationsDAO {

	/**
	 * @var WiseChatOptions
	 */
	private $options;

	public function __construct() {
		WiseChatContainer::load('model/WiseChatSentNotification');
		$this->options = WiseChatOptions::getInstance();
	}

	/**
	 * Creates or updates a sent notification.
	 *
	 * @param WiseChatSentNotification $sentNotification
	 *
	 * @return WiseChatSentNotification
	 * @throws Exception On validation error
	 */
	public function save($sentNotification) {
		global $wpdb;

		// low-level validation:
		if ($sentNotification->getNotificationId() === null) {
			throw new Exception('Notification ID is required');
		}
		if ($sentNotification->getSentTime() === null) {
			throw new Exception('Sent time is required');
		}

		// prepare pending chat data:
		$table = WiseChatInstaller::getSentNotificationsTable();
		$columns = array(
			'channel_id' => $sentNotification->getChannelId(),
			'user_id' => $sentNotification->getUserId(),
			'notification_id' => $sentNotification->getNotificationId(),
			'sent_time' => $sentNotification->getSentTime()
		);

		// update or insert:
		if ($sentNotification->getId() !== null) {
			$wpdb->update($table, $columns, array('id' => $sentNotification->getId()), '%s', '%s');
		} else {
			if ($columns['sent_time'] === null) {
				$columns['sent_time'] = time();
			}
			$wpdb->insert($table, $columns);
			$sentNotification->setId($wpdb->insert_id);
		}

		return $sentNotification;
	}

	/**
	 * Returns sent notification by ID.
	 *
	 * @param integer $id
	 *
	 * @return WiseChatSentNotification|null
	 */
	public function get($id) {
		global $wpdb;

		$table = WiseChatInstaller::getSentNotificationsTable();
		$sql = sprintf('SELECT * FROM %s WHERE id = %d;', $table, intval($id));
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			return $this->populateData($results[0]);
		}

		return null;
	}

	/**
	 * Checks if the notification was sent in the given time range and for given channel ID.
	 *
	 * @param string $notificationId
	 * @param integer $channelId
	 * @param integer $timeRange Time range in seconds
	 * @return boolean
	 */
	public function wasNotificationSend($notificationId, $channelId, $timeRange) {
		global $wpdb;

		$table = WiseChatInstaller::getSentNotificationsTable();
		$sql = sprintf(
			'SELECT count(*) AS quantity FROM %s WHERE channel_id = %d AND user_id IS NULL AND notification_id = "%s" AND sent_time >= %d;',
			$table, intval($channelId), addslashes($notificationId), time() - intval($timeRange)
		);

		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $result->quantity > 0;
		}

		return false;
	}

	/**
	 * Checks if the notification was sent in the given time range, for given channel ID and user ID.
	 *
	 * @param string $notificationId
	 * @param integer $channelId
	 * @param integer $userId
	 * @param integer $timeRange Time range in seconds
	 * @return boolean
	 */
	public function wasNotificationSendForUser($notificationId, $channelId, $userId, $timeRange) {
		global $wpdb;

		$table = WiseChatInstaller::getSentNotificationsTable();
		$sql = sprintf(
			'SELECT count(*) AS quantity FROM %s WHERE channel_id = %d AND user_id = %d AND notification_id = "%s" AND sent_time >= %d;',
			$table, intval($channelId), intval($userId), addslashes($notificationId), time() - intval($timeRange)
		);

		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $result->quantity > 0;
		}

		return false;
	}

	/**
	 * Converts stdClass object into WiseChatSentNotification object.
	 *
	 * @param stdClass $sentNotificationRaw
	 *
	 * @return WiseChatSentNotification
	 */
	private function populateData($sentNotificationRaw) {
		$sentNotification = new WiseChatSentNotification();
		if ($sentNotificationRaw->id > 0) {
			$sentNotification->setId(intval($sentNotificationRaw->id));
		}
		if ($sentNotificationRaw->user_id > 0) {
			$sentNotification->setUserId(intval($sentNotificationRaw->user_id));
		}
		if ($sentNotificationRaw->channel_id > 0) {
			$sentNotification->setChannelId(intval($sentNotificationRaw->channel_id));
		}
		$sentNotification->setNotificationId($sentNotificationRaw->notification_id);
		if ($sentNotificationRaw->sent_time > 0) {
			$sentNotification->setSentTime(intval($sentNotificationRaw->sent_time));
		}

		return $sentNotification;
	}

}
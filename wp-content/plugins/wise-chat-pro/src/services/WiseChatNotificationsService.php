<?php

/**
 * WiseChat notifications services.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatNotificationsService {

	/**
	 * @var WiseChatNotificationsDAO
	 */
	private $notificationsDAO;

	/**
	 * @var WiseChatSentNotificationsDAO
	 */
	private $sentNotificationsDAO;

	/**
	 * @var WiseChatOptions
	 */
	private $options;

	public function __construct() {
		$this->options = WiseChatOptions::getInstance();
		$this->notificationsDAO = WiseChatContainer::getLazy('dao/WiseChatNotificationsDAO');
		$this->sentNotificationsDAO = WiseChatContainer::getLazy('dao/WiseChatSentNotificationsDAO');
	}

	/**
	 * Sends all notifications for message.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 */
	public function send($message, $channel) {
		foreach ($this->notificationsDAO->getAll() as $notification) {
			$this->sendNotification($message, $channel, $notification);
		}
	}

	/**
	 * Sends the notification.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 * @param WiseChatNotification $notification
	 */
	private function sendNotification($message, $channel, $notification) {
		if ($notification->getType() == 'email') {
			$this->sendEmailNotification($message, $channel, $notification);
		}
	}

	/**
	 * Sends e-mail notification.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 * @param WiseChatNotification $notification
	 */
	private function sendEmailNotification($message, $channel, $notification) {
		$timeRange = null; // seconds

		if ($notification->getFrequency() == 'daily') {
			$timeRange = 24 * 60 * 60;
		}
		if ($notification->getFrequency() == 'hourly') {
			$timeRange = 60 * 60;
		}
		if ($timeRange === null) {
			return;
		}

		$sendNotificationFlag = false;

		if ($notification->getAction() == 'message') {
			if (!$this->sentNotificationsDAO->wasNotificationSend($notification->getId(), $channel->getId(), $timeRange)) {
				$sendNotificationFlag = true;
			}
		}

		if ($notification->getAction() == 'message-of-user') {
			if (!$this->sentNotificationsDAO->wasNotificationSendForUser($notification->getId(), $channel->getId(), $message->getUserId(), $timeRange)) {
				$sendNotificationFlag = true;
			}
		}

		if (!$sendNotificationFlag) {
			return;
		}

		$sentNotification = new WiseChatSentNotification();
		$sentNotification->setNotificationId($notification->getId());
		$sentNotification->setSentTime(time());
		$sentNotification->setChannelId($channel->getId());
		if ($notification->getAction() == 'message-of-user') {
			$sentNotification->setUserId($message->getUserId());
		}
		$this->sentNotificationsDAO->save($sentNotification);

		// send e-mail:
		$templateData = array(
			'${user}' => $message->getUserName(),
			'${message}' => $message->getText(),
			'${channel}' => $message->getChannelName()
		);
		$emailSubject = str_replace(array_keys($templateData), array_values($templateData), $notification->getDetails()['subject']);
		$emailBody = str_replace(array_keys($templateData), array_values($templateData), $notification->getDetails()['content']);

		wp_mail($notification->getDetails()['recipientEmail'], $emailSubject, $emailBody);
	}
}
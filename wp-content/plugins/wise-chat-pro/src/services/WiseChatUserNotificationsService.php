<?php

/**
 * WiseChat user notifications services.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatUserNotificationsService {

	/**
	 * @var WiseChatUserNotificationsDAO
	 */
	private $userNotificationsDAO;

	/**
	 * @var WiseChatSentNotificationsDAO
	 */
	private $sentNotificationsDAO;

	/**
	 * @var WiseChatUsersDAO
	 */
	private $usersDAO;

	/**
	 * @var WiseChatChannelUsersDAO
	 */
	private $channelUsersDAO;

	/**
	 * @var WiseChatHttpRequestService
	 */
	private $httpRequestService;

	/**
	 * @var WiseChatOptions
	 */
	private $options;

	public function __construct() {
		$this->options = WiseChatOptions::getInstance();
		$this->userNotificationsDAO = WiseChatContainer::getLazy('dao/WiseChatUserNotificationsDAO');
		$this->sentNotificationsDAO = WiseChatContainer::getLazy('dao/WiseChatSentNotificationsDAO');
		$this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
		$this->channelUsersDAO = WiseChatContainer::get('dao/WiseChatChannelUsersDAO');
		$this->httpRequestService = WiseChatContainer::getLazy('services/WiseChatHttpRequestService');
	}

	/**
	 * Sends all notifications for message.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 */
	public function send($message, $channel) {
		foreach ($this->userNotificationsDAO->getAll() as $notification) {
			$this->sendNotification($message, $channel, $notification);
		}
	}

	/**
	 * Sends the notification.
	 *
	 * @param WiseChatMessage $message
	 * @param WiseChatChannel $channel
	 * @param WiseChatUserNotification $notification
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
	 * @param WiseChatUserNotification $notification
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

		if ($this->sentNotificationsDAO->wasNotificationSendForUser($notification->getId(), $channel->getId(), $message->getUserId(), $timeRange)) {
			return;
		}

		$recipientUser = $this->usersDAO->get($message->getRecipientId());
		if ($recipientUser === null) {
			return;
		}

		$channelUser = $this->channelUsersDAO->getActiveByUserIdAndChannelId($recipientUser->getId(), $channel->getId());
		if ($channelUser !== null) {
			return;
		}

		if ($recipientUser->getDataProperty('disableNotifications') === true) {
			return;
		}

		if (!($recipientUser->getWordPressId() > 0)) {
			return;
		}

		$recipientWordPressUser = $this->usersDAO->getWpUserByID($recipientUser->getWordPressId());
		if ($recipientWordPressUser === null) {
			return;
		}

		$sentNotification = new WiseChatSentNotification();
		$sentNotification->setNotificationId($notification->getId());
		$sentNotification->setSentTime(time());
		$sentNotification->setChannelId($channel->getId());
		$sentNotification->setUserId($message->getUserId());
		$this->sentNotificationsDAO->save($sentNotification);

		// send the e-mail:
		$templateData = array(
			'${recipient}' => $recipientWordPressUser->display_name,
			'${recipient-email}' => $recipientWordPressUser->user_email,
			'${sender}' => strip_tags($message->getUserName()),
			'${message}' => $message->getText(),
			'${channel}' => $message->getChannelName(),
			'${link}' => $this->httpRequestService->getReferrerURL()
		);
		$emailSubject = str_replace(array_keys($templateData), array_values($templateData), $notification->getDetails()['subject']);
		$emailBody = str_replace(array_keys($templateData), array_values($templateData), $notification->getDetails()['content']);

		wp_mail($recipientWordPressUser->user_email, $emailSubject, $emailBody);
	}
}
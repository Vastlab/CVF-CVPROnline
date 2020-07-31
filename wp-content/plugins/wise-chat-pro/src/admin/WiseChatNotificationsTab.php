<?php

/**
 * Wise Chat admin messages notifications tab class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatNotificationsTab extends WiseChatAbstractTab {

	public function getFields() {
		return array(
			array('_section', 'User Notifications', 'User notifications are sent when a private message is posted. Note that only registered WordPress users can receive such notifications. If the user is currently online then no notification is sent.', array('hideSubmitButton' => true)),
			array('user_notifications', 'E-mail Notifications', 'userNotificationsListCallback', 'void'),
			array('user_notification_add', 'New E-mail Notification', 'userNotificationAddCallback', 'void'),

			array('_section', 'Admin Notifications', 'Admin notifications are sent when a message is posted in the chat\'s public channel.', array('hideSubmitButton' => true)),
			array('notifications', 'E-mail Notifications', 'notificationsListCallback', 'void'),
			array('notification_add', 'New E-mail Notification', 'notificationAddCallback', 'void'),
		);
	}

	public function getDefaultValues() {
		return array(
			'notifications' => null,
			'notification_add' => null,
			'user_notifications' => null,
			'user_notification_add' => null,
		);
	}

	public function addNotificationAction() {
		$action = $_GET['action'];
		$frequency = $_GET['frequency'];
		$recipientEmail = stripslashes($_GET['recipientEmail']);
		$subject = stripslashes($_GET['subject']);
		$content = stripslashes($_GET['content']);

		try {
			if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
				throw new Exception('E-mail is not valid');
			}

			$notification = new WiseChatNotification();
			$notification->setType('email');
			$notification->setAction($action);
			$notification->setFrequency($frequency);
			$notification->setDetails(array(
				'recipientEmail' => $recipientEmail,
				'subject' => $subject,
				'content' => $content,
			));

			$this->notificationsDAO->save($notification);
			$this->addMessage('Notification has been added');
		} catch (Exception $ex) {
			$this->addErrorMessage($ex->getMessage());
		}
	}

	public function editNotificationAction() {
		$id = $_GET['notificationId'];

		$notification = $this->notificationsDAO->get($id);
		if ($notification === null) {
			$this->addErrorMessage('Notification does not exist');
		} else {
			$action = $_GET['action'];
			$frequency = $_GET['frequency'];
			$recipientEmail = stripslashes($_GET['recipientEmail']);
			$subject = stripslashes($_GET['subject']);
			$content = stripslashes($_GET['content']);

			try {
				if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
					throw new Exception('E-mail is not valid');
				}

				$notification->setAction($action);
				$notification->setFrequency($frequency);
				$notification->setDetails(array(
					'recipientEmail' => $recipientEmail,
					'subject' => $subject,
					'content' => $content,
				));

				$this->notificationsDAO->save($notification);
				$this->addMessage('Notification has been saved');
			} catch (Exception $ex) {
				$this->addErrorMessage($ex->getMessage());
			}
		}
	}

	public function deleteNotificationAction() {
		$id = $_GET['id'];

		$this->notificationsDAO->delete($id);
		$this->addMessage('Notification has been deleted');
	}

	public function notificationsListCallback() {
		$url = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG);

		$notifications = $this->notificationsDAO->getAll();

		$html = "<table class='wp-list-table widefat'>";
		if (count($notifications) == 0) {
			$html .= '<tr><td>No notifications created yet</td></tr>';
		} else {
			$html .= '<thead><tr><th>&nbsp;Send when</th><th>No more than</th><th>E-mail</th><th>Subject</th><th></th></tr></thead>';
		}

		foreach ($notifications as $key => $notification) {
			$deleteURL = $url.'&wc_action=deleteNotification&id='.$notification->getId().'&tab=notifications';
			$editLink = '<a href="javascript://" title="Edit notification" onclick="jQuery(\'#editNotification'.$notification->getId().'\').toggle()">Edit</a>';
			$deleteLink = "<a href='{$deleteURL}' title='Delete notification' onclick='return confirm(\"Are you sure you want to delete this notification?\")'>Delete</a>";

			$html .= sprintf(
				'<tr class="%s"><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s | %s</td></tr>',
				($key % 2 == 0 ? 'alternate' : ''),
				$this->notificationsDAO->getAllActions()[$notification->getAction()],
				$this->notificationsDAO->getAllFrequencies()[$notification->getFrequency()],
				$notification->getDetails()['recipientEmail'],
				$notification->getDetails()['subject'],
				$editLink,
				$deleteLink
			);
			$html .= sprintf(
				'<tr id="editNotification%s" class="%s" style="display: none"><td colspan="5">%s</td></tr>',
				$notification->getId(),
				($key % 2 == 0 ? 'alternate' : ''),
				$this->getNotificationForm($notification)
			);
		}
		$html .= '</table>';

		print($html);
	}

	public function notificationAddCallback() {
		print($this->getNotificationForm(null));
	}

	/**
	 * @param WiseChatNotification $notification
	 * @return string HTML form
	 */
	private function getNotificationForm($notification) {
		$details = $notification !== null ? $notification->getDetails() : array();
		$currentUser = wp_get_current_user();
		$url = $notification !== null
				? admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=editNotification&notificationId=".$notification->getId())
				: admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=addNotification");

		// actions:
		$actionsHtmlOptions = '';
		foreach ($this->notificationsDAO->getAllActions() as $key => $option) {
			$actionsHtmlOptions .= sprintf('<option value="%s" %s>%s</option>', $key, $notification !== null && $notification->getAction() == $key ? 'selected' : '', $option);
		}

		// frequencies:
		$frequenciesHtmlOptions = '';
		foreach ($this->notificationsDAO->getAllFrequencies() as $key => $option) {
			$frequenciesHtmlOptions .= sprintf('<option value="%s" %s>%s</option>', $key, $notification !== null && $notification->getFrequency() == $key ? 'selected' : '', $option);
		}

		$recipient = $notification !== null
			? (array_key_exists('recipientEmail', $details) ? $details['recipientEmail'] : '')
			: ($currentUser instanceof WP_User ? $currentUser->user_email : '');

		$subject = $notification !== null
			? (array_key_exists('subject', $details) ? $details['subject'] : '')
			: 'New Message in Chat';

		$content = $notification !== null
			? (array_key_exists('content', $details) ? $details['content'] : '')
			: sprintf("Hello%s,\n\nA new message has been posted in the chat.\n\nUser: \${user}\nChannel: \${channel}\nMessage: \${message}\n\nBest regards,\n%s", $currentUser instanceof WP_User ? ' '.$currentUser->display_name : '', get_bloginfo( 'name' ));

		$buttonLabel = $notification !== null ? 'Save Notification' : 'Add Notification';

		return sprintf(
			'<table class="wp-list-table widefat wc-notification-form">'.
				'<tr>'.
					'<td class="th-full" width="150">Send when:</td>'.
					'<td>
						<select id="notificationAction">%s</select>
						<p class="description" style="display: inline;"></p>
					</td>'.
				'</tr>'.
				'<tr>'.
					'<td class="th-full">No more than:</td>'.
					'<td>
						<label><select id="notificationFrequency">%s</select></label>
					</td>'.
				'</tr>'.
				'<tr>'.
					'<td class="th-full">E-mail:</td>'.
					'<td><input type="email" value="%s" placeholder="E-mail" id="notificationRecipientEmail" style="width: 100%%;" /></td>'.
				'</tr>'.
				'<tr>'.
					'<td class="th-full">Subject:</td>'.
					'<td><input type="text" value="%s" placeholder="Subject" id="notificationSubject" style="width: 100%%;" /></td>'.
				'</tr>'.
				'<tr>'.
					'<td class="th-full">Content:</td>'.
					'<td>
						<textarea placeholder="Content" id="notificationContent" rows="10" style="width: 100%%;">%s</textarea>
						<p class="description">Available variables: ${user}, ${message}, ${channel}</p>
					</td>'.
				'</tr>'.
				'<tr>'.
					'<td colspan="2"><a class="button-secondary wc-save-notification-button" href="%s">%s</a></td>'.
				'</tr>'.
			'</table>',

			$actionsHtmlOptions,
			$frequenciesHtmlOptions,
			$recipient,
			$subject,
			$content,
			wp_nonce_url($url),
			$buttonLabel
		);
	}

	public function addUserNotificationAction() {
		$frequency = $_GET['frequency'];
		$subject = stripslashes($_GET['subject']);
		$content = stripslashes($_GET['content']);

		try {
			$notification = new WiseChatUserNotification();
			$notification->setType('email');
			$notification->setFrequency($frequency);
			$notification->setDetails(array(
				'subject' => $subject,
				'content' => $content,
			));

			$this->userNotificationsDAO->save($notification);
			$this->addMessage('User notification has been added');
		} catch (Exception $ex) {
			$this->addErrorMessage($ex->getMessage());
		}
	}

	public function editUserNotificationAction() {
		$id = $_GET['notificationId'];

		$notification = $this->userNotificationsDAO->get($id);
		if ($notification === null) {
			$this->addErrorMessage('User notification does not exist');
		} else {
			$frequency = $_GET['frequency'];
			$subject = stripslashes($_GET['subject']);
			$content = stripslashes($_GET['content']);

			try {
				$notification->setFrequency($frequency);
				$notification->setDetails(array(
					'subject' => $subject,
					'content' => $content,
				));

				$this->userNotificationsDAO->save($notification);
				$this->addMessage('User notification has been saved');
			} catch (Exception $ex) {
				$this->addErrorMessage($ex->getMessage());
			}
		}
	}

	public function deleteUserNotificationAction() {
		$id = $_GET['id'];

		$this->userNotificationsDAO->delete($id);
		$this->addMessage('User notification has been deleted');
	}

	/**
	 * @param WiseChatUserNotification $notification
	 * @return string HTML form
	 */
	private function getUserNotificationForm($notification) {
		$details = $notification !== null ? $notification->getDetails() : array();
		$url = $notification !== null
			? admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=editUserNotification&notificationId=".$notification->getId())
			: admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=addUserNotification");

		// frequencies:
		$frequenciesHtmlOptions = '';
		foreach ($this->userNotificationsDAO->getAllFrequencies() as $key => $option) {
			$frequenciesHtmlOptions .= sprintf('<option value="%s" %s>%s</option>', $key, $notification !== null && $notification->getFrequency() == $key ? 'selected' : '', $option);
		}

		$subject = $notification !== null
			? (array_key_exists('subject', $details) ? $details['subject'] : '')
			: 'New Private Message from ${sender}';

		$content = $notification !== null
			? (array_key_exists('content', $details) ? $details['content'] : '')
			: "Hello \${recipient},\n\nA new message has been sent to you in the chat.\n\nSender: \${sender}\nGo to the chat page: \${link}\n\nBest regards,\n".get_bloginfo( 'name' );

		$buttonLabel = $notification !== null ? 'Save Notification' : 'Add Notification';

		return sprintf(
			'<table class="wp-list-table widefat wc-user-notification-form">'.
				'<tr>'.
					'<td class="th-full">No more than:</td>'.
					'<td>
						<label><select id="userNotificationFrequency">%s</select></label> from each user separately
					</td>'.
				'</tr>'.
				'<tr>'.
					'<td class="th-full">Subject:</td>'.
					'<td><input type="text" value="%s" placeholder="Subject" id="userNotificationSubject" style="width: 100%%;" /></td>'.
				'</tr>'.
				'<tr>'.
					'<td class="th-full">Content:</td>'.
					'<td>
						<textarea placeholder="Content" id="userNotificationContent" rows="10" style="width: 100%%;">%s</textarea>
						<p class="description">Available variables: ${recipient}, ${recipient-email}, ${sender}, ${link}, ${message}, ${channel}</p>
					</td>'.
				'</tr>'.
				'<tr>'.
					'<td colspan="2"><a class="button-secondary wc-save-user-notification-button" href="%s">%s</a></td>'.
				'</tr>'.
			'</table>',

			$frequenciesHtmlOptions,
			$subject,
			$content,
			wp_nonce_url($url),
			$buttonLabel
		);
	}

	public function userNotificationsListCallback() {
		$url = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG);

		$notifications = $this->userNotificationsDAO->getAll();

		$html = "<table class='wp-list-table widefat'>";
		if (count($notifications) == 0) {
			$html .= '<tr><td>No user notifications created yet</td></tr>';
		} else {
			$html .= '<thead><tr><th>No more than</th><th>Subject</th><th></th></tr></thead>';
		}

		foreach ($notifications as $key => $notification) {
			$deleteURL = $url.'&wc_action=deleteUserNotification&id='.$notification->getId().'&tab=notifications';
			$editLink = '<a href="javascript://" title="Edit user notification" onclick="jQuery(\'#editUserNotification'.$notification->getId().'\').toggle()">Edit</a>';
			$deleteLink = "<a href='{$deleteURL}' title='Delete user notification' onclick='return confirm(\"Are you sure you want to delete this notification?\")'>Delete</a>";

			$html .= sprintf(
				'<tr class="%s"><td>%s</td><td>%s</td><td>%s | %s</td></tr>',
				($key % 2 == 0 ? 'alternate' : ''),
				$this->userNotificationsDAO->getAllFrequencies()[$notification->getFrequency()],
				$notification->getDetails()['subject'],
				$editLink,
				$deleteLink
			);
			$html .= sprintf(
				'<tr id="editUserNotification%s" class="%s" style="display: none"><td colspan="5">%s</td></tr>',
				$notification->getId(),
				($key % 2 == 0 ? 'alternate' : ''),
				$this->getUserNotificationForm($notification)
			);
		}
		$html .= '</table>';

		print($html);
	}

	public function userNotificationAddCallback() {
		print($this->getUserNotificationForm(null));
	}
}
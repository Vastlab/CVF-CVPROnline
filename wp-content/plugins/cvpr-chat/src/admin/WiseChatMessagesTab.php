<?php 

/**
 * CVPR Chat admin messages settings tab class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatMessagesTab extends CVPRChatAbstractTab {

	public function getFields() {
		return array(
			array('_section', 'General Settings'),
			array('message_max_length', 'Message Maximum Length', 'stringFieldCallback', 'integer', 'Maximum length of a message sent by an user'),
			array('allow_post_links', 'Enable Links', 'booleanFieldCallback', 'boolean', 'Makes posted links clickable'),
			array('enable_twitter_hashtags', 'Enable Twitter Hashtags', 'booleanFieldCallback', 'boolean', 'Detects Twitter hashtags and converts them to links'),
			array(
				'emoticons_enabled', 'Emoticons Set', 'selectCallback', 'integer',
				'Displays posted emoticons (like :-) or ;-)) as images. You can display a button that allows to insert emoticons. The option is in appearance settings.',
				self::getEmoticonSets()
			),

			array(
				'_section', 'Users Notifications',
				'Configure text notifications or sounds for various events such as new messages, new users in the channel, etc. You can give users an option to mute all sounds. Check appearance settings. '
			),
			array('enable_title_notifications', 'Enable Title Notifications', 'booleanFieldCallback', 'boolean', 'Shows notifications in browser\'s window title when new messages arrives and the browser window is hidden / inactive'),
			array('sound_notification', 'Message  Sound Notification', 'selectCallback', 'string', 'Plays a sound when new messages arrives.', CVPRChatMessagesTab::getNotificationSounds()),
			array('enable_join_notification', 'Enable Join Notification', 'booleanFieldCallback', 'boolean', 'When user joins the channel it displays the following message: User has joined the channel.'),
			array('join_sound_notification', 'Join Sound Notification', 'selectCallback', 'string', 'Plays a sound when user joins the chat.', CVPRChatMessagesTab::getNotificationSounds()),
			array('enable_leave_notification', 'Enable Leave Notification', 'booleanFieldCallback', 'boolean', 'When user leaves the channel it displays the following message: User has left the channel.'),
			array('leave_sound_notification', 'Leave Sound Notification', 'selectCallback', 'string', 'Plays a sound when user leaves the chat.', CVPRChatMessagesTab::getNotificationSounds()),
			array('mentioning_sound_notification', 'Mentioning Sound Notification', 'selectCallback', 'string', 'Plays a sound when user has been mentioned using @UserName notation.', CVPRChatMessagesTab::getNotificationSounds()),

			array('_section', 'Images Settings'),
			array('allow_post_images', 'Enable Images', 'booleanFieldCallback', 'boolean', 'Downloads posted images (links pointing to images) into Media Library and displays them'),
			array('enable_images_uploader', 'Enable Uploader', 'booleanFieldCallback', 'boolean', 'Enables the uploader for sending pictures either from local storage or from a camera (on a mobile device). <br />In order to see sent pictures "Enable Images" option has to be enabled'),
			array('images_size_limit', 'Size Limit', 'stringFieldCallback', 'integer', 'Size limit (in bytes) of images that are posted by users'),
			array('images_width_limit', 'Maximum Width', 'stringFieldCallback', 'integer', 'Resize images to the declared width'),
			array('images_height_limit', 'Maximum Height', 'stringFieldCallback', 'integer', 'Resize images to the declared height'),
			array('images_thumbnail_width_limit', 'Thumbnails Maximum Width', 'stringFieldCallback', 'integer', 'Maximum width of the generated thumbnail'),
			array('images_thumbnail_height_limit', 'Thumbnails Maximum Height', 'stringFieldCallback', 'integer', 'Maximum height of the generated thumbnail'),
			
			array('_section', 'File Attachments Settings'),
			array('enable_attachments_uploader', 'Enable Uploader', 'booleanFieldCallback', 'boolean', 'Enables the uploader for sending file attachments from local storage. You can specify allowed file formats below'),
			array('attachments_file_formats', 'Allowed File Extensions', 'stringFieldCallback', 'string', 'Comma-separated list of allowed extensions'),
			array('attachments_size_limit', 'Size Limit', 'stringFieldCallback', 'integer', 'Size limit (in bytes) of attachments that are posted by users'),
			
			array('_section', 'YouTube Videos Settings'),
			array('enable_youtube', 'Enable YouTube Videos', 'booleanFieldCallback', 'boolean', 'Detects YouTube links and converts them to video players'),
			array('youtube_width', 'Player Width', 'stringFieldCallback', 'integer', 'Width of the video player'),
			array('youtube_height', 'Player Height', 'stringFieldCallback', 'integer', 'Height of the video player')
		);
	}
	
	public function getDefaultValues() {
		return array(
			'enable_title_notifications' => 0,
			'enable_join_notification' => 1,
			'join_sound_notification' => '',
			'enable_leave_notification' => 1,
			'leave_sound_notification' => '',
			'mention_sound_notification' => '',
			'sound_notification' => '',
			'message_max_length' => 400,
			'allow_post_links' => 1,
			'emoticons_enabled' => 1,
			'allow_post_images' => 1,
			'enable_images_uploader' => 1,
			'enable_twitter_hashtags' => 1,
			'enable_attachments_uploader' => 1,
			'attachments_file_formats' => 'pdf,doc,docx',
			'attachments_size_limit' => 3145728,
			
			'images_size_limit' => 3145728,
			'images_width_limit' => 1000,
			'images_height_limit' => 1000,
			'images_thumbnail_width_limit' => 60,
			'images_thumbnail_height_limit' => 60,
			
			'enable_youtube' => 1,
			'youtube_width' => 186,
			'youtube_height' => 105
		);
	}
	
	public function getParentFields() {
		return array(
			'attachments_file_formats' => 'enable_attachments_uploader',
			'attachments_size_limit' => 'enable_attachments_uploader',
			'youtube_width' => 'enable_youtube',
			'youtube_height' => 'enable_youtube',
		);
	}
	
	public static function getNotificationSounds() {
		return array(
			'' => 'Disabled',
			'sound-01' => 'Legacy Sound 1',
			'sound-02' => 'Legacy Sound 2',
			'sound-03' => 'Legacy Sound 3',
			'sound-04' => 'Legacy Sound 4',
			'Cvpr-chat-01' => 'CVPR Chat 1',
			'Cvpr-chat-02' => 'CVPR Chat 2',
			'Cvpr-chat-03' => 'CVPR Chat 3',
			'Cvpr-chat-04' => 'CVPR Chat 4',
			'Cvpr-chat-05' => 'CVPR Chat 5',
			'Cvpr-chat-06' => 'CVPR Chat 6',
			'Cvpr-chat-07' => 'CVPR Chat 7',
			'Cvpr-chat-08' => 'CVPR Chat 8',
			'Cvpr-chat-09' => 'CVPR Chat 9',
			'Cvpr-chat-10' => 'CVPR Chat 10',
			'Cvpr-chat-11' => 'CVPR Chat 11',
			'Cvpr-chat-12' => 'CVPR Chat 12',
			'Cvpr-chat-13' => 'CVPR Chat 13',
			'Cvpr-chat-14' => 'CVPR Chat 14',
			'Cvpr-chat-15' => 'CVPR Chat 15',
			'Cvpr-chat-16' => 'CVPR Chat 16',
			'Cvpr-chat-17' => 'CVPR Chat 17',
			'Cvpr-chat-18' => 'CVPR Chat 18',
			'Cvpr-chat-19' => 'CVPR Chat 19',
			'Cvpr-chat-20' => 'CVPR Chat 20',
		);
	}

	public static function getEmoticonSets() {
		return array(
			0 => '-- No emoticons --',
			1 => 'Basic CVPR Chat',
			2 => 'Animated',
			3 => 'Steel',
			4 => 'Pidgin',
		);
	}
}
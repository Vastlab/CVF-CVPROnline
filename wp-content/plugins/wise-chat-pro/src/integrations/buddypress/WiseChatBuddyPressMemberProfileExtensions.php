<?php

/**
 * Wise Chat BuddyPress member profile extensions.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatBuddyPressMemberProfileExtensions {

	/**
	 * @var WiseChatOptions
	 */
	private $options;

	public function __construct() {
		$this->options = WiseChatOptions::getInstance();

		$this->setUpHeader();
	}

	private function setUpHeader() {
		if ($this->options->isOptionEnabled('bp_member_profile_chat_button', false)) {
			add_action('bp_init', array($this, 'onInitialize'), 100);
		}
	}
	
	public function onInitialize() {
		if (!bp_is_my_profile()) {
			add_action('bp_member_header_actions', array($this, 'displayChatButton'), 100, 1);
		}
	}

	public function displayChatButton() {
		$label = $this->options->getOption('message_bp_chat_message', 'Chat Message');
		$labelNoTags = htmlentities(strip_tags($label));
		?>
			<div class="generic-button">
				<a href="#" class="send-message wise-chat-send-message" title="<?php echo $labelNoTags; ?>" data-user-id="<?php echo bp_displayed_user_id(); ?>"><?php echo $label; ?></a>
			</div>
		<?php
	}
}
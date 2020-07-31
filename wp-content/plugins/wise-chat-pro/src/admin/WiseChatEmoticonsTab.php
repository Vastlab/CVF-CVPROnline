<?php 

/**
 * Wise Chat admin emoticons settings tab class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatEmoticonsTab extends WiseChatAbstractTab {

	public function getFields() {
		return array(
			array('_section', 'General Settings'),
			array(
				'emoticons_enabled', 'Emoticons Theme', 'selectCallback', 'integer',
				'Displays emoticons (like :-) or ;-) etc.) as images. Additionally, you can enable a button that allows to insert emoticons from popup layer. The option is in Appearance settings.<br />
				<strong>Notice:</strong> This setting takes no effect if Custom Emoticons (see below) option is enabled.',
				self::getEmoticonSets()
			),
			array('_section', 'Custom Emoticons', 'Below you can compose and enable your own set of emoticons.'),
			array('custom_emoticons_enabled', 'Enable Custom Emoticons', 'booleanFieldCallback', 'boolean', 'Enable custom set of emoticons. Below you can specify width of the emoticons layer and the list of emoticons.'),
			array('custom_emoticons_popup_width', 'Popup Width', 'stringFieldCallback', 'integer', 'Width of the emoticons popup (<strong>px</strong> unit). If the value is empty a default width is used.'),
			array('custom_emoticons_popup_height', 'Popup Height', 'stringFieldCallback', 'integer', 'Height of the emoticons popup (<strong>px</strong> unit). If the value is empty the height is set to contain all emoticons.'),
			array('custom_emoticons_emoticon_max_width_in_popup', 'Emoticon Width In Popup', 'stringFieldCallback', 'integer', 'Maximum width of a single emoticon in the popup (<strong>px</strong> unit). If the value is empty a default width is used.'),
			array('custom_emoticons_emoticon_width', 'Emoticon Width In Chat', 'selectCallback', 'string', 'Width of a single emoticon in the chat window.', WiseChatEmoticonsTab::getImageSizes()),
			array('custom_emoticon_add', 'New Emoticon', 'emoticonAddCallback', 'void'),
			array('custom_emoticons', 'Emoticons', 'emoticonsCallback', 'void'),
		);
	}
	
	public function getDefaultValues() {
		return array(
			'emoticons_enabled' => 1,
			'custom_emoticons_enabled' => 0,
			'custom_emoticons_popup_width' => '',
			'custom_emoticons_popup_height' => '',
			'custom_emoticons_emoticon_max_width_in_popup' => '',
			'custom_emoticons_emoticon_width' => ''
		);
	}

	public function getParentFields() {
		return array(
			'custom_emoticons_popup_width' => 'custom_emoticons_enabled',
			'custom_emoticons_popup_height' => 'custom_emoticons_enabled',
			'custom_emoticons_emoticon_max_width_in_popup' => 'custom_emoticons_enabled',
			'custom_emoticons_emoticon_width' => 'custom_emoticons_enabled',
		);
	}

	public static function getEmoticonSets() {
		return array(
			0 => '-- No emoticons --',
			1 => 'Basic Wise Chat',
			2 => 'Animated',
			3 => 'Steel',
			4 => 'Pidgin',
		);
	}

	public static function getImageSizes() {
		$defaultNames = array(
			'thumbnail' => __('Thumbnail'),
			'medium' => __('Medium'),
			'medium_large' => __('Medium Large'),
			'large' => __('Large'),
			'full' => __('Full Size')
		);
		$sizes = get_intermediate_image_sizes();

		$sizesOut = array(
			'' => ''
		);
		foreach ($sizes as $size) {
			if (array_key_exists($size, $defaultNames)) {
				$sizesOut[$size] = $defaultNames[$size];
			} else {
				$sizesOut[$size] = $size;
			}
		}

		return $sizesOut;
	}

	public function addEmoticonAction() {
		$newEmoticonAttachmentId = intval($_GET['newEmoticonAttachmentId']);
		$newEmoticonAlias = $_GET['newEmoticonAlias'];

		if ($newEmoticonAttachmentId > 0) {
			WiseChatContainer::load('model/WiseChatEmoticon');
			$emoticon = new WiseChatEmoticon();
			$emoticon->setAttachmentId($newEmoticonAttachmentId);
			$emoticon->setAlias($newEmoticonAlias);
			$this->emoticonsDAO->save($emoticon);

			$this->addMessage("Emoticon has been added");
		} else {
			$this->addErrorMessage('No attachment ID');
		}
	}

	public function deleteCustomEmoticonAction() {
		$id = intval($_GET['id']);

		if (strlen($_GET['id']) > 0) {
			$this->emoticonsDAO->delete($id);
			$this->addMessage("Emoticon has been deleted");
		} else {
			$this->addErrorMessage('No emoticon ID');
		}
	}

	public function moveUpCustomEmoticonAction() {
		$id = intval($_GET['id']);

		if (strlen($_GET['id']) > 0) {
			$this->emoticonsDAO->moveUp($id);
			$this->addMessage("Emoticon has been moved up");
		} else {
			$this->addErrorMessage('No emoticon ID');
		}
	}

	public function moveDownCustomEmoticonAction() {
		$id = intval($_GET['id']);

		if (strlen($_GET['id']) > 0) {
			$this->emoticonsDAO->moveDown($id);
			$this->addMessage("Emoticon has been moved down");
		} else {
			$this->addErrorMessage('No emoticon ID');
		}
	}

	public function emoticonAddCallback() {
		$url = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=addEmoticon");

		printf(
			'<input type="hidden" value="" id="newEmoticonId" name="newEmoticonId" />'.
			'<div id="newEmoticonImageContainerId"></div>'.
			'<button class="wc-image-picker button-secondary" data-parent-field="custom_emoticons_enabled" data-target-id="newEmoticonId" data-image-container-id="newEmoticonImageContainerId">Select Image</button>'.
			'<input type="text" value="" id="newEmoticonAlias" data-parent-field="custom_emoticons_enabled" name="newEmoticonAlias" placeholder="Shortcut" autocomplete="false" />'.
			' | '.
			'<a class="button-primary new-emoticon-submit" href="%s" data-parent-field="custom_emoticons_enabled">Add Emoticon</a>'.
			'<p class="description">Select the image and click Add Emoticon button. Optionally you can choose a shortcut for the emoticon. For example - for smiley you might want to put the shortcut: <strong>:)</strong></p>',
			wp_nonce_url($url)
		);
	}

	public function emoticonsCallback() {
		$emoticons = $this->emoticonsDAO->getAll();

		$html = "<table class='wp-list-table widefat emotstable'>";
		if (count($emoticons) == 0) {
			$html .= '<tr><td>No custom emoticons added yet. Use the form above in order to add you own emoticons.</td></tr>';
		} else {
			$html .= '<thead><tr><th>&nbsp;Image</th><th>Alias</th><th>Actions</th></tr></thead>';
		}

		$total = count($emoticons);
		foreach ($emoticons as $key => $emoticon) {
			$classes = $key % 2 == 0 ? 'alternate' : '';
			$imageUrl = wp_get_attachment_url($emoticon->getAttachmentId());

			$actions = array();
			$deleteURL = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=deleteCustomEmoticon&id=".urlencode($emoticon->getId())).'&tab=emoticons';
			$moveUpURL = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=moveUpCustomEmoticon&id=".urlencode($emoticon->getId())).'&tab=emoticons';
			$moveDownURL = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=moveDownCustomEmoticon&id=".urlencode($emoticon->getId())).'&tab=emoticons';
			if ($key > 0) {
				$actions[] = sprintf(
					'<a class="button-secondary" href="%s" title="Move up" data-parent-field="custom_emoticons_enabled">Up</a>',
					$moveUpURL
				);
			}
			if ($key < ($total - 1)) {
				$actions[] = sprintf(
					'<a class="button-secondary" href="%s" title="Move down" data-parent-field="custom_emoticons_enabled">Down</a>',
					$moveDownURL
				);
			}

			$actions[] = sprintf(
				'<a class="button-primary" href="%s" title="Delete the emoticon" onclick="return confirm(\'Are you sure?\')" data-parent-field="custom_emoticons_enabled">Delete</a>',
				$deleteURL
			);

			$imageTag = '[Image deleted]';
			if ($imageUrl !== false) {
				$imageTag = sprintf(
					'<a href="%s" target="_blank" title="Open in new window" style="outline: none;"><img src="%s" style="max-width: 100px;" alt="Emoticon Image" /></a>',
					$imageUrl, $imageUrl
				);
			}

			$html .= sprintf(
				'<tr class="%s">
					<td>%s</td><td>%s</td><td>%s</td>
				</tr>',
				$classes, $imageTag, htmlentities($emoticon->getAlias(), ENT_QUOTES, 'UTF-8'), implode('&nbsp;|&nbsp;', $actions)
			);
		}
		$html .= "</table>";

		print($html);
	}
}
<?php

/**
 * CVPR Chat admin abstract tab class.
 *
 * @author Kainex <contact@kaine.pl>
 */
abstract class CVPRChatAbstractTab {

	/**
	* @var CVPRChatChannelsDAO
	*/
	protected $channelsDAO;

	/**
	* @var CVPRChatBansDAO
	*/
	protected $bansDAO;

	/**
	 * @var CVPRChatKicksDAO
	 */
	protected $kicksDAO;
	
	/**
	* @var CVPRChatUsersDAO
	*/
	protected $usersDAO;
	
	/**
	* @var CVPRChatMessagesDAO
	*/
	protected $messagesDAO;

	/**
	 * @var CVPRChatActions
	 */
	protected $actions;

	/**
	* @var CVPRChatFiltersDAO
	*/
	protected $filtersDAO;

	/**
	 * @var CVPRChatBansService
	 */
	protected $bansService;

	/**
	 * @var CVPRChatKicksService
	 */
	protected $kicksService;

	/**
	 * @var CVPRChatMessagesService
	 */
	protected $messagesService;
	
	/**
	* @var CVPRChatOptions
	*/
	protected $options;
	
	public function __construct() {
		$this->options = CVPRChatOptions::getInstance();
		$this->channelsDAO = CVPRChatContainer::get('dao/CVPRChatChannelsDAO');
		$this->bansDAO = CVPRChatContainer::get('dao/CVPRChatBansDAO');
		$this->kicksDAO = CVPRChatContainer::get('dao/CVPRChatKicksDAO');
		$this->usersDAO = CVPRChatContainer::get('dao/user/CVPRChatUsersDAO');
		$this->messagesDAO = CVPRChatContainer::get('dao/CVPRChatMessagesDAO');
		$this->filtersDAO = CVPRChatContainer::get('dao/CVPRChatFiltersDAO');
		$this->actions = CVPRChatContainer::getLazy('services/user/CVPRChatActions');
		$this->bansService = CVPRChatContainer::get('services/CVPRChatBansService');
		$this->kicksService = CVPRChatContainer::get('services/CVPRChatKicksService');
		$this->messagesService = CVPRChatContainer::get('services/CVPRChatMessagesService');
	}
	
	/**
	* Shows the message. 
	*
	* @param string $message
	*
	* @return null
	*/
	protected function addMessage($message) {
		$_SESSION[CVPRChatSettings::SESSION_MESSAGE_KEY] = $message;
	}
	
	/**
	* Shows error message. 
	*
	* @param string $message
	*
	* @return null
	*/
	protected function addErrorMessage($message) {
		$_SESSION[CVPRChatSettings::SESSION_MESSAGE_ERROR_KEY] = $message;
	}
	
	/**
	* Returns an array of fields displayed on the tab.
	*
	* @return array
	*/
	public abstract function getFields();
	
	/**
	* Returns an array of default values of fields.
	*
	* @return array
	*/
	public abstract function getDefaultValues();
	
	/**
	* Returns an array of parent fields.
	*
	* @return array
	*/
	public function getParentFields() {
		return array();
	}

	/**
	 * Returns an array of PRO fields.
	 *
	 * @return array
	 */
	public function getProFields() {
		return array();
	}
	
	/**
	* Filters values of fields.
	*
	* @param array $inputValue
	*
	* @return null
	*/
	public function sanitizeOptionValue($inputValue) {
		$newInputValue = array();
		
		foreach ($this->getFields() as $field) {
			$id = $field[0];
			if ($id === CVPRChatSettings::SECTION_FIELD_KEY) {
				continue;
			}
			
			$type = $field[3];
			$value = array_key_exists($id, $inputValue) ? $inputValue[$id] : '';
			
			switch ($type) {
				case 'boolean':
					$newInputValue[$id] = isset($inputValue[$id]) && $value == '1' ? 1 : 0;
					break;
				case 'integer':
					if (isset($inputValue[$id])) {
						if (intval($value).'' != $value) {
							$newInputValue[$id] = '';
						} else {
							$newInputValue[$id] = absint($value);
						}
					}
					break;
				case 'string':
					if (isset($inputValue[$id])) {
						$newInputValue[$id] = sanitize_text_field($value);
					}
					break;
				case 'multilinestring':
					if (isset($inputValue[$id])) {
						$newInputValue[$id] = $value;
					}
					break;
				case 'multivalues':
					if (isset($inputValue[$id]) && is_array($inputValue[$id])) {
						$newInputValue[$id] = $inputValue[$id];
					} else {
						$newInputValue[$id] = array();
					}
					
					break;
			}
		}
		
		return $newInputValue;
	}

	private function printProFeatureNotice() {
		$button = '<a class="button-secondary wcAdminButtonPro" target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro?source=pro-field" title="Check CVPR Chat Pro">
						Check CVPR Chat <strong>Pro</strong>
					</a>';
		printf('<p class="description wcProDescription">%s</p>', 'Notice: This feature is available after upgrading to CVPR Chat Pro. '.$button);
	}

	/**
	* Callback method for displaying plain text field with a hint. If the property is not defined the default value is used.
	*
	* @param array $args Array containing keys: id, name and hint
	*
	* @return null
	*/
	public function stringFieldCallback($args) {
		$id = $args['id'];
		$hint = $args['hint'];
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$parentId = $this->getFieldParent($id);
		$isProFeature = in_array($id, $this->getProFields());
	
		printf(
			'<input type="text" id="%s" name="'.CVPRChatOptions::OPTIONS_NAME.'[%s]" value="%s" %s data-parent-field="%s" />',
			$id, $id,
			$this->options->getEncodedOption($id, $defaultValue),
			$isProFeature || $parentId != null && !$this->options->isOptionEnabled($parentId, false) ? ' disabled="1" ' : '',
			$parentId != null ? $parentId : ''
		);
		if (strlen($hint) > 0) {
			printf('<p class="description">%s</p>', $hint);
		}
		if ($isProFeature) {
			$this->printProFeatureNotice();
		}
	}
	
	/**
	* Callback method for displaying multiline text field with a hint. If the property is not defined the default value is used.
	*
	* @param array $args Array containing keys: id, name and hint
	*
	* @return null
	*/
	public function multilineFieldCallback($args) {
		$id = $args['id'];
		$hint = $args['hint'];
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$parentId = $this->getFieldParent($id);
		$isProFeature = in_array($id, $this->getProFields());
		
		printf(
			'<textarea id="%s" name="'.CVPRChatOptions::OPTIONS_NAME.'[%s]" cols="70" rows="6" %s data-parent-field="%s">%s</textarea>',
			$id, $id,
			$isProFeature || $parentId != null && !$this->options->isOptionEnabled($parentId, false) ? ' disabled="1" ' : '',
			$parentId != null ? $parentId : '',
			$this->options->getEncodedOption($id, $defaultValue)
		);
		if (strlen($hint) > 0) {
			printf('<p class="description">%s</p>', $hint);
		}
		if ($isProFeature) {
			$this->printProFeatureNotice();
		}
	}
	
	/**
	* Callback method for displaying color selection text field with a hint. If the property is not defined the default value is used.
	*
	* @param array $args Array containing keys: id, name and hint
	*
	* @return null
	*/
	public function colorFieldCallback($args) {
		$id = $args['id'];
		$hint = $args['hint'];
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$parentId = $this->getFieldParent($id);
	
		printf(
			'<input type="text" id="%s" name="'.CVPRChatOptions::OPTIONS_NAME.'[%s]" value="%s" %s data-parent-field="%s" class="wc-color-picker" />',
			$id, $id,
			$this->options->getEncodedOption($id, $defaultValue),
			$parentId != null && !$this->options->isOptionEnabled($parentId, false) ? ' disabled="1" ' : '',
			$parentId != null ? $parentId : ''
		);
		if (strlen($hint) > 0) {
			printf('<p class="description">%s</p>', $hint);
		}
	}
	
	/**
	* Callback method for displaying boolean field (checkbox) with a hint. If the property is not defined the default value is used.
	*
	* @param array $args Array containing keys: id, name and hint
	*
	* @return null
	*/
	public function booleanFieldCallback($args) {
		$id = $args['id'];
		$hint = $args['hint'];
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$parentId = $this->getFieldParent($id);
		$isProFeature = in_array($id, $this->getProFields());

		printf(
			'<input type="checkbox" id="%s" name="'.CVPRChatOptions::OPTIONS_NAME.'[%s]" value="1" %s  %s data-parent-field="%s" />',
			$id, $id, 
			$this->options->isOptionEnabled($id, $defaultValue == 1) ? ' checked="1" ' : '',
			$isProFeature || $parentId != null && !$this->options->isOptionEnabled($parentId, false) ? ' disabled="1" ' : '',
			$parentId != null ? $parentId : ''
		);
		if (strlen($hint) > 0) {
			printf('<p class="description">%s</p>', $hint);
		}
		if ($isProFeature) {
			$this->printProFeatureNotice();
		}
	}
	
	/**
	* Callback method for displaying select field with a hint. If the property is not defined the default value is used.
	*
	* @param array $args Array containing keys: id, name, hint, options
	*
	* @return null
	*/
	public function selectCallback($args) {
		$id = $args['id'];
		$hint = $args['hint'];
		$options = $args['options'];
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$value = $this->options->getEncodedOption($id, $defaultValue);
		$parentId = $this->getFieldParent($id);
		$isProFeature = in_array($id, $this->getProFields());

		$optionsHtml = '';
		foreach ($options as $name => $label) {
			$disabled = strpos($name, '_DISABLED_') !== false;
			$optionsHtml .= sprintf("<option value='%s'%s %s>%s</option>", $name, $name == $value ? ' selected="1"' : '', $disabled ? 'disabled' : '', $label);
		}
		
		printf(
			'<select id="%s" name="'.CVPRChatOptions::OPTIONS_NAME.'[%s]" %s data-parent-field="%s">%s</select>',
			$id, $id,
			$isProFeature || $parentId != null && !$this->options->isOptionEnabled($parentId, false) ? ' disabled="1" ' : '',
			$parentId != null ? $parentId : '',
			$optionsHtml
		);
		if (strlen($hint) > 0) {
			printf('<p class="description">%s</p>', $hint);
		}
		if ($isProFeature) {
			$this->printProFeatureNotice();
		}
	}
	
	/**
	* Callback method for displaying list of checkboxes with a hint.
	*
	* @param array $args Array containing keys: id, name, hint, options
	*
	* @return null
	*/
	public function checkboxesCallback($args) {
		$id = $args['id'];
		$hint = $args['hint'];
		$options = $args['options'];
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$values = $this->options->getOption($id, $defaultValue);
		$parentId = $this->getFieldParent($id);
		$isProFeature = in_array($id, $this->getProFields());
		
		$html = '';
		foreach ($options as $key => $value) {
			$html .= sprintf(
				'<label><input type="checkbox" value="%s" name="%s[%s][]" %s %s data-parent-field="%s" />%s</label>&nbsp;&nbsp; ',
				$key, CVPRChatOptions::OPTIONS_NAME, $id, 
				in_array($key, (array) $values) ? 'checked="1"' : '',
				$isProFeature || $parentId != null && !$this->options->isOptionEnabled($parentId, false) ? 'disabled="1"' : '',
				$parentId != null ? $parentId : '',
				$value
			);
		}
		
		printf($html);
		
		if (strlen($hint) > 0) {
			printf('<p class="description">%s</p>', $hint);
		}
		if ($isProFeature) {
			$this->printProFeatureNotice();
		}
	}
	
	/**
	* Callback method for displaying separator.
	*
	* @param array $args Array containing keys: name
	*
	* @return null
	*/
	public function separatorCallback($args) {
		$name = $args['name'];
		
		printf(
			'<p class="description">%s</p>',
			$name
		);
	}
	
	protected function getFieldParent($fieldId) {
		$parents = $this->getParentFields();
		if (is_array($parents) && array_key_exists($fieldId, $parents)) {
			return $parents[$fieldId];
		}
		
		return null;
	}
}
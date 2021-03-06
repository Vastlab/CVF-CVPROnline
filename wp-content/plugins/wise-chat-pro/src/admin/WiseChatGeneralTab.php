<?php 

/**
 * Wise Chat admin general settings tab class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatGeneralTab extends WiseChatAbstractTab {

	public function getFields() {
		return array(
			array('_section', 'General Settings'),
			array(
				'mode', 'Mode', 'selectCallback', 'string',
				'Sets overall chat mode:<br />
				<strong>Classic chat:</strong> displays a classic chat window embedded in a page or a post<br />
                <strong>Facebook-like chat:</strong> displays a chat window and users list attached to the right side of the browser\'s window<br />',
				self::getAllModes()
			),
			array('access_mode', 'Disable Anonymous Users', 'booleanFieldCallback', 'boolean', 'Only regular WP users are allowed to enter the chat. Choose user roles below. '),
			array('access_roles', 'Access For Roles', 'checkboxesCallback', 'multivalues', 'Access only for these user roles', self::getRoles()),
			array('force_user_name_selection', 'Force Username Selection', 'booleanFieldCallback', 'boolean', 'Blocks access to the chat until an user enters his/her name.'),
			array('read_only_for_anonymous', 'Read-only For Anonymous', 'booleanFieldCallback', 'boolean', 'Makes the chat read-only for anonymous users. Only logged in WordPress users are allowed to send messages. You can choose read-only roles below.'),
			array('read_only_for_roles', 'Read-only For Roles', 'checkboxesCallback', 'multivalues', 'Selected roles have read-only access to the chat.', self::getRoles()),
            array('collect_user_stats', 'Collect User Statistics', 'booleanFieldCallback', 'boolean', 'Collects various statistics of users, including country, city, etc.'),
			array(
				'enable_buddypress', 'Enable BuddyPress', 'booleanFieldCallback', 'boolean',
				'Enables BuddyPress integration features.<br />'.
				'<strong>Notice:</strong> Please remember to enable User Groups support in BuddyPress settings otherwise the integration will not work.'
			),
			array('user_actions', 'Actions', 'adminActionsCallback', 'void'),
			array('_section', 'Chat Opening Hours and Days', 'Server UTC date and time is taken into account. It is currently: '.date('Y-m-d H:i:s')),
			array('enable_opening_control', 'Enable Opening Control', 'booleanFieldCallback', 'boolean', 'Allows to specify when the chat is available for users.'),
			array('opening_days', 'Opening Days', 'checkboxesCallback', 'multivalues', 'Select chat opening days.', self::getOpeningDaysValues()),
			array('opening_hours', 'Opening Hours', 'openingHoursCallback', 'multivalues', 'Specify chat opening hours (HH:MM format)'),
		);
	}
	
	public function getDefaultValues() {
		return array(
			'mode' => 0,
			'access_mode' => 0,
			'access_roles' => array('administrator'),
			'force_user_name_selection' => 0,
            'read_only_for_anonymous' => 0,
			'user_actions' => null,
            'collect_user_stats' => 1,
			'enable_buddypress' => 1,
			'enable_opening_control' => 0,
			'opening_days' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
			'opening_hours' => array('opening' => '8:00', 'openingMode' => 'AM', 'closing' => '4:00', 'closingMode' => 'PM'),
			'read_only_for_roles' => array()
		);
	}
	
	public function getParentFields() {
		return array(
			'opening_days' => 'enable_opening_control',
			'opening_hours' => 'enable_opening_control',
			'read_only_for_roles' => 'read_only_for_anonymous',
			'access_roles' => 'access_mode',
		);
	}
	
	public function resetAnonymousCounterAction() {
		$this->options->resetUserNameSuffix();
		$this->addMessage('The prefix has been reset.');
	}

	public function resetSettingsAction() {
		$this->options->dropAllOptions();
		$this->addMessage('All settings were reset to defaults.');
	}
	
	public function openingHoursCallback($args) {
		$id = 'opening_hours';
		$hint = $args['hint'];
		
		$defaults = $this->getDefaultValues();
		$defaultValue = array_key_exists($id, $defaults) ? $defaults[$id] : '';
		$values = $this->options->getOption($id, $defaultValue);
		$parentId = $this->getFieldParent($id);
		$disabledAttribute = $parentId != null && !$this->options->isOptionEnabled($parentId, false) ? 'disabled="1"' : '';
		
		$modes = array('AM', 'PM', '24h');
		$openingModesSelect = sprintf(
			'<select name="%s[%s][openingMode]" %s data-parent-field="%s">', 
			WiseChatOptions::OPTIONS_NAME, $id,
			$disabledAttribute, $parentId != null ? $parentId : ''
		);
		$closingModesSelect = sprintf(
			'<select name="%s[%s][closingMode]" %s data-parent-field="%s">', 
			WiseChatOptions::OPTIONS_NAME, $id,
			$disabledAttribute, $parentId != null ? $parentId : ''
		);
		foreach ($modes as $mode) {
			$openingModesSelect .= sprintf(
				'<option value="%s" %s>%s</option>', 
				$mode, array_key_exists('openingMode', $values) && $values['openingMode'] == $mode ? 'selected="1"' : '', $mode
			);
			$closingModesSelect .= sprintf(
				'<option value="%s" %s>%s</option>', 
				$mode, array_key_exists('closingMode', $values) && $values['closingMode'] == $mode ? 'selected="1"' : '', $mode
			);
		}
		$openingModesSelect .= '</select>';
		$closingModesSelect .= '</select>';
		
		print(
			sprintf(
				'From: <input type="text" value="%s" placeholder="HH:MM" id="openingHour" name="%s[%s][opening]" pattern="\d{1,2}:\d{2}"
						%s data-parent-field="%s" style="max-width: 90px;" />'.$openingModesSelect,
				array_key_exists('opening', $values) ? $values['opening'] : '',
				WiseChatOptions::OPTIONS_NAME, $id,
				$disabledAttribute,
				$parentId != null ? $parentId : ''
			).
			sprintf(
				'&nbsp;&nbsp; To: <input type="text" value="%s" placeholder="HH:MM" id="closingHour" name="%s[%s][closing]" pattern="\d{1,2}:\d{2}"
						%s data-parent-field="%s" style="max-width: 90px;" />'.$closingModesSelect,
				array_key_exists('closing', $values) ? $values['closing'] : '',
				WiseChatOptions::OPTIONS_NAME, $id,
				$disabledAttribute,
				$parentId != null ? $parentId : ''
			).
			sprintf('<p class="description">%s</p>', $hint)
		);
	}
	
	public static function getOpeningDaysValues() {
		return array(
			'Monday' => 'Monday', 
			'Tuesday' => 'Tuesday', 
			'Wednesday' => 'Wednesday', 
			'Thursday' => 'Thursday', 
			'Friday' => 'Friday', 
			'Saturday' => 'Saturday',
			'Sunday' => 'Sunday'
		);
	}

	public function getRoles() {
		$editableRoles = array_reverse(get_editable_roles());
		$rolesOptions = array();

		foreach ($editableRoles as $role => $details) {
			$name = translate_user_role($details['name']);
			$rolesOptions[esc_attr($role)] = $name;
		}

		return $rolesOptions;
	}
	
	public function adminActionsCallback() {
		$url = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=resetAnonymousCounter");
		
		printf(
			'<a class="button-secondary" href="%s" title="Resets username prefix" onclick="return confirm(\'Are you sure you want to reset the prefix?\')">Reset Username Prefix</a><p class="description">Resets prefix number used to generate username for anonymous users.</p>',
			wp_nonce_url($url)
		);

		$url = admin_url("options-general.php?page=".WiseChatSettings::MENU_SLUG."&wc_action=resetSettings");
		printf(
			'<a class="button-secondary" href="%s" title="Resets Wise Chat Pro settings" onclick="return confirm(\'WARNING: All settings will be permanently deleted. \\n\\nAre you sure you want to reset the settings?\')">Reset All Settings</a><p class="description">Resets all settings to default values.</p>',
			wp_nonce_url($url)
		);
	}

	public static function getAllModes() {
		return array(
			0 => 'Classic chat',
			1 => 'Facebook-like chat',
		);
	}
}
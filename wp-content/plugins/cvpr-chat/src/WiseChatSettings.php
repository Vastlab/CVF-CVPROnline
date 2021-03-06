<?php

/**
 * CVPRChat admin settings page.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatSettings {
	const OPTIONS_GROUP = 'Cvpr_chat_options_group';
	const MENU_SLUG = 'Cvpr-chat-admin';
	
	const PAGE_TITLE = 'Settings Admin';
	const MENU_TITLE = 'CVPR Chat Settings';
	const SESSION_MESSAGE_KEY = 'wc_plugin_data_messages_update';
	const SESSION_MESSAGE_ERROR_KEY = 'wc_plugin_data_messages_error';
	
	const SECTION_FIELD_KEY = '_section';
	
	/**
	* @var array Tabs definition
	*/
	private $tabs = array(
		'Cvpr-chat-general' => 'General', 
		'Cvpr-chat-externalLogin' => 'External Login',
		'Cvpr-chat-messages' => 'Messages Posting',
		'Cvpr-chat-moderation' => 'Moderation',
		'Cvpr-chat-appearance' => 'Appearance',
		'Cvpr-chat-emoticons' => 'Emoticons',
		'Cvpr-chat-channels' => 'Channels',
		'Cvpr-chat-filters' => 'Filters',
		'Cvpr-chat-bans' => 'Bans',
		'Cvpr-chat-kicks' => 'Kicks',
		'Cvpr-chat-localization' => 'Localization',
		'Cvpr-chat-advanced' => 'Advanced',
		'Cvpr-chat-pro' => 'PRO Options',
	);
	
	/**
	* @var array Generated sections
	*/
	private $sections = array();
	
	public function __construct() {
		CVPRChatContainer::load('admin/CVPRChatAbstractTab');
	}
	
	/**
	* Initializes settings page link in admin menu.
	*
	* @return null
	*/
	public function initialize() {
		add_action('admin_menu', array($this, 'addAdminMenu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
		add_action('admin_init', array($this, 'pageInit'));
	}
	
	public function addAdminMenu() {
		add_options_page(self::PAGE_TITLE, self::MENU_TITLE, 'manage_options', self::MENU_SLUG, array($this, 'renderAdminPage'));
		$this->handleActions();
	}
	
	public function enqueueScripts() {
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker-script', plugins_url('../js/Cvpr_chat_admin.js', __FILE__), array('wp-color-picker'), false, true);
	}
	
	public function pageInit() {
		register_setting(self::OPTIONS_GROUP, CVPRChatOptions::OPTIONS_NAME, array($this, 'getSanitizedFormValues'));

		foreach ($this->tabs as $key => $caption) {
			$sectionKey = "section_{$key}";
			$tabObject = $this->getTabObject($key);
			
			$fields = $tabObject->getFields();
			foreach ($fields as $field) {
				$id = $field[0];
				$name = $field[1];
				
				if ($id === self::SECTION_FIELD_KEY) {
					$sectionKey = "section_{$key}_".md5($name);
					add_settings_section($sectionKey, $name, null, $key);
					$this->sections[$key][] = array(
						'id' => $sectionKey,
						'name' => $name,
						'hint' => array_key_exists(2, $field) ? $field[2] : ''
					);
				} else {
					$args = array(
						'id' => $id,
						'name' => $name,
						'hint' => array_key_exists(4, $field) ? $field[4] : '',
						'options' => array_key_exists(5, $field) ? $field[5] : array()
					);
				
					add_settings_field($id, $name, array($tabObject, $field[2]), $key, $sectionKey, $args);
				}
			}
		}
	}

	/**
	* Sets the default values of all configuration fields.
	* It should be used right after the activation of the plugin.
	*
	* @return null
	*/
	public function setDefaultSettings() {
		$options = get_option(CVPRChatOptions::OPTIONS_NAME, array());
		
		foreach ($this->tabs as $key => $caption) {
			$tabObject = $this->getTabObject($key);
			foreach ($tabObject->getDefaultValues() as $key => $value) {
				if (!array_key_exists($key, $options)) {
					$options[$key] = $value;
				}
			}
		}
		update_option(CVPRChatOptions::OPTIONS_NAME, $options);
	}

	public function renderAdminPage() {
		$options = CVPRChatOptions::getInstance();

		?>
			<div class="wrap">
				<style type="text/css">
					.wcAdminFl { float: left; }
					.wcAdminFr { float: right; }
					.wcAdminCb { clear:both; }
					.wcAdminMenu, .wcAdminMenu * { -moz-box-sizing: border-box; box-sizing: border-box; }
					.wcAdminTabContainer { overflow:hidden; }
					.wcAdminMenu *:focus { outline: none; box-shadow: none; }
					.wcAdminMenu { width: 200px; margin-right: 10px; border: 1px solid #e5e5e5; -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.04); box-shadow: 0 1px 1px rgba(0,0,0,.04); }
					.wcAdminMenu ul { margin: 0px; list-style: none; padding: 0px; }
					.wcAdminMenu ul li { border-bottom: 1px solid #dfdfdf; margin: 0; display: list-item; text-align: -webkit-match-parent; }
					.wcAdminMenu ul li a { background-color: #fff; display: inline-block; padding: 10px 20px; width: 100%; font-size: 1.1em; text-decoration: none; color: #000; }
					.wcAdminMenu ul li a:hover { background-color: #fafafa; color: #000; outline: 0;}
					.wcAdminMenu ul li a:visited { color: #000; }
					.wcAdminMenu ul li a.wcAdminMenuActive { font-weight: bold; background-color: #fafafa; }
					.wcAdminMenu ul li a#Cvpr-chat-pro {
						background: #4F3B5E url("<?php echo $options->getBaseDir(); ?>/gfx/icons/small-pro-icon.png") no-repeat 17px center;
						color: #fff; padding-left: 48px;
					}
					.wcAdminDonation span { padding-top: 5px; display: inline-block; font-size: 1.1em; }
					.wcAdminDonation a.wcAdminButton { border-color: #11f; color: #005; font-size: 1.1em; }
					.wp-admin a.wcAdminButtonPro, .wp-admin a.wcAdminButtonPro:hover, .wp-admin a.wcAdminButtonPro:focus, #Cvpr-chat-proContainer a.wcAdminButtonPro, #Cvpr-chat-proContainer a.wcAdminButtonPro:hover {
						background: #4f3b5e url("<?php echo $options->getBaseDir(); ?>/gfx/icons/small-pro-icon.png") no-repeat 10px top;
						border: 1px solid #4f3b5e; color: #fff; font-size: 1.2em; padding-left: 61px; padding-right: 35px;
					}
					.wp-admin p.wcProDescription {
						color: #f00;
					}
					.wp-admin p.description a.wcAdminButtonPro {
						vertical-align: middle; padding-right: 6px; padding-left: 40px; font-style: normal;
					}
					#Cvpr-chat-proContainer a.wcAdminButtonPro, #Cvpr-chat-proContainer a.wcAdminButtonPro:hover {
						padding-top: 10px; padding-bottom: 10px; height: 50px; background-position: 10px center;
						padding-left: 60px; padding-right: 40px;
					}

					.wp-admin a.wcAdminButtonPro:hover, #Cvpr-chat-proContainer a.wcAdminButtonPro:hover { background-color: #533f62; border: 1px solid #533f62; color: #fff; }

					.wp-admin a.wcAdminButtonProDemo, .wp-admin a.wcAdminButtonProDemo:hover, .wp-admin a.wcAdminButtonProDemo:focus, #Cvpr-chat-proContainer a.wcAdminButtonProDemo, #Cvpr-chat-proContainer a.wcAdminButtonProDemo:hover {
						padding-left: 31px; background: #0085ba;
						border: 1px solid #0085ba;
					}
				</style>
			
				<h2><?php echo self::MENU_TITLE ?></h2>
				<div class="wcAdminDonation">
					<a class="button-secondary wcAdminButtonPro" target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro?source=settings-page" title="Check CVPR Chat Pro">
						CVPR Chat <strong>Pro</strong>
					</a>
					<a class="button-secondary" target="_blank" href="https://kaine.pl/" title="Kainex software">Visit Us</a>
					<a class="button-secondary" target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat/Cvpr-chat-feedback" title="Send quick feedback">Send Feedback</a>
				</div>
				
				<form method="post" action="options.php" class="metabox-holder">
					<?php settings_fields(self::OPTIONS_GROUP); ?>
					
					<?php $this->renderMenu(); ?>
					
					<?php
						$isFirstContainer = true;
						foreach ($this->tabs as $pageId => $tabCaption) {
							$hideContainer = $isFirstContainer ? '' : 'display:none';
							echo "<div id='{$pageId}Container' class='wcAdminTabContainer' style='{$hideContainer}'>";
							
							$sections = $this->sections[$pageId];
							foreach ($sections as $section) {
								echo "<div class='postbox'>";
								echo "<h3 class='hndle'><span>".$section['name']."</span></h3>";
								echo "<div class='inside'>";
								echo '<table class="form-table">';
								if (strlen($section['hint']) > 0) {
									echo '<tr><td colspan="2" style="padding:0px"><p class="description">'.$section['hint'].'</p></td></tr>';
								}
								do_settings_fields($pageId, $section['id']);
								echo '<tr><td colspan="2">';
								submit_button('', 'primary large', 'submit', false, array('onclick' => 'Cvpr_chat_append_tab(\''.str_replace('Cvpr-chat-', '', $pageId).'\')'));
								echo '</td></tr>';
								echo '</table>';
								echo "</div></div>";
							}
							
							echo "</div>";
							$isFirstContainer = false;
						}
					?>
					
					<br class="wcAdminCb" />
				</form>
				
				<script type="text/javascript">
					function Cvpr_chat_append_tab(tab) {
						var referrer = jQuery('input[name = "_wp_http_referer"]');
						referrer.val(referrer.val() + '#tab=' + tab);
					}

					jQuery(window).load(function() {
						jQuery('.wcAdminMenu a').click(function() {
							jQuery('.wcAdminTabContainer').hide();
							jQuery('#' + jQuery(this).attr('id') + 'Container').show();
							jQuery('.wcAdminMenu a').removeClass('wcAdminMenuActive');
							jQuery(this).addClass('wcAdminMenuActive');
						});

						if (location && location.hash && location.hash.length > 0) {
							var matches = location.hash.match(new RegExp('tab=([^&]*)'));
							if (matches) {
								var tab = matches[1];
								jQuery('.wcAdminTabContainer').hide();
								jQuery('#Cvpr-chat-' + tab + 'Container').show();
								jQuery('.wcAdminMenu a').removeClass('wcAdminMenuActive');
								jQuery('#Cvpr-chat-' + tab).addClass('wcAdminMenuActive');
							}
						}
					});
				</script>
			</div>
		<?php
	}
	
	private function renderMenu() {
		$outHtml = '';
		
		$outHtml .= '<div class="wcAdminMenu wcAdminFl">';
		$outHtml .= '<ul>';
		$isFirstTab = true;
		foreach ($this->tabs as $key => $caption) {
			$isActive = $isFirstTab ? 'wcAdminMenuActive' : '';
			$outHtml .= '<li><a id="'.$key.'" class="'.$isActive.'" href="javascript://">'.$caption.'</a></li>';
			$isFirstTab = false;
		}
		$outHtml .= '</ul>';
		$outHtml .= '</div>';
		
		echo $outHtml;
	}
	
	/**
	* Detects actions passed in parameters and delegates to an action method.
	*
	* @return null
	*/
	public function handleActions() {
		global $wpdb;
		
		if (isset($_GET['wc_action'])) {
			foreach ($this->tabs as $tabKey => $tabCaption) {
				$tabObject = $this->getTabObject($tabKey);
				$actionMethod = $_GET['wc_action'].'Action';
				if (method_exists($tabObject, $actionMethod)) {
					$tabObject->$actionMethod();
				}
			}

			$redirURL = admin_url("options-general.php?page=".self::MENU_SLUG).(isset($_GET['tab']) ? '#wc_tab='.urlencode($_GET['tab']) : '');
			echo '<script type="text/javascript">location.replace("' . $redirURL . '");</script>';
		} else {
			$this->showUpdatedMessage();
			$this->showErrorMessage();
		}
	}
	
	/**
	* Filters form input using filters from each tab object.
	*
	* @param array $input A key-value list of form values
	*
	* @return array Filtered array
	*/
	public function getSanitizedFormValues($input) {
		$sanitized = array();
		foreach ($this->tabs as $tabKey => $tabCaption) {
			$sanitized = array_merge($sanitized, $this->getTabObject($tabKey)->sanitizeOptionValue($input));
		}
		$sanitized = array_merge(get_option(CVPRChatOptions::OPTIONS_NAME, array()), $sanitized);
		
		return $sanitized;
	}
	
	
	/**
	* Returns an instance of the requested tab object.
	*
	* @param string $tabKey A key from $this->tabs array
	*
	* @return CVPRChatAbstractTab
	*/
	private function getTabObject($tabKey) {
		$tabKey = ucfirst(str_replace('Cvpr-chat-', '', $tabKey));
		$classPathAndName = "admin/CVPRChat{$tabKey}Tab";
		
		return CVPRChatContainer::get($classPathAndName);
	}
	
	/**
	* Shows a message stored in session.
	*
	* @return null
	*/
	private function showUpdatedMessage() {
		if (isset($_SESSION[self::SESSION_MESSAGE_KEY])) {
			add_settings_error(md5($_SESSION[self::SESSION_MESSAGE_KEY]), esc_attr('settings_updated'), $_SESSION[self::SESSION_MESSAGE_KEY], 'updated');
			unset($_SESSION[self::SESSION_MESSAGE_KEY]);
		}
	}
	
	/**
	* Shows a message stored in session.
	*
	* @return null
	*/
	private function showErrorMessage() {
		if (isset($_SESSION[self::SESSION_MESSAGE_ERROR_KEY])) {
			add_settings_error(md5($_SESSION[self::SESSION_MESSAGE_ERROR_KEY]), esc_attr('settings_updated'), $_SESSION[self::SESSION_MESSAGE_ERROR_KEY], 'error');
			unset($_SESSION[self::SESSION_MESSAGE_ERROR_KEY]);
		}
	}

	/**
	 * Shows the documentation of all shortcode attributes.
	 */
	private function showDocs() {
		$excludedFields = array(
			'mode', 'anonymous_login_enabled', 'facebook_login_enabled', 'facebook_login_app_id', 'facebook_login_app_secret',
			'twitter_login_enabled', 'twitter_login_api_key', 'twitter_login_api_secret', 'google_login_enabled', 'google_login_client_id', 'google_login_client_secret', 'permission_approve_message_role',
			'user_actions', 'enable_opening_control', 'opening_days', 'opening_hours',
			'enable_approval_confirmation', 'new_messages_hidden', 'show_hidden_messages_roles', 'no_hidden_messages_roles', 'approving_messages_mode', 'show_avatars', 'enable_edit_own_messages',
			'bans', 'ban_add', 'channels', 'admin_actions', 'filters', 'filter_add',
			'enable_private_messages', 'show_users_list_avatars', 'show_users_list_info_windows', 'users_list_info_windows_template', 'fb_users_list_top_offset', 'fb_bottom_offset',
			'fb_bottom_offset_threshold', 'fb_show_users_list_title', 'fb_minimize_users_list_option', 'fb_minimize_on_start', 'fb_disable_channel', 'bp_member_profile_chat_button', 'custom_emoticons_enabled',
			'custom_emoticons_popup_width', 'custom_emoticons_popup_height', 'custom_emoticons_emoticon_max_width_in_popup', 'custom_emoticons_emoticon_width', 'custom_emoticon_add', 
			'custom_emoticons', 'kicks', 'kick_add',
		);
		foreach ($this->tabs as $key => $caption) {
			$tabObject = $this->getTabObject($key);
			$fields = $tabObject->getFields();
			$defaults = $tabObject->getDefaultValues();
			$printSection = null;
			foreach ($fields as $field) {
				$id = $field[0];
				$name = str_replace('<br />', ' ', $field[1]);
				$callback = $field[2];
				$type = $field[3];
				$hint = $field[4];
				$values = $field[5];

				if (in_array($id, $excludedFields)) {
					continue;
				}

				if ($id == '_section') {
					$printSection = "<h4>{$caption}: $name</h4>";
					continue;
				} else {
					if ($printSection !== null) {
						echo $printSection;
						$printSection = null;
					}
					$default = $defaults[$id];
					$defaultLabel = ($default !== '' && $default !== null ? $default : '[not set]');

					$allowedValue = 'a text';
					if ($callback == 'booleanFieldCallback') {
						$allowedValue = '<ul><li><i>0</i> - disabled</li><li><i>1</i> - enabled</li></ul>';
					} else if ($type == 'integer') {
						$allowedValue = '<i>a positive number</i>';
					} else if ($callback == 'colorFieldCallback') {
						$allowedValue = '<i>a color in hex format, eg. #ef2244</i>';
					} else if (is_array($values)) {
						$allowedValues = array();
						foreach ($values as $key => $value) {
							if ($key === '') {
								$key = '[not set]';
							}
							if ($id == 'permission_delete_message_role' || $id == 'permission_ban_user_role') {
								$value = ucfirst($key);
							}
							$allowedValues[] = "<li><i>$key</i> - {$value}</li>";
						}
						$allowedValue = '<ul>'.implode('', $allowedValues).'</ul>';
					}

					if ($id == 'chat_width' || $id == 'chat_height') {
						$allowedValue = null;
					}

					echo "<h5>$id - $name</h5>\n";
					echo "$hint\n";
					if ($allowedValue !== null) {
						echo "Allowed values: {$allowedValue}\n";
					}
					echo "Default: <i>".$defaultLabel."</i>\n\n";
				}
			}
		}

		die();
	}
}
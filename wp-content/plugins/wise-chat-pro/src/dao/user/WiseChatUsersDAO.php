<?php

/**
 * WiseChat and WordPress users DAO.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatUsersDAO {
	/**
	* @var WiseChatOptions
	*/
	private $options;
	
	public function __construct() {
		WiseChatContainer::load('model/WiseChatUser');
		$this->options = WiseChatOptions::getInstance();
	}

	/**
	 * Returns user by ID.
	 *
	 * @param integer $id
	 *
	 * @return WiseChatUser|null
	 */
	public function get($id) {
		global $wpdb;

		$table = WiseChatInstaller::getUsersTable();
		$sql = sprintf('SELECT * FROM %s WHERE id = %d;', $table, $id);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			return $this->populateUserData($results[0]);
		}

		return null;
	}

	/**
	 * Returns the latest (according the ID field) user by specified name.
	 *
	 * @param string $name
	 *
	 * @return WiseChatUser|null
	 */
	public function getLatestByName($name) {
		global $wpdb;

		$table = WiseChatInstaller::getUsersTable();
		$sql = sprintf("SELECT max(id) AS id FROM %s WHERE name = '%s';", $table, addslashes($name));
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $this->get($result->id);
		}

		return null;
	}

	/**
	 * Returns the latest (according the ID field) user by specified WordPress user ID.
	 *
	 * @param integer $wpUserId
	 *
	 * @return WiseChatUser|null
	 */
	public function getLatestByWordPressId($wpUserId) {
		global $wpdb;

		$table = WiseChatInstaller::getUsersTable();
		$sql = sprintf("SELECT max(id) AS id FROM %s WHERE wp_id = '%d';", $table, $wpUserId);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];

			return $this->get($result->id);
		}

		return null;
	}

	/**
	 * Returns the latest user by external login details.
	 *
	 * @param string $externalType
	 * @param string $externalId
	 * @return null|WiseChatUser
	 */
	public function getByExternalTypeAndId($externalType, $externalId) {
		global $wpdb;

		$table = WiseChatInstaller::getUsersTable();
		$sql = sprintf("SELECT max(id) AS id FROM %s WHERE external_type = '%s' AND external_id = '%s';", $table, addslashes($externalType), addslashes($externalId));
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $this->get($result->id);
		}

		return null;
	}

	/**
	 * Returns users by IDs.
	 *
	 * @param array $ids Array of IDs
	 *
	 * @return WiseChatUser[]
	 */
	public function getAll($ids) {
		global $wpdb;

		if (!is_array($ids) || count($ids) == 0) {
			return array();
		}
        $idsFiltered = array();
        foreach ($ids as $id) {
            if ($id > 0) {
                $idsFiltered[] = $id;
            }
        }

		if (count($idsFiltered) === 0) {
			return array();
		}

		$users = array();
		$table = WiseChatInstaller::getUsersTable();
		$sql = sprintf('SELECT * FROM %s WHERE id IN (%s);', $table, implode(',', $idsFiltered));
		$results = $wpdb->get_results($sql);
		if (is_array($results)) {
			foreach ($results as $result) {
				$users[] = $this->populateUserData($result);
			}
		}

		return $users;
	}

	/**
	 * Creates or updates the user and returns it.
	 *
	 * @param WiseChatUser $user
	 *
	 * @return WiseChatUser
	 * @throws Exception On validation error
	 */
	public function save($user) {
		global $wpdb;

		// low-level validation:
		if ($user->getName() === null) {
			throw new Exception('Name of the user cannot equal null');
		}
		if ($user->getSessionId() === null) {
			throw new Exception('Session ID of the user cannot equal null');
		}

		// prepare user data:
		$table = WiseChatInstaller::getUsersTable();
		$columns = array(
			'name' => $user->getName(),
			'session_id' => $user->getSessionId(),
			'external_type' => $user->getExternalType(),
			'external_id' => $user->getExternalId(),
			'avatar_url' => $user->getAvatarUrl(),
			'profile_url' => $user->getProfileUrl(),
			'data' => json_encode($user->getData()),
			'ip' => $user->getIp()
		);

		// update or insert:
		if ($user->getId() !== null) {
			$columns['wp_id'] = $user->getWordPressId();
			$wpdb->update($table, $columns, array('id' => $user->getId()), '%s', '%d');
		} else {
			if ($user->getWordPressId() > 0) {
				$columns['wp_id'] = $user->getWordPressId();
			}
			$columns['created'] = time();
			$wpdb->insert($table, $columns);
			$user->setId($wpdb->insert_id);
		}

		return $user;
	}

	/**
	 * Updates name by specified WordPress user ID.
	 *
	 * @param string $name
	 * @param integer $wpUserId
	 *
	 * @return null
	 */
	public function updateNameByWordPressId($name, $wpUserId) {
		global $wpdb;

		$table = WiseChatInstaller::getUsersTable();
		$wpdb->update($table, array('name' => $name), array('wp_id' => $wpUserId), '%s', '%d');
	}

	/**
	 * Creates chat user based on given WordPress user ID.
	 *
	 * @param $wordPressUserId
	 * @return null|WiseChatUser
	 * @throws Exception
	 */
	public function createOrGetBasedOnWordPressUserId($wordPressUserId) {
		$chatUser = $this->getLatestByWordPressId($wordPressUserId);
		if ($chatUser !== null) {
			return $chatUser;
		}

		$wordPressUser = $this->getWpUserByID($wordPressUserId);
		if ($wordPressUser !== null) {
			$chatUser = new WiseChatUser();
			$chatUser->setName($wordPressUser->display_name);
			$chatUser->setWordPressId($wordPressUser->ID);
			$chatUser->setSessionId('');

			return $this->save($chatUser);
		}

		return null;
	}

	/**
	 * Converts raw object into WiseChatUser object.
	 *
	 * @param stdClass $rawUserData
	 *
	 * @return WiseChatUser
	 */
	private function populateUserData($rawUserData) {
		$user = new WiseChatUser();
		if (strlen($rawUserData->id) > 0) {
			$user->setId(intval($rawUserData->id));
		}
        if (strlen($rawUserData->wp_id) > 0) {
            $user->setWordPressId(intval($rawUserData->wp_id));
        }
		$user->setName($rawUserData->name);
		$user->setSessionId($rawUserData->session_id);
		$user->setExternalType($rawUserData->external_type);
		$user->setExternalId($rawUserData->external_id);
		$user->setAvatarUrl($rawUserData->avatar_url);
		$user->setProfileUrl($rawUserData->profile_url);
		$user->setIp($rawUserData->ip);
		$user->setData(json_decode($rawUserData->data, true));

		return $user;
	}

	/**
	* Detects whether a WordPress admin is logged in.
	*
	* @return boolean
	*/
	public function isWpUserAdminLogged() {
		return current_user_can('manage_options');
	}
	
	/**
	* Determines whether current user has given right.
	*
	* @param string $rightName
	*
	* @return boolean
	*/
	public function hasCurrentWpUserRight($rightName) {
		$wpUser = $this->getCurrentWpUser();
		
		if ($wpUser !== null) {
			$targetRoles = (array) $this->options->getOption("permission_{$rightName}_role", 'administrator');
			if ((is_array($wpUser->roles) && count(array_intersect($targetRoles, $wpUser->roles)) > 0) || current_user_can("wise_chat_{$rightName}")) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Determines whether current BuddyPress user has given right.
	 *
	 * @param string $rightName
	 * @param integer $groupId
	 *
	 * @return boolean
	 */
	public function hasCurrentBpUserRight($rightName, $groupId) {
		$wpUser = $this->getCurrentWpUser();

		if ($wpUser !== null && $groupId > 0) {
			if ($this->options->isOptionEnabled('enable_buddypress', false) && function_exists("is_buddypress")) {
				if ($rightName == 'delete_message') {
					if (groups_is_user_mod($wpUser->ID, $groupId) && groups_get_groupmeta($groupId, 'bp_wisechat_permissions_mod_delete_messages')) {
						return true;
					}

					if (groups_is_user_admin($wpUser->ID, $groupId) && groups_get_groupmeta($groupId, 'bp_wisechat_permissions_admin_delete_messages')) {
						return true;
					}
				}
				if ($rightName == 'edit_message') {
					if (groups_is_user_mod($wpUser->ID, $groupId) && groups_get_groupmeta($groupId, 'bp_wisechat_permissions_mod_edit_messages')) {
						return true;
					}

					if (groups_is_user_admin($wpUser->ID, $groupId) && groups_get_groupmeta($groupId, 'bp_wisechat_permissions_admin_edit_messages')) {
						return true;
					}
				}
				if ($rightName == 'ban_user') {
					if (groups_is_user_mod($wpUser->ID, $groupId) && groups_get_groupmeta($groupId, 'bp_wisechat_permissions_mod_ban_users')) {
						return true;
					}
					if (groups_is_user_admin($wpUser->ID, $groupId) && groups_get_groupmeta($groupId, 'bp_wisechat_permissions_admin_ban_users')) {
						return true;
					}
				}
			}
		}

		return false;
	}
	
	/**
	* Checks if WordPress user is logged in.
	*
	* @return boolean
	*/
	public function isWpUserLogged() {
		if (is_user_logged_in()) {
			return true;
		}
		
		return false;
	}
	
	/**
	* Returns WordPress user by its "display_name" field.
	* All results are cached in static field for later use.
	*
	* @param string $displayName
	*
	* @return WP_User|null
	*/
	public function getWpUserByDisplayName($displayName) {
		global $wpdb;
		static $cache = array();
		
		if (array_key_exists($displayName, $cache)) {
			return $cache[$displayName];
		}

		$userRow = $wpdb->get_row($wpdb->prepare(
			"SELECT `ID` FROM {$wpdb->users} WHERE `display_name` = %s", $displayName
		));
		if ($userRow === null) {
			$cache[$displayName] = null;
		} else {
			$args = array(
				'search' => $userRow->ID,
				'search_columns' => array('ID')
			);
			$users = new WP_User_Query($args);
			if (count($users->results) > 0) {
				$cache[$displayName] = $users->results[0];
			} else {
				$cache[$displayName] = null;
			}
		}
		
		return $cache[$displayName];
	}
	
	/**
	* Returns WordPress user by its ID field.
	* All results are cached in static field for later use.
	*
	* @param integer $id
	*
	* @return WP_User|null
	*/
	public function getWpUserByID($id) {
		static $cache = array();
		
		if (array_key_exists($id, $cache)) {
			return $cache[$id];
		}
		
		$user = get_user_by('id', $id);
		$cache[$id] = $user !== false ? $user : null;
		
		return $cache[$id];
	}
	
	/**
	* Returns WordPress user by its "user_login" field.
	* All results are cached in static field.
	*
	* @param string $userLogin
	*
	* @return WP_User|null
	*/
	public function getWpUserByLogin($userLogin) {
		static $cache = array();
		
		if (array_key_exists($userLogin, $cache)) {
			return $cache[$userLogin];
		}
		
		$args = array(
			'search' => $userLogin,
			'search_columns' => array('user_login')
		);
		$users = new WP_User_Query($args);
		if (count($users->results) > 0) {
			$cache[$userLogin] = $users->results[0];
		} else {
			$cache[$userLogin] = null;
		}

		return $cache[$userLogin];
	}
	
	/**
	* Returns current WordPress user or null if nobody is logged in.
	*
	* @return WP_User|null
	*/
	public function getCurrentWpUser() {
		if (is_user_logged_in()) {
			return wp_get_current_user();
		}
		
		return null;
	}
}
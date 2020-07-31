<?php

/**
 * CVPR Chat channel-user associations DAO
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatChannelUsersDAO {

	/**
	 * @var CVPRChatUsersDAO
	 */
	private $usersDAO;

	/**
	 * @var CVPRChatChannelsDAO
	 */
	protected $channelsDAO;

	/**
	* @var CVPRChatOptions
	*/
	private $options;
	
	public function __construct() {
		CVPRChatContainer::load('model/CVPRChatChannelUser');
		$this->options = CVPRChatOptions::getInstance();
		$this->usersDAO = CVPRChatContainer::get('dao/user/CVPRChatUsersDAO');
		$this->channelsDAO = CVPRChatContainer::get('dao/CVPRChatChannelsDAO');
	}

	/**
	 * Creates or updates channel-to-user association and returns it.
	 *
	 * @param CVPRChatChannelUser $channelUser
	 *
	 * @return CVPRChatChannelUser
	 * @throws Exception On validation error
	 */
	public function save($channelUser) {
		global $wpdb;

		// low-level validation:
		if ($channelUser->getChannelId() === null) {
			throw new Exception('Channel ID is required');
		}
		if ($channelUser->getUserId() === null) {
			throw new Exception('User ID is required');
		}

		// prepare channel-user data:
		$table = CVPRChatInstaller::getChannelUsersTable();
		$columns = array(
			'user_id' => $channelUser->getUserId(),
			'channel_id' => $channelUser->getChannelId(),
			'active' => $channelUser->isActive() === true ? '1' : '0',
			'last_activity_time' => $channelUser->getLastActivityTime()
		);

		// update or insert:
		if ($channelUser->getId() !== null) {
			$wpdb->update($table, $columns, array('id' => $channelUser->getId()), '%s', '%d');
		} else {
			$wpdb->insert($table, $columns);
			$channelUser->setId($wpdb->insert_id);
		}

		return $channelUser;
	}

	/**
	 * Returns channel-user by ID.
	 *
	 * @param integer $userId
	 * @param integer $channelId
	 *
	 * @return CVPRChatChannelUser|null
	 */
	public function getByUserIdAndChannelId($userId, $channelId) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$sql = sprintf(
			'SELECT * FROM %s WHERE user_id = %d AND channel_id = %d;', $table, intval($userId), intval($channelId)
		);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			return $this->populateData($results[0]);
		}

		return null;
	}

	/**
	 * Returns active channel-user by ID.
	 *
	 * @param integer $userId
	 * @param integer $channelId
	 *
	 * @return CVPRChatChannelUser|null
	 */
	public function getActiveByUserIdAndChannelId($userId, $channelId) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$sql = sprintf(
			'SELECT * FROM %s WHERE active = "1" AND user_id = %d AND channel_id = %d;', $table, intval($userId), intval($channelId)
		);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			return $this->populateData($results[0]);
		}

		return null;
	}

	/**
	 * Returns all active CVPRChatChannelUser objects by channel name.
	 *
	 * @param integer $channelId
	 *
	 * @return CVPRChatChannelUser[]
	 */
	public function getAllActiveByChannelId($channelId) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$usersTable = CVPRChatInstaller::getUsersTable();
		$sql = sprintf(
			'SELECT cu.* FROM %s AS cu '.
			'LEFT JOIN %s AS u ON (u.id = cu.user_id) '.
			'WHERE cu.active = 1 AND cu.channel_id = "%d" '.
			'ORDER BY u.name ASC '.
			'LIMIT 1000;',
			$table, $usersTable, intval($channelId)
		);
		$channelUsersRaw = $wpdb->get_results($sql);

		/** @var CVPRChatChannelUser[][] $channelUsersToComplete */
		$channelUsersToComplete = array();
		$channelUsers = array();
		foreach ($channelUsersRaw as $channelUserRaw) {
			$channelUser = $this->populateData($channelUserRaw);
			$channelUsers[] = $channelUser;
			$channelUsersToComplete[$channelUser->getUserId()][] = $channelUser;
		}

		// load related users:
		$users = $this->usersDAO->getAll(array_keys($channelUsersToComplete));
		foreach ($users as $user) {
			if (array_key_exists($user->getId(), $channelUsersToComplete)) {
				foreach ($channelUsersToComplete[$user->getId()] as $channelUser) {
					$channelUser->setUser($user);
				}
			}
		}

		return $channelUsers;
	}

	/**
	 * Updates active status of all associations older than given amount of seconds.
	 *
	 * @param boolean $active
	 * @param integer $time
	 *
	 * @return null
	 */
	public function updateActiveForOlderByLastActivityTime($active, $time) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$threshold = time() - $time;

		$wpdb->get_results(
			sprintf("UPDATE %s SET active = %d WHERE last_activity_time < %d;", $table, $active === true ? 1 : 0, $threshold)
		);
	}

	/**
	 * Deletes all associations older than given amount of seconds.
	 *
	 * @param integer $time
	 *
	 * @return null
	 */
	public function deleteOlderByLastActivityTime($time) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$threshold = time() - $time;

		$wpdb->get_results(
			sprintf("DELETE FROM %s WHERE last_activity_time < %s;", $table, $threshold)
		);
	}

	/**
	 * Removes the user from all channels.
	 *
	 * @param CVPRChatUser $user
	 */
	public function deleteAllByUser($user) {
		global $wpdb;
		if ($user === null) {
			return;
		}

		$table = CVPRChatInstaller::getChannelUsersTable();
		$wpdb->get_results(sprintf("DELETE FROM %s WHERE user_id = %d;", $table, intval($user->getId())));
	}

	/**
	 * Converts stdClass object into CVPRChatChannelUser object.
	 *
	 * @param stdClass $channelUserRaw
	 *
	 * @return CVPRChatChannelUser
	 */
	private function populateData($channelUserRaw) {
		$channelUser = new CVPRChatChannelUser();
		if ($channelUserRaw->id > 0) {
			$channelUser->setId(intval($channelUserRaw->id));
		}
		if ($channelUserRaw->user_id > 0) {
			$channelUser->setUserId(intval($channelUserRaw->user_id));
		}
		if ($channelUserRaw->channel_id > 0) {
			$channelUser->setChannelId(intval($channelUserRaw->channel_id));
		}
		$channelUser->setActive($channelUserRaw->active == '1');
		if ($channelUserRaw->last_activity_time > 0) {
			$channelUser->setLastActivityTime(intval($channelUserRaw->last_activity_time));
		}

		return $channelUser;
	}

	/**
	 * Returns the amount of active users of the given channel.
	 *
	 * @param integer $channelId ID of the Channel
	 *
	 * @return integer
	 */
	public function getAmountOfUsersInChannel($channelId) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$sql = sprintf(
			'SELECT count(DISTINCT user_id) AS quantity FROM %s WHERE active = 1 AND channel_id = "%d";',
			$table, intval($channelId)
		);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $result->quantity;
		}

		return 0;
	}

	/**
	 * Returns the amount of logged-in users of the given channel.
	 *
	 * @param integer $channelId ID of the Channel
	 *
	 * @return integer
	 */
	public function getAmountOfLoggedInUsersInChannel($channelId) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelUsersTable();
		$usersTable = CVPRChatInstaller::getUsersTable();
		$sql = sprintf(
			'SELECT count(DISTINCT ch_us.user_id) AS quantity
				FROM %s AS ch_us
				LEFT JOIN %s AS us ON (us.id = ch_us.user_id)
				WHERE ch_us.active = 1 AND ch_us.channel_id = "%d" AND us.wp_id > 0;',
			$table, $usersTable, intval($channelId)
		);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $result->quantity;
		}

		return 0;
	}

	/**
	 * Returns statistics of channels containing active users.
	 *
	 * @return CVPRChatChannelStats[]
	 */
	public function getAllChannelsStats() {
		global $wpdb;

		CVPRChatContainer::load('model/CVPRChatChannelStats');

		$table = CVPRChatInstaller::getChannelUsersTable();
		$sql = sprintf(
			'SELECT channel_id, count(DISTINCT user_id) AS users '.
			'FROM %s '.
			'WHERE active = 1 '.
			'GROUP BY channel_id '.
			'ORDER BY channel_id ASC '.
			'LIMIT 1000;', $table
		);

		$rawChannelStatsData = $wpdb->get_results($sql);
		if (is_array($rawChannelStatsData)) {
			$channelStatsArray = array();
			foreach ($rawChannelStatsData as $rawChannelStats) {
				$channelStats = new CVPRChatChannelStats();
				$channelStats->setChannelId($rawChannelStats->channel_id);
				$channelStats->setNumberOfUsers($rawChannelStats->users);
				$channelStats->setChannel($this->channelsDAO->get($rawChannelStats->channel_id));
				$channelStatsArray[] = $channelStats;
			}

			return $channelStatsArray;
		}

		return array();
	}

	/**
	 * Returns the amount of active channel-user associations for given session ID.
	 *
	 * @param string $sessionId
	 *
	 * @return integer
	 */
	public function getAmountOfActiveBySessionId($sessionId) {
		global $wpdb;

		$sessionId = addslashes($sessionId);
		$table = CVPRChatInstaller::getChannelUsersTable();
		$usersTable = CVPRChatInstaller::getUsersTable();
		$sql = sprintf(
			'SELECT count(usc.user_id) AS quantity '.
			'FROM %s AS usc '.
			'LEFT JOIN %s AS us ON (usc.user_id = us.id) '.
			'WHERE usc.active = 1 AND us.session_id = "%s";',
			$table, $usersTable, addslashes($sessionId)
		);

		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			$result = $results[0];
			return $result->quantity;
		}

		return 0;
	}

	/**
	* Checks whether the given user name belongs to a different user (with different session ID) either active or inactive.
	*
	* @param string $userName Username to check
	* @param string $sessionId Session ID to check
	* @param boolean $includeActiveOnly
	*
	* @return boolean
	*/
	public function isUserNameOccupied($userName, $sessionId, $includeActiveOnly = false) {
		global $wpdb;

		$userName = addslashes($userName);
		$sessionId = addslashes($sessionId);
		$table = CVPRChatInstaller::getChannelUsersTable();
		$usersTable = CVPRChatInstaller::getUsersTable();
		$activeOnlyCondition = $includeActiveOnly ? ' AND usc.active = 1 ' : '';
		$sql = sprintf(
			'SELECT * '.
			'FROM %s AS usc '.
			'LEFT JOIN %s AS us ON (usc.user_id = us.id) '.
			'WHERE us.name = "%s" AND us.session_id != "%s" %s LIMIT 1;',
			$table, $usersTable, $userName, $sessionId, $activeOnlyCondition
		);
		$results = $wpdb->get_results($sql);
		
		return is_array($results) && count($results) > 0 ? true : false;
	}
	
}
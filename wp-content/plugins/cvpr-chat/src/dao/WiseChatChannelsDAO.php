<?php

/**
 * CVPR Chat channels DAO
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatChannelsDAO {
	
	/**
	* @var CVPRChatOptions
	*/
	private $options;
	
	public function __construct() {
		CVPRChatContainer::load('model/CVPRChatChannel');
		$this->options = CVPRChatOptions::getInstance();
	}

	/**
	 * Creates or updates the channel and returns it.
	 *
	 * @param CVPRChatChannel $channel
	 *
	 * @return CVPRChatChannel
	 * @throws Exception On validation error
	 */
	public function save($channel) {
		global $wpdb;

		// low-level validation:
		if ($channel->getName() === null) {
			throw new Exception('Name of the channel cannot equal null');
		}

		// prepare channel data:
		$table = CVPRChatInstaller::getChannelsTable();
		$columns = array(
			'name' => $channel->getName(),
			'password' => $channel->getPassword()
		);

		// update or insert:
		if ($channel->getId() !== null) {
			$wpdb->update($table, $columns, array('id' => $channel->getId()), '%s', '%d');
		} else {
			$wpdb->insert($table, $columns);
			$channel->setId($wpdb->insert_id);
		}

		return $channel;
	}

	/**
	 * Returns channel by ID.
	 *
	 * @param integer $id
	 *
	 * @return CVPRChatChannel|null
	 */
	public function get($id) {
		global $wpdb;

		$table = CVPRChatInstaller::getChannelsTable();
		$sql = sprintf('SELECT * FROM %s WHERE id = %d;', $table, intval($id));
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			return $this->populateChannelData($results[0]);
		}

		return null;
	}

	/**
	 * Returns all channels sorted by name.
	 *
	 * @return CVPRChatChannel[]
	 */
	public function getAll() {
		global $wpdb;

		$channels = array();
		$table = CVPRChatInstaller::getChannelsTable();
		$sql = sprintf('SELECT * FROM %s ORDER BY name ASC;', $table);
		$results = $wpdb->get_results($sql);
		if (is_array($results)) {
			foreach ($results as $result) {
				$channels[] = $this->populateChannelData($result);
			}
		}

		return $channels;
	}

	/**
	 * Returns channel by name.
	 *
	 * @param string $name
	 *
	 * @return CVPRChatChannel|null
	 */
	public function getByName($name) {
		global $wpdb;

		$name = addslashes($name);
		$table = CVPRChatInstaller::getChannelsTable();
		$sql = sprintf('SELECT * FROM %s WHERE name = "%s";', $table, $name);
		$results = $wpdb->get_results($sql);
		if (is_array($results) && count($results) > 0) {
			return $this->populateChannelData($results[0]);
		}

		return null;
	}

    /**
     * Deletes the channel by ID.
     *
     * @param integer $id
     *
     * @return null
     */
    public function deleteById($id) {
        global $wpdb;

        $id = intval($id);
        $table = CVPRChatInstaller::getChannelsTable();
        $wpdb->get_results(sprintf("DELETE FROM %s WHERE id = '%d';", $table, $id));
    }

	/**
	 * Converts raw object into CVPRChatChannel object.
	 *
	 * @param stdClass $rawChannelData
	 *
	 * @return CVPRChatChannel
	 */
	private function populateChannelData($rawChannelData) {
		$channel = new CVPRChatChannel();
		if ($rawChannelData->id > 0) {
			$channel->setId(intval($rawChannelData->id));
		}
		$channel->setName($rawChannelData->name);
		$channel->setPassword($rawChannelData->password);

		return $channel;
	}
}
<?php

/**
 * Wise Chat private messages rules DAO
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatPrivateMessagesRulesDAO {
	/**
	* @var WiseChatOptions
	*/
	private $options;
	
	public function __construct() {
		WiseChatContainer::load('model/WiseChatPrivateMessagesRule');
		$this->options = WiseChatOptions::getInstance();
	}

	/**
	 * Creates or updates the object.
	 *
	 * @param WiseChatPrivateMessagesRule $rule
	 *
	 * @return WiseChatPrivateMessagesRule
	 * @throws Exception On validation error
	 */
	public function save($rule) {
		// low-level validation:
		if ($rule->getSource() === null) {
			throw new Exception('Source cannot equal null');
		}
		if ($rule->getTarget() === null) {
			throw new Exception('Target cannot equal null');
		}

		// prepare data:
		$columns = array(
			'source' => $rule->getSource(),
			'target' => $rule->getTarget()
		);

		// update or insert:
		$rules = (array) $this->options->getOption('private_messages_rules', array());
		if ($rule->getId() !== null) {
			foreach ($rules as $key => $ruleEntry) {
				if ($ruleEntry['id'] == $rule->getId()) {
					$rules[$key] = array_merge($ruleEntry, $columns);
					break;
				}
			}
		} else {
			$lastId = $this->options->getIntegerOption("private_messages_rules_last_id", 0);
			$lastId++;
			$columns['id'] = $lastId;
			$rules[] = $columns;
			$rule->setId($lastId);
			$this->options->setOption('private_messages_rules_last_id', $lastId);
		}

		$this->options->setOption('private_messages_rules', array_values($rules));
		$this->options->saveOptions();

		return $rule;
	}

	/**
	 * Returns a rule by ID.
	 *
	 * @param integer $id
	 *
	 * @return WiseChatPrivateMessagesRule|null
	 */
	public function get($id) {
		$rules = (array) $this->options->getOption('private_messages_rules', array());

		foreach ($rules as $key => $ruleEntry) {
			if ($ruleEntry['id'] == $id) {
				return $this->populateData($ruleEntry);
			}
		}

		return null;
	}

	/**
	 * Returns all rules.
	 *
	 * @return WiseChatPrivateMessagesRule[]
	 */
	public function getAll() {
		$rules = (array) $this->options->getOption('private_messages_rules', array());

		$result = array();
		foreach ($rules as $rule) {
			$result[] = $this->populateData($rule);
		}

		return $result;
	}

	/**
	 * Deletes rule by its ID.
	 *
	 * @param integer $id
	 *
	 * @return null
	 */
	public function delete($id) {
		$index = $this->getRuleIndexById($id);
		if ($index !== null) {
			$rules = (array) $this->options->getOption('private_messages_rules', array());

			unset($rules[$index]);

			$this->options->setOption('private_messages_rules', array_values($rules));
			$this->options->saveOptions();
		}
	}

	/**
	 * @param integer $id
	 *
	 * @return int|null
	 */
	private function getRuleIndexById($id) {
		$rules = (array) $this->options->getOption('private_messages_rules', array());

		foreach ($rules as $index => $rule) {
			if ($rule['id'] == $id) {
				return $index;
			}
		}

		return null;
	}

	/**
	 * Converts a raw object into WiseChatPrivateMessagesRule object.
	 *
	 * @param stdClass $rawData
	 *
	 * @return WiseChatPrivateMessagesRule
	 */
	private function populateData($rawData) {
		$rule = new WiseChatPrivateMessagesRule();
		$rule->setId($rawData['id']);
		$rule->setSource($rawData['source']);
		$rule->setTarget($rawData['target']);

		return $rule;
	}
}
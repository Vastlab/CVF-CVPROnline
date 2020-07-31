<?php

class WiseChatPrivateMessagesRulesService {

	/**
	 * @var WiseChatPrivateMessagesRulesDAO
	 */
	protected $privateMessagesRulesDAO;

	public function __construct() {
		$this->privateMessagesRulesDAO = WiseChatContainer::get('dao/WiseChatPrivateMessagesRulesDAO');
	}

	/**
	 * @param WiseChatUser $sender
	 * @param WiseChatUser $receiver
	 * @return boolean
	 */
	public function isMessageDeliveryAllowed($sender, $receiver) {
		$rules = $this->privateMessagesRulesDAO->getAll();
		if (count($rules) === 0) {
			return true;
		}

		if ($sender === null || $receiver === null) {
			return false;
		}

		if ($sender->getId() === $receiver->getId()) {
			return true;
		}

		$senderRoles = $this->getRoles($sender);
		$receiverRoles = $this->getRoles($receiver);

		foreach ($rules as $rule) {
			if (in_array($rule->getSource(), $senderRoles) && in_array($rule->getTarget(), $receiverRoles)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param WiseChatUser $user
	 * @return array
	 */
	private function getRoles($user) {
		$roles = array();

		if (in_array($user->getExternalType(), array('fb', 'go', 'tw'))) {
			$roles = array('_'.$user->getExternalType());
		} else if ($user->getWordPressId() > 0) {
			$wpUser = get_userdata($user->getWordPressId());
			$roles = $wpUser !== false && is_array($wpUser->roles) ? $wpUser->roles : array();
		} else {
			$roles = array('_anonymous');
		}

		$roles[] = '_any';

		return $roles;
	}
}
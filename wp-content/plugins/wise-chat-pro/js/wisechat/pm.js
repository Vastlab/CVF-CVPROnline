/**
 * Wise Chat Private Messages namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.pm = {};

/**
 * Manages the list of ignored users. The list is stored in Local Storage.
 *
 * @param {String} channelId
 * @constructor
 */
wisechat.pm.IgnoreList = function(channelId) {
	var LOCAL_STORAGE_IGNORE_LIST_KEY = "WiseChatIgnoreList";
	var fallbackStorage = {};

	/**
	 * Adds user to the ignore list.
	 *
	 * @param {String} hash ID of the user
	 */
	function addUser(hash) {
		var data = getData();
		data[hash] = true;
		saveData(data);
	}

	/**
	 * Removes user from the ignore list.
	 *
	 * @param {String} hash ID of the user
	 */
	function removeUser(hash) {
		var data = getData();
		delete data[hash];
		saveData(data);
	}

	/**
	 * Checks if the user is in the ignore list.
	 *
	 * @param {String} hash ID of the user
	 */
	function isIgnored(hash) {
		var data = getData();

		return typeof data[hash] !== "undefined";
	}

	/**
	 * Returns the ignore list for current channel.
	 *
	 * @returns {Object}
	 */
	function getData() {
		var ignoredList = null;
		if (typeof(Storage) !== "undefined") {
			var data = window.localStorage.getItem(LOCAL_STORAGE_IGNORE_LIST_KEY);
			if (data != null) {
				try {
					var parsedData = JSON.parse(data);
					if (jQuery.isPlainObject(parsedData)) {
						ignoredList = parsedData;
					}
				} catch (e) { }
			}
		}

		if (ignoredList === null) {
			ignoredList = fallbackStorage;
		}

		return typeof ignoredList[channelId] !== 'undefined' && jQuery.isPlainObject(ignoredList[channelId]) ? ignoredList[channelId] : {};
	}

	/**
	 * Saves the ignore list for current channel.
	 *
	 * @param {Object} data
	 */
	function saveData(data) {
		if (typeof(Storage) !== "undefined") {
			var currentData = window.localStorage.getItem(LOCAL_STORAGE_IGNORE_LIST_KEY);
			if (currentData != null) {
				try {
					currentData = JSON.parse(currentData);
				} catch (e) { }
			}

			if (!jQuery.isPlainObject(currentData)) {
				currentData = {};
			}

			if (typeof currentData[channelId] === 'undefined') {
				currentData[channelId] = {};
			}

			currentData[channelId] = data;

			if (jQuery.isEmptyObject(data)) {
				delete currentData[channelId];
			}

			window.localStorage.setItem(LOCAL_STORAGE_IGNORE_LIST_KEY, JSON.stringify(currentData));
		} else {
			if (typeof fallbackStorage[channelId] === 'undefined') {
				fallbackStorage[channelId] = {};
			}

			fallbackStorage[channelId] = data;

			if (jQuery.isEmptyObject(data)) {
				delete fallbackStorage[channelId];
			}
		}
	}

	// public API:
	this.addUser = addUser;
	this.removeUser = removeUser;
	this.isIgnored = isIgnored;
};

/**
 * Manages the list of invitations.
 *
 * @constructor
 */
wisechat.pm.InvitationList = function() {
	var inProgressInvitations = {};

	/**
	 * Checks if the user is in the invitation process.
	 *
	 * @param {String} hash ID of the user
	 */
	function isInvitationInProgress(hash) {
		return typeof inProgressInvitations[hash] !== "undefined";
	}

	/**
	 * Starts invitation for the user.
	 *
	 * @param {String} hash ID of the user
	 */
	function startInvitation(hash) {
		inProgressInvitations[hash] = hash;
	}

	/**
	 * Ends invitation for the user.
	 *
	 * @param {String} hash ID of the user
	 */
	function endInvitation(hash) {
		delete inProgressInvitations[hash];
	}

	// public API:
	this.isInvitationInProgress = isInvitationInProgress;
	this.startInvitation = startInvitation;
	this.endInvitation = endInvitation;
};

/**
 * Represents a single conversation.
 *
 * @param {String} publicId User public ID
 * @param {String} hash User ID
 * @param {String} name User name
 * @param {wisechat.ui.Window} window Messages window
 * @constructor
 */
wisechat.pm.Conversation = function(publicId, hash, name, window) {
	var invitationEnabled = true;
	var publicIdSaved = publicId;

	// public api:
	this.getPublicId = function() {
		return publicIdSaved;
	};
	this.setPublicId = function(publicId) {
		publicIdSaved = publicId;
	};
	this.getHash = function() {
		return hash;
	};
	this.getName = function() {
		return name;
	};
	this.getWindow = function() {
		return window;
	};
	this.setInvitationEnabled = function(enabled) {
		invitationEnabled = enabled;
	};
	this.isInvitationEnabled = function() {
		return invitationEnabled;
	};
};

/**
 * Manages the list of saved conversations.
 *
 * @param {String} channelId
 * @constructor
 */
wisechat.pm.SavedConversations = function(channelId) {
	var $this = jQuery(this);
	var LOCAL_STORAGE_KEY = "WiseChatOpenConversations";

	/**
	 * Marks conversation as minimized.
	 *
	 * @param {String} hash
	 */
	function markMinimized(hash) {
		var data = getData();

		if (typeof data[channelId] === 'undefined') {
			data[channelId] = {};
		}
		data[channelId]['__m__' + hash] = true;

		saveData(data);
	}

	/**
	 * Check if the conversation is minimized.
	 *
	 * @param {String} hash User ID
	 * @return {Boolean}
	 */
	function isMinimized(hash) {
		var data = getData();

		return typeof data[channelId] !== 'undefined' && typeof data[channelId]['__m__' + hash] !== 'undefined';
	}

	/**
	 * Clears minimize state of the conversation.
	 *
	 * @param {String} hash User ID
	 */
	function unmarkMinimized(hash) {
		var data = getData();

		if (typeof data[channelId] !== 'undefined' && typeof data[channelId]['__m__' + hash] !== 'undefined') {
			delete data[channelId]['__m__' + hash];
			if (jQuery.isEmptyObject(data[channelId])) {
				delete data[channelId];
			}
			saveData(data);
		}
	}

	/**
	 * Marks conversation as active.
	 *
	 * @param {String} hash
	 */
	function markActive(hash) {
		var data = getData();
		data['__active__'] = hash;

		saveData(data);
		$this.trigger('markedActive', [hash]);
	}

	/**
	 * Returns active conversation.
	 *
	 * @return {String}
	 */
	function getActive() {
		var data = getData();

		if (typeof data['__active__'] !== 'undefined') {
			return data['__active__'];
		}

		return null;
	}

	/**
	 * Marks conversation as open.
	 *
	 * @param {String} hash User ID
	 */
	function markOpen(hash) {
		var data = getData();

		if (typeof data[channelId] === 'undefined') {
			data[channelId] = {};
		}
		data[channelId][hash] = true;

		saveData(data);
	}

	/**
	 * Removes the conversation from the list.
	 *
	 * @param {String} hash User ID
	 */
	function clear(hash) {
		var data = getData();

		if (typeof data[channelId] !== 'undefined' && typeof data[channelId][hash] !== 'undefined') {
			delete data[channelId][hash];
			delete data[channelId]['__m__' + hash];
			if (jQuery.isEmptyObject(data[channelId])) {
				delete data[channelId];
			}
			saveData(data);
		}
	}

	/**
	 * Check if the conversation is on the list.
	 *
	 * @param {String} hash User ID
	 * @return {Boolean}
	 */
	function isOpen(hash) {
		var data = getData();

		return typeof data[channelId] !== 'undefined' && typeof data[channelId][hash] !== 'undefined';
	}

	function getData() {
		var data = {};
		if (typeof(Storage) !== "undefined") {
			var encodedData = window.localStorage.getItem(LOCAL_STORAGE_KEY);
			if (encodedData != null) {
				try {
					data = JSON.parse(encodedData);
				} catch (e) { }
			}
		}

		return data;
	}

	function saveData(data) {
		if (typeof(Storage) !== "undefined") {
			window.localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(data));
		}
	}

	// public API:
	this.$ = $this;
	this.markOpen = markOpen;
	this.clear = clear;
	this.isOpen = isOpen;
	this.markActive = markActive;
	this.getActive = getActive;
	this.markMinimized = markMinimized;
	this.isMinimized = isMinimized;
	this.unmarkMinimized = unmarkMinimized;
};

// instances of wisechat.pm.SavedConversations class:
wisechat.pm.SavedConversations.instances = {};

/**
 * Returns single instance of SavedConversations class for given channel ID.
 *
 * @param {String} channelId
 * @returns {wisechat.pm.SavedConversations}
 * @static
 */
wisechat.pm.SavedConversations.getInstance = function(channelId) {
	if (typeof(wisechat.pm.SavedConversations.instances[channelId]) === 'undefined') {
		wisechat.pm.SavedConversations.instances[channelId] = new wisechat.pm.SavedConversations(channelId);
	}

	return wisechat.pm.SavedConversations.instances[channelId];
};

/**
 * Private messages support. It initializes wisechat.ui.Window objects that represents each individual private conversation.
 * Additionally it manages ignoring list.
 *
 * @param {Object} options Plugin's global options
 * @param {wisechat.ui.UsersList} usersList
 * @param {wisechat.pm.RecentChats} recentChats
 * @param {wisechat.core.MessagesTransceiver} transceiver
 * @param {wisechat.maintenance.MaintenanceExecutor} maintenance
 * @param {wisechat.settings.Settings} settings
 * @param {wisechat.utils.Notifier} notifier
 * @param {wisechat.ui.VisualLogger} logger
 * @param {jQuery} controlsTemplate
 * @param {jQuery} dialogsContainer
 * @constructor
 */
wisechat.pm.PrivateMessages = function(
	options, usersList, recentChats, transceiver, maintenance, settings, notifier, logger, controlsTemplate, dialogsContainer
) {
	var $this = jQuery(this);
	var ignoreList = new wisechat.pm.IgnoreList(options.channelId);
	var invitationList = new wisechat.pm.InvitationList();
	var conversations = [];
	var usersOriginalHashMap = {};

	/**
	 * Opens a conversation when an user is clicked on the users list.
	 *
	 * @param {Event} event
	 * @param {String} publicId
	 * @param {String} hash
	 * @param {String} name
	 * @param {Boolean} isCurrent
	 * @param {Boolean} isAllowed
	 */
	function onUserListUserClick(event, publicId, hash, name, isCurrent, isAllowed) {
		if (isCurrent) {
			return;
		}

		if (!isAllowed) {
			var alertDialog = new wisechat.ui.Dialog('alert', dialogsContainer, {
				title: options.messages.messageInformation,
				text: options.messages.messageError14,
				buttons: [
					{
						label: options.messages.messageOk,
						onClickClose: true
					}
				]
			});
			alertDialog.show();
			return;
		}

		var conversation = getConversationByHash(hash);
		if (conversation == null) {
			conversation = createConversation(publicId, hash, name, true);
		}

		// if the user is in the ignore list then display confirmation dialog:
		if (ignoreList.isIgnored(hash)) {
			var confirmDialog = new wisechat.ui.Dialog('confirm', dialogsContainer, {
				title: options.messages.messageInformation,
				text: options.messages.messageInfo1,
				buttons: [
					{
						label: options.messages.messageNo,
						onClickClose: true
					},
					{
						label: options.messages.messageYes,
						onClick: function () {
							$this.trigger('open', [conversation]);
							ignoreList.removeUser(hash);
							confirmDialog.close();
						}
					}
				]
			});
			confirmDialog.show();
		} else {
			$this.trigger('open', [conversation]);
		}
	}

	function onRecentChatClicked(event, recentChat) {
		var conversation = getConversationByHash(recentChat.hash);
		if (conversation !== null) {
			$this.trigger('open', [conversation]);
		}
	}

	/**
	 * Detects new private messages. Supports restoring private messages (during chat initialization).
	 *
	 * @param {Event} event
	 * @param {Object} response
	 */
	function onMessagesArrived(event, response) {
		if (typeof options.userData === 'undefined') {
			return;
		}

		var restoreMode = typeof response.restorePrivateConversations !== 'undefined' && response.restorePrivateConversations === true;
		for (var x = 0; x < response.result.length; x++) {
			var message = response.result[x];
			if (typeof message.isPrivate === 'undefined' || !message.isPrivate) {
				continue;
			}

			if (restoreMode) {
				restoreConversation(message, response.nowTime);
			} else {
				processMessage(message, response.nowTime);
			}
		}

		// restored conversations can be handled by UI:
		if (restoreMode) {
			$this.trigger('restore', [conversations]);
		}
	}

	/**
	 * Updates publicId and hash if new user mapping is received. The event is generated after a message is sent to non-existing chat user.
	 *
	 * @param {Event} event
	 * @param {Object} mapping
	 */
	function onUserMappingArrived(event, mapping) {
		var conversation = getConversationByHash(mapping.hash);
		if (conversation !== null) {
			// update publicId in the window:
			conversation.getWindow().setMessageCustomParameters({
				privateMessage: mapping.map.publicId
			});
			conversation.setPublicId(mapping.map.publicId);
		}
		usersOriginalHashMap[mapping.map.hash] = mapping.hash;

		$this.trigger('userHashChange', [mapping.hash, mapping.map.hash]);
	}

	function getOriginalHash(hash) {
		if (typeof usersOriginalHashMap[hash] !== 'undefined') {
			return usersOriginalHashMap[hash];
		}

		return hash;
	}

	/**
	 * Restores conversation based on the message.
	 *
	 * @param {Object} message
	 * @param {String} time
	 * @returns {null}
	 */
	function restoreConversation(message, time) {
		var userHash = options.userData.hash;
		var senderHash = message.senderHash;
		var recipientHash = message.recipientHash;
		var targetId = null;
		var targetHash = null;
		var targetName = null;
		var isAllowed = true;

		// sent by me:
		if (userHash == senderHash) {
			targetId = message.recipientId;
			targetHash = message.recipientHash;
			targetName = message.recipientName;
			isAllowed = message.allowedSenderToRecipient;
		}

		// sent by someone else:
		if (userHash == recipientHash) {
			if (ignoreList.isIgnored(senderHash)) {
				return null;
			}
			targetId = message.senderId;
			targetHash = message.senderHash;
			targetName = message.senderName;
			isAllowed = message.allowedRecipientToSender;
		}

		// create and open a new conversation if it does not exist yet:
		if (targetId !== null) {
			var conversation = getConversationByHash(targetHash);
			if (conversation === null) {
				conversation = createConversation(targetId, targetHash, targetName, isAllowed);
				conversation.setInvitationEnabled(false);
			}

			// ask for disabling notifications for this message:
			message.noNotifications = true;

			// resend message in order to be handled by the regular messages processing:
			transceiver.$.trigger('messagesArrived', [{
				result: [message],
				nowTime: time
			}]);
		}
	}

	/**
	 * Handles a regular message.
	 *
	 * @param {Object} message
	 * @param {String} time
	 * @returns {null}
	 */
	function processMessage(message, time) {
		// translate new hash to old hash:
		message.senderHash = getOriginalHash(message.senderHash);
		message.recipientHash = getOriginalHash(message.recipientHash);

		var userHash = options.userData.hash;
		var senderHash = message.senderHash;
		var recipientHash = message.recipientHash;

		// sent by me:
		if (userHash == senderHash) {
			return null; // do nothing
		}

		// sent by someone else:
		if (userHash == recipientHash) {
			if (ignoreList.isIgnored(senderHash)) {
				return null;
			}

			// create new conversation if it does not exist already:
			var conversation = getConversationByHash(senderHash);
			if (conversation === null) {
				conversation = createConversation(message.senderId, message.senderHash, message.senderName, message.allowedRecipientToSender);

				// resend message, it can be caught by the window and shown:
				transceiver.$.trigger('messagesArrived', [{
					result: [message],
					nowTime: time
				}]);
			}

			if (!conversation.isInvitationEnabled() || invitationList.isInvitationInProgress(senderHash)) {
				return null;
			}

			// auto opens the chat:
			if (!options.privateMessageConfirmation) {
				$this.trigger('open', [conversation]);
				return null;
			}

			invitationList.startInvitation(senderHash);

			// show invitation dialog:
			var confirmDialog = new wisechat.ui.Dialog('confirm', dialogsContainer, {
				title: options.messages.messageInvitation,
				text: message.senderName + " " + options.messages.messageInfo2,
				buttons: [
					{
						label: options.messages.messageIgnoreUser,
						onClick: function () {
							ignoreList.addUser(message.senderHash);
							invitationList.endInvitation(message.senderHash);
							confirmDialog.close();
						}
					},
					{
						label: options.messages.messageNo,
						onClick: function () {
							invitationList.endInvitation(message.senderHash);
							confirmDialog.close();
						}
					},
					{
						label: options.messages.messageYes,
						onClick: function () {
							$this.trigger('open', [conversation]);
							invitationList.endInvitation(message.senderHash);
							confirmDialog.close();
						}
					}
				]
			});
			confirmDialog.show();
		}
	}

	/**
	 * Creates new wisechat.ui.Window and wisechat.pm.Conversation objects.
	 *
	 * @param {String} publicId User public ID
	 * @param {String} hash User ID
	 * @param {String} name User name
	 * @param {Boolean} isAllowed
	 * @returns {wisechat.pm.Conversation}
	 */
	function createConversation(publicId, hash, name, isAllowed) {
		var controls = controlsTemplate.clone().addClass('wcInvisible wcControls' + hash);
		if (!isAllowed) {
			controls.addClass('wcDisallowed');
			controls.append(
				jQuery('<div />').addClass('wcError wcDisallowedError').append(options.messages.messageError14)
			)
		}

		// create required DOM elements for wisechat.ui.Window:
		var windowContainer = jQuery('<div />')
			.append(jQuery('<div />').addClass('wcMessages wcInvisible wcMessages' + hash).addClass(options.messagesInline ? 'wcMessagesInline' : null))
			.append(controls);

		// create window:
		var window = new wisechat.ui.Window(options, windowContainer, transceiver, maintenance, usersList, settings, notifier, logger);
		window.setMessageCustomParameters({
			privateMessage: publicId
		});
		window.setMessagesFilter(function(message) {
			if (typeof message.isPrivate === 'undefined' || !message.isPrivate || typeof options.userData === 'undefined') {
				return false;
			}

			var userHash = options.userData.hash;

			// sent by me:
			if (userHash == message.senderHash) {
				return hash == message.recipientHash;
			}

			// sent by someone:
			if (userHash == message.recipientHash) {
				return hash == message.senderHash;
			}

			return false;
		});

		// create conversation:
		var conversation = new wisechat.pm.Conversation(publicId, hash, name, window);
		conversations.push(conversation);

		return conversation;
	}

	/**
	 * Get conversation by user ID.
	 *
	 * @param {String} hash User ID
	 * @returns {wisechat.pm.Conversation}
	 */
	function getConversationByHash(hash) {
		var key = -1;
		jQuery.each(conversations, function(index, element) {
			if (element.getHash() == hash) {
				key = index;
			}
		});

		return key >= 0 ? conversations[key] : null;
	}

	// external events:
	usersList.$.bind('userClick', onUserListUserClick);
	recentChats.$.bind('recentChatClicked', onRecentChatClicked);
	transceiver.$.bind('messagesArrived', onMessagesArrived);
	transceiver.$.bind('userMappingArrived', onUserMappingArrived);

	// public API:
	this.$ = $this;
	this.getConversationByHash = getConversationByHash;
};

/**
 * Tabbed view for private messages.
 *
 * @param {Object} options Plugin's global options
 * @param {wisechat.pm.PrivateMessages} privateMessages
 * @param {wisechat.ui.Window} channelWindow
 * @param {jQuery} tabsContainer
 * @param {jQuery} controlsContainer
 * @param {wisechat.maintenance.MaintenanceExecutor} maintenance
 * @constructor
 */
wisechat.pm.TabbedUI = function(options, privateMessages, channelWindow, tabsContainer, controlsContainer, maintenance) {
	var savedConversations = wisechat.pm.SavedConversations.getInstance(options.channelId);
	var windows = {};
	var leftNavButton = null;
	var rightNavButton = null;
	var usersHashMapChange = {};

	function createTabsContainer() {
		tabsContainer.append(
			jQuery('<span />')
				.data('hash', options.channelId)
				.addClass('wcMessagesContainerTab wcChannelTab')
				.append(
					jQuery('<sup class="wcTabAlert" />')
						.html('*')
						.hide()
				)
				.append(
					jQuery('<a />')
						.addClass('wcMessagesContainerTabLink wcTab' + options.channelId)
						.data('hash', options.channelId)
						.attr('href', 'javascript://')
						.html(options.channelName)
				)
		);
		leftNavButton = jQuery('<a />')
			.attr('href', 'javascript://')
			.addClass('wcPmNavigationButton wcLeftButton')
			.text('<');
		rightNavButton = jQuery('<a />')
			.attr('href', 'javascript://')
			.addClass('wcPmNavigationButton wcRightButton')
			.text('>');
		tabsContainer.append(leftNavButton);
		tabsContainer.append(rightNavButton);
	}

	function createTab(hash, name) {
		var tab = jQuery('<span />')
			.data('hash', hash)
			.addClass('wcMessagesContainerTab wcInvisible')
			.append(
				jQuery('<sup class="wcTabAlert" />')
					.html('*')
					.hide()
			)
			.append(
				jQuery('<a />')
					.addClass('wcMessagesContainerTabLink wcTab' + hash + ' wcUserName' + hash)
					.data('hash', hash)
					.attr('href', 'javascript://')
					.html(name)
			)
			.append(
				jQuery('<a />')
					.addClass('wcMessagesContainerTabCloseLink')
					.data('hash', hash)
					.attr('href', 'javascript://')
					.html('x')
			);
		tabsContainer.append(tab);

		return tab;
	}

	function getActiveConversationHash() {
		var tab = tabsContainer.find('.wcMessagesContainerTabActive a');

		return tab.length > 0 ? tab.data('hash') : null;
	}

	function getNextVisibleTab(hash, includeChannelTab) {
		var channelTabClassSelector = includeChannelTab ? '.wcChannelTabNonExisting' : '.wcChannelTab';
		var tabs = getTabByHash(hash).nextAll('.wcMessagesContainerTab').not(channelTabClassSelector).not('.wcInvisible');

		return tabs.length > 0 ? tabs.slice(0, 1).data('hash') : null;
	}

	function getPreviousVisibleTab(hash, includeChannelTab) {
		var channelTabClassSelector = includeChannelTab ? '.wcChannelTabNonExisting' : '.wcChannelTab';
		var tabs = getTabByHash(hash).prevAll('.wcMessagesContainerTab').not(channelTabClassSelector).not('.wcInvisible');

		return tabs.length > 0 ? jQuery(tabs[0]).data('hash') : null;
	}

	function getTabByHash(hash) {
		return tabsContainer.find('.wcTab' + hash).parent();
	}

	function getPrivateConversationsCount() {
		return tabsContainer.find('.wcMessagesContainerTab').not('.wcChannelTab').not('.wcInvisible').length;
	}

	function hideAlertInTab(hash) {
		getTabByHash(hash).find('.wcTabAlert').hide();
	}

	function showAlertInTab(hash) {
		getTabByHash(hash).find('.wcTabAlert').show();
	}

	function onMessagesContainerTabClick(event) {
		focusConversation(jQuery(this).data('hash'));
	}

	function onMessagesContainerTabCloseClick() {
		var hash = jQuery(this).data('hash');
		var activeConversationHash = getActiveConversationHash();

		hideConversation(hash);
		if (activeConversationHash == hash) {
			var focusTabHash = getNextVisibleTab(hash, false);
			if (focusTabHash === null) {
				focusTabHash = getPreviousVisibleTab(hash, false);
			}

			focusConversation(focusTabHash !== null ? focusTabHash : options.channelId);
		}
	}

	function onMessagesContainerLeftButtonClick() {
		var hash = getActiveConversationHash();
		if (hash !== null) {
			var previousHash = getPreviousVisibleTab(hash, true);
			if (previousHash !== null) {
				focusConversation(previousHash);
			}
		}
	}

	function onMessagesContainerRightButtonClick() {
		var hash = getActiveConversationHash();
		if (hash !== null) {
			var nextHash = getNextVisibleTab(hash, true);
			if (nextHash !== null) {
				focusConversation(nextHash);
			}
		}
	}

	function attachEventListeners() {
		tabsContainer.on('click', 'a.wcMessagesContainerTabLink', onMessagesContainerTabClick);
		tabsContainer.on('click', 'a.wcMessagesContainerTabCloseLink', onMessagesContainerTabCloseClick);
		tabsContainer.on('click', 'a.wcLeftButton', onMessagesContainerLeftButtonClick);
		tabsContainer.on('click', 'a.wcRightButton', onMessagesContainerRightButtonClick);
	}

	/**
	 * Shows conversation: its window, controls and the tab. It also refreshes the window and hides alert indicator in the tab.
	 *
	 * @param {String} hash Conversation ID
	 */
	function focusConversation(hash) {
		var conversation = privateMessages.getConversationByHash(hash);
		if (conversation === null && hash != options.channelId) {
			return;
		}

		// show messages container:
		tabsContainer.siblings('.wcMessages').addClass('wcInvisible');
		tabsContainer.siblings('.wcMessages' + hash).removeClass('wcInvisible');

		// show controls container:
		controlsContainer.parent().find('.wcControls').addClass('wcInvisible');
		controlsContainer.parent().find('.wcControls' + hash).removeClass('wcInvisible');

		// activate tab:
		tabsContainer.find('.wcMessagesContainerTab').removeClass('wcMessagesContainerTabActive');
		tabsContainer.find('.wcTab' + hash).parent().addClass('wcMessagesContainerTabActive').removeClass('wcInvisible');

		// show tabs only if private conversation is being activated:
		if (hash != options.channelId) {
			tabsContainer.removeClass('wcInvisible');
		}

		// refresh window:
		if (conversation !== null) {
			conversation.getWindow().refresh();
		} else if (hash == options.channelId) {
			channelWindow.refresh();
		}

		// hide alert mark in the tab:
		hideAlertInTab(hash);

		// mark active:
		savedConversations.markActive(hash);

		// set navigation buttons:
		leftNavButton.toggleClass('wcPmNavigationButtonDisabled', getPreviousVisibleTab(hash, true) === null);
		rightNavButton.toggleClass('wcPmNavigationButtonDisabled', getNextVisibleTab(hash, true) === null);
	}

	/**
	 * @param {String} hash
	 */
	function hideConversation(hash) {
		tabsContainer.siblings('.wcMessages' + hash).addClass('wcInvisible');
		controlsContainer.parent().find('.wcControls' + hash).addClass('wcInvisible');
		tabsContainer.find('.wcTab' + hash).parent().removeClass('wcMessagesContainerTabActive').addClass('wcInvisible');

		// hide tabs if there is only channel window left:
		if (getPrivateConversationsCount() === 0) {
			tabsContainer.addClass('wcInvisible');
		}

		// enable invitations when the tab is not visible:
		var conversation = privateMessages.getConversationByHash(hash);
		if (conversation !== null) {
			conversation.setInvitationEnabled(true);
		}

		// mark conversation as closed:
		savedConversations.clear(hash);

		// newly generated hash conversation:
		if (typeof usersHashMapChange[hash] !== 'undefined') {
			savedConversations.clear(usersHashMapChange[hash]);
		}
	}

	/**
	 * Shows alert in the tab if the tab is not active and tabs are visible.
	 *
	 * @param {String} hash Conversation ID
	 */
	function showAlertIfNotActive(hash) {
		if (!tabsContainer.hasClass('wcInvisible') && getActiveConversationHash() != hash) {
			showAlertInTab(hash);
		}
	}

	/**
	 * Opens conversation.
	 *
	 * @param {Event} event
	 * @param {wisechat.pm.Conversation} conversation
	 */
	function onConversationOpen(event, conversation) {
		// insert the window and create the tab:
		if (typeof windows[conversation.getHash()] === 'undefined') {
			var conversationWindow = conversation.getWindow();

			// insert window into DOM and create corresponding tab:
			conversationWindow.insertAfter(tabsContainer, controlsContainer);
			createTab(conversation.getHash(), conversation.getName());

			conversationWindow.$.bind('messageShow', function(event, message) {
				showAlertIfNotActive(conversation.getHash());
			});
			windows[conversation.getHash()] = conversationWindow;
		}

		// show window and tabs:
		focusConversation(conversation.getHash());

		// disable invitations when the tab is visible:
		conversation.setInvitationEnabled(false);

		// mark conversation as open:
		savedConversations.markOpen(conversation.getHash());
	}

	/**
	 * Executed when conversation are being restored after chat initialization.
	 *
	 * @param {Event} event
	 * @param {Array} conversations
	 */
	function onConversationsRestore(event, conversations) {
		var activeConversationHash = savedConversations.getActive();
		for (var x = 0; x < conversations.length; x++) {
			var conversation = conversations[x];

			if (savedConversations.isOpen(conversation.getHash())) {
				onConversationOpen(event, conversation);
			} else {
				conversation.setInvitationEnabled(true);
			}
		}

		if (activeConversationHash !== null) {
			focusConversation(activeConversationHash);
		}
	}


	/**
	 * Executed if user hash changes.
	 *
	 * @param {Event} event
	 * @param {String} oldHash
	 * @param {String} newHash
	 */
	function onUserHashChange(event, oldHash, newHash) {
		if (savedConversations.isOpen(oldHash)) {
			// mark conversation open for the new hash:
			savedConversations.markOpen(newHash);
			usersHashMapChange[oldHash] = newHash;
		}
	}

	// events:
	privateMessages.$.bind('open', onConversationOpen);
	privateMessages.$.bind('restore', onConversationsRestore);
	privateMessages.$.bind('userHashChange', onUserHashChange);
	channelWindow.$.bind('messageShow', function(event, message) {
		showAlertIfNotActive(options.channelId);
	});

	// maintenance events:
	maintenance.$.bind('refreshPlainUserNameByHash', function(event, data) {
		tabsContainer.find('.wcUserName' + data.hash).html(data.name); // refresh tabs when user name changes
	});

	// initializations:
	createTabsContainer();
	attachEventListeners();
};

/**
 * Sidebar view for private messages.
 *
 * @param {wisechat.utils.Options} chatOptions Plugin's global options
 * @param {jQuery} container
 * @param {wisechat.pm.PrivateMessages} privateMessages
 * @param {wisechat.ui.Window} channelWindow
 * @param {wisechat.ui.UsersList} usersList
 * @param {jQuery} tabsContainer
 * @param {jQuery} controlsContainer
 * @param {wisechat.maintenance.MaintenanceExecutor} maintenance
 * @constructor
 */
wisechat.pm.SidebarUI = function(chatOptions, container, privateMessages, channelWindow, usersList, tabsContainer, controlsContainer, maintenance) {
	var $this = jQuery(this);
	var options = chatOptions.getPlain();
	var savedConversations = wisechat.pm.SavedConversations.getInstance(options.channelId);
	/**
	 * @type {Array.<wisechat.ui.Window>}
	 */
	var windows = {};
	var usersHashMapChange = {};

	function getTabByHash(hash) {
		return tabsContainer.find('.wcTab' + hash).parent();
	}

	function onMessagesContainerTabClick(event) {
		var hash = jQuery(this).data('hash');
		if (typeof windows[hash] === 'undefined') {
			return;
		}
		var conversationWindow = windows[hash];
		var isMinimize = false;

		if (conversationWindow.getMessages().isVisible()) {
			conversationWindow.setInactive();
			conversationWindow.getMessages().hide();
			conversationWindow.getControls().hide();
			savedConversations.markMinimized(hash);
			getTabByHash(hash).addClass('wcMessagesContainerTabMinimized');
			getTabByHash(hash).find('.wcMessagesContainerTabMinMaxLink').attr('title', options.messages.messageMaximize);
			isMinimize = true;
			event.stopPropagation();
		} else {
			conversationWindow.getMessages().show();
			conversationWindow.getControls().show();
			savedConversations.unmarkMinimized(hash);
			conversationWindow.hideUnreadMessagesFlag();
			getTabByHash(hash).removeClass('wcMessagesContainerTabMinimized');
			getTabByHash(hash).find('.wcMessagesContainerTabMinMaxLink').attr('title', options.messages.messageMinimize);
			conversationWindow.setActive();
			conversationWindow.focus();
		}

		// arrange windows positions on the screen:
		positionWindows();

		if (isMinimize) {
			$this.trigger('windowMinimized', [conversationWindow]);
		} else {
			$this.trigger('windowMaximized', [conversationWindow]);
		}
	}

	/**
	 * @param {String} hash
	 */
	function hideConversation(hash) {
		tabsContainer.siblings('.wcMessages' + hash).addClass('wcInvisible');
		controlsContainer.parent().find('.wcControls' + hash).addClass('wcInvisible');
		tabsContainer.find('.wcTab' + hash).parent().addClass('wcInvisible');

		// enable invitations when the tab is not visible:
		var conversation = privateMessages.getConversationByHash(hash);
		if (conversation !== null) {
			conversation.setInvitationEnabled(true);
		}

		// mark conversation as closed:
		savedConversations.clear(hash);

		// newly generated hash conversation:
		if (typeof usersHashMapChange[hash] !== 'undefined') {
			savedConversations.clear(usersHashMapChange[hash]);
		}

		// arrange windows positions on the screen:
		positionWindows();

		$this.trigger('windowHide', [windows[hash]]);
	}

	function onMessagesContainerTabCloseLinkClick(event) {
		event.stopPropagation();
		hideConversation(jQuery(this).data('hash'));
	}

	function attachEventListeners() {
		tabsContainer.on('click', 'a.wcMessagesContainerTabCloseLink', onMessagesContainerTabCloseLinkClick);
		tabsContainer.on('click', 'span.wcMessagesContainerTab', onMessagesContainerTabClick);
	}

	function createTab(hash, name) {
		var tab = jQuery('<span />')
			.data('hash', hash)
			.addClass('wcMessagesContainerTab wcInvisible')
			.append(
				jQuery('<sup class="wcUnreadMessagesFlag" />')
					.html('*')
					.hide()
			)
			.append(
				jQuery('<a />')
					.addClass('wcMessagesContainerTabLink wcTab' + hash + ' wcUserName' + hash)
					.data('hash', hash)
					.attr('href', 'javascript://')
					.html(name)
			)
			.append(
				jQuery('<a />')
					.addClass('wcMessagesContainerTabMinMaxLink')
					.data('hash', hash)
					.attr('href', 'javascript://')
					.attr('title', options.messages.messageMinimize)
					.html('')
			)
			.append(
				jQuery('<a />')
					.addClass('wcMessagesContainerTabCloseLink')
					.data('hash', hash)
					.attr('href', 'javascript://')
					.attr('title', options.messages.messageClose)
					.html('')
			);
		tabsContainer.append(tab);

		return tab;
	}

	/**
	 * Shows conversation: its window, controls and the tab. It also refreshes the window and hides alert indicator in the tab.
	 *
	 * @param {String} hash Conversation ID
	 * @param {Boolean} maintainMinimized
	 */
	function focusConversation(hash, maintainMinimized) {
		var conversation = privateMessages.getConversationByHash(hash);
		if (conversation === null) {
			return;
		}

		if (!savedConversations.isMinimized(hash) || maintainMinimized !== true) {
			// show messages container:
			tabsContainer.siblings('.wcMessages' + hash).removeClass('wcInvisible');

			// show controls container:
			controlsContainer.parent().find('.wcControls' + hash).removeClass('wcInvisible');

			getTabByHash(hash).removeClass('wcMessagesContainerTabMinimized');

			savedConversations.unmarkMinimized(hash);
		} else {
			getTabByHash(hash).addClass('wcMessagesContainerTabMinimized');
			getTabByHash(hash).find('.wcMessagesContainerTabMinMaxLink').attr('title', options.messages.messageMaximize);
		}

		// activate tab:
		tabsContainer.find('.wcTab' + hash).parent().removeClass('wcInvisible');
		tabsContainer.removeClass('wcInvisible');

		conversation.getWindow().refresh();
		conversation.getWindow().focus();
		conversation.getWindow().hideUnreadMessagesFlag();
	}

	/**
	 * Opens conversation.
	 *
	 * @param {Event} event
	 * @param {wisechat.pm.Conversation} conversation
	 * @param {Boolean} maintainMinimized
	 */
	function onConversationOpen(event, conversation, maintainMinimized) {
		var hash = conversation.getHash();

		// insert the window and create the tab:
		if (typeof windows[hash] === 'undefined') {
			var conversationWindow = conversation.getWindow();
			conversationWindow.setTitleContainer(createTab(hash, conversation.getName()));

			// insert window into DOM and create corresponding tab:
			conversationWindow.insertAfter(tabsContainer, controlsContainer);

			conversationWindow.$.bind('messageShow', function(event, message) {
				if (!conversationWindow.isActive()) {
					conversationWindow.showUnreadMessagesFlag();
				}
			});
			conversationWindow.$.bind('windowFocus', function(event, originalEvent) {
				conversationWindow.hideUnreadMessagesFlag();
			});
			conversationWindow.$.bind('clickInside', function(event, originalEvent) {
				conversationWindow.hideUnreadMessagesFlag();
				conversationWindow.setActive();
			});
			conversationWindow.$.bind('clickOutside', function(event, originalEvent) {
				var tab = getTabByHash(hash);

				if (jQuery(originalEvent.target).closest(tab).length > 0 && !savedConversations.isMinimized(hash) || conversationWindow.isFocused()) {
					conversationWindow.setActive();
				} else {
					conversationWindow.setInactive();
				}
			});

			windows[hash] = conversationWindow;
		}

		// show window and tabs:
		focusConversation(hash, maintainMinimized);

		// disable invitations when the tab is visible:
		conversation.setInvitationEnabled(false);

		// mark conversation as open:
		savedConversations.markOpen(hash);

		// arrange windows positions on the screen:
		positionWindows();

		$this.trigger('windowOpen', [windows[hash]]);
	}

	/**
	 * Executed when conversation are being restored after chat initialization.
	 *
	 * @param {Event} event
	 * @param {Array} conversations
	 */
	function onConversationsRestore(event, conversations) {
		for (var x = 0; x < conversations.length; x++) {
			var conversation = conversations[x];

			if (savedConversations.isOpen(conversation.getHash())) {
				onConversationOpen(event, conversation, true);
			} else {
				conversation.setInvitationEnabled(true);
			}
		}

		$this.trigger('windowsRestored', []);
	}

	function positionWindows() {
		var right = 0;
		if (options.fbDisableChannel) {
			right = usersList.getWidth()
		} else {
			var channelWindowLeft = channelWindow.getMessagesContainer().offset().left;
			if (channelWindowLeft == 0) {
				// messages container may be hidden:
				channelWindowLeft = channelWindow.getMessagesContainer().siblings('.wcWindowTitle').offset().left;
			}
			right = jQuery(window).width() - channelWindowLeft;
		}
		var width = channelWindow.getMessagesContainer().outerWidth();

		for (var hash in windows) {
			var conversationWindow = windows[hash];
			var messages = conversationWindow.getMessages();
			var controls = conversationWindow.getControls();
			var tabContainer = getTabByHash(hash);

			// omit invisible windows:
			if (tabContainer.hasClass('wcInvisible')) {
				continue;
			}

			var mobileNavigation = container.find('.wcSidebarModeMobileNavigation');
			var mobileNavigationHeight = mobileNavigation.is(":visible") && mobileNavigation.outerHeight() > 0 ? mobileNavigation.outerHeight() : 0;
			var bottomOffset = mobileNavigationHeight + options.fbBottomOffset;

			if (!savedConversations.isMinimized(hash)) {
				// safe refresh:
				conversationWindow.refresh();

				// position of controls container:
				controls.setWidth(width);
				controls.setRight(right);
				controls.setBottom(bottomOffset);
				controls.refresh();

				// position of messages container:
				messages.setWidth(width);
				messages.setRight(right);
				messages.setBottom((controls.getHeight() > 0 ? controls.getHeight() : 0) + bottomOffset);

				var effectiveChatHeight = chatOptions.getEffectiveChatHeightBasedOnPercentageHeight();
				if (effectiveChatHeight !== null) {
					effectiveChatHeight -= (controls.getHeight() + tabContainer.outerHeight() + mobileNavigationHeight);
					messages.setHeight(effectiveChatHeight);
				}
			}

			tabContainer.css({
				width: width,
				right: right,
				bottom: (messages.isVisible() ? messages.getHeight() + controls.getHeight() : 0) + bottomOffset
			});

			right += width;
		}
	}

	function getWindows() {
		return windows;
	}

	/**
	 * Executed if user hash changes.
	 *
	 * @param {Event} event
	 * @param {String} oldHash
	 * @param {String} newHash
	 */
	function onUserHashChange(event, oldHash, newHash) {
		if (savedConversations.isOpen(oldHash)) {
			// mark conversation open for the new hash:
			savedConversations.markOpen(newHash);
			usersHashMapChange[oldHash] = newHash;
		}
	}

	// events:
	privateMessages.$.bind('open', onConversationOpen);
	privateMessages.$.bind('restore', onConversationsRestore);
	privateMessages.$.bind('userHashChange', onUserHashChange);
	channelWindow.$.bind('messageShow', function(event, message) {
		if (!channelWindow.isActive()) {
			channelWindow.showUnreadMessagesFlag();
		}
	});
	channelWindow.$.bind('windowFocus', function(event, originalEvent) {
		channelWindow.hideUnreadMessagesFlag();
	});

	// maintenance events:
	maintenance.$.bind('refreshPlainUserNameByHash', function(event, data) {
		tabsContainer.find('.wcUserName' + data.hash).html(data.name); // refresh tabs when user name changes
	});

	jQuery(window).resize(function () {
		positionWindows()
	});

	// initializations:
	attachEventListeners();

	// public API:
	this.$ = $this;
	this.getWindows = getWindows;
};

/**
 * Recent chats layer support.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} indicatorContainer
 * @param {wisechat.core.MessagesTransceiver} transceiver
 * @constructor
 */
wisechat.pm.RecentChats = function(options, indicatorContainer, transceiver) {
	var savedConversations = wisechat.pm.SavedConversations.getInstance(options.channelId);
	var dateAndTimeRenderer = new wisechat.ui.DateAndTimeRenderer(options);
	var dateFormatter = wisechat.utils.dateFormatter;
	var $this = jQuery(this);
	var pendingChats = {};
	var recentChats = [];
	var layer = null;
	var LAYER_WIDTH = 400;
	var MESSAGE_MAX_LENGTH = 50;
	var layerContainer = jQuery('body');
	var nowTime = null;

	function onPendingChatReceived(event, pendingChat) {
		// don't show active chat as pending:
		if (savedConversations.getActive() == pendingChat.hash) {
			checkPendingChat(pendingChat.id);

			return;
		}
		pendingChats[pendingChat.hash] = pendingChat;

		refreshIndicator();
	}

	function onActiveChatMarked(event, activeChatHash) {
		moveToRecentChats(activeChatHash);
	}

	/**
	 * Intercepts private messages restoring phase and creates the list of recent chats.
	 *
	 * @param {Event} event
	 * @param {Object} response
	 */
	function onMessagesArrived(event, response) {
		if (typeof options.userData === 'undefined' || !jQuery.isPlainObject(response) || typeof response.restorePrivateConversations === 'undefined') {
			return;
		}

		var tempRecentChats = {};
		for (var x = 0; x < response.result.length; x++) {
			var message = response.result[x];
			if (typeof message.isPrivate === 'undefined' || !message.isPrivate) {
				continue;
			}

			// don't process own messages:
			if (options.userData.hash == message.senderHash) {
				continue;
			}

			// parse message and date from the HTML source:
			var parsedMessageContent = jQuery(message.text);
			var text = parsedMessageContent.find('.wcMessageContent').text();
			if (text.length > MESSAGE_MAX_LENGTH) {
				text = text.substring(0, MESSAGE_MAX_LENGTH) + ' ...';
			}
			var date = parsedMessageContent.find('.wcMessageTime').data('utc');

			tempRecentChats[message.senderHash] = {
				id: message.senderId,
				name: message.senderName,
				hash: message.senderHash,
				message: text,
				date: date
			};
		}

		for (hash in tempRecentChats) {
			recentChats.push(tempRecentChats[hash]);
		}
		recentChats = recentChats.reverse();
	}

	function refreshIndicator() {
		var totalPendingChats = 0;
		for (var hash in pendingChats) {
			totalPendingChats++;
		}

		indicatorContainer.empty();
		if (totalPendingChats > 0) {
			var numberElement = jQuery('<span>')
				.addClass('wcPendingChatsNumber')
				.text(totalPendingChats);
			indicatorContainer.append(numberElement);
		}
	}

	function createLayer() {
		if (layer !== null) {
			return;
		}
		layer = jQuery('<div>').addClass('wcRecentChats').addClass('wcInvisible');
		layerContainer.append(layer);
		layer.css({
			backgroundColor: wisechat.utils.htmlUtils.getAncestorBackgroundColor(indicatorContainer)
		});
		layer.on('click', '.wcRecentChatsRow', onRecentChatClick);
	}

	function createRecentChatRow(recentChat, rowClass) {
		var recentChatRow = jQuery('<div>').addClass('wcRecentChatsRow ' + rowClass).data('hash', recentChat.hash);
		var sender = jQuery('<span>').addClass('wcRecentChatName').text(recentChat.name);
		var date = jQuery('<span>').addClass('wcRecentChatDate').data('utc', recentChat.date);
		var message = jQuery('<span>').addClass('wcRecentChatMessage').text(recentChat.message);

		recentChatRow.append(sender);
		recentChatRow.append(date);
		recentChatRow.append('<br class="wcClear" />');
		recentChatRow.append(message);

		return recentChatRow;
	}

	function refreshLayerContent() {
		var hash;
		var hasChats = false;
		layer.empty();

		// add pending chats:
		for (hash in pendingChats) {
			layer.append(createRecentChatRow(pendingChats[hash], 'wcPendingChat'));
			hasChats = true;
		}

		// add recent chats:
		for (var i = 0; i < recentChats.length; i++) {
			var recentChat = recentChats[i];

			if (!(recentChat.hash in pendingChats)) {
				layer.append(createRecentChatRow(recentChat, ''));
				hasChats = true;
			}
		}

		if (!hasChats) {
			layer.append('<span class="wcRecentChatsEmpty">' + options.messages.messageNoRecentChats + '</span>');
		}

		refreshChatsTime();
	}

	function refreshLayerPosition() {
		var windowWidth = jQuery(window).width();
		var windowHeight = jQuery(window).height();
		var indicatorTopPosition = indicatorContainer.offset().top;
		var indicatorLeftPosition = indicatorContainer.offset().left;
		var indicatorHeight = indicatorContainer.outerHeight();
		var layerTopPosition = indicatorTopPosition + indicatorHeight;
		var layerLeftPosition = indicatorLeftPosition;
		var layerRightPosition = null;
		var layerWidth = ''; // the default value

		if (indicatorLeftPosition + LAYER_WIDTH > windowWidth) {
			if (windowWidth > LAYER_WIDTH) {
				layerLeftPosition = null;
				layerRightPosition = 0;
			} else {
				layerWidth = '100%';
				layerLeftPosition = 0;
			}
		}

		layer.css({
			width: layerWidth,
			top: layerTopPosition
		});
		if (layerLeftPosition !== null) {
			layer.css('left', layerLeftPosition);
			layer.css('right', '');
		} else if (layerRightPosition !== null) {
			layer.css('right', layerRightPosition);
			layer.css('left', '');
		}

		// adjust the height:
		var layerHeight = layer.outerHeight();
		var windowScrollTop = jQuery(window).scrollTop();
		if (indicatorTopPosition + layerHeight > (windowHeight + windowScrollTop)) {
			layerTopPosition = indicatorTopPosition - layerHeight;
		}
		layer.css({
			top: layerTopPosition
		});
	}

	function refreshChatsTime() {
		if (nowTime === null || layer === null) {
			return;
		}

		layer.find('.wcRecentChatDate:not([data-fixed])').each(function(index, element) {
			element = jQuery(element);

			if (typeof element.data('utc') !== 'undefined' && element.data('utc').length > 0) {
				var date = dateFormatter.parseISODate(element.data('utc'));
				var nowDate = dateFormatter.parseISODate(nowTime);
				dateAndTimeRenderer.formatElapsedDateAndTime(date, nowDate, element);
			}
		});
	}

	function getRecentChat(hash) {
		for (var i = 0; i < recentChats.length; i++) {
			var recentChat = recentChats[i];

			if (recentChat.hash == hash) {
				return recentChat;
			}
		}

		return null;
	}

	function openLayer() {
		createLayer();
		layer.removeClass('wcInvisible');
		refreshLayerContent();
		refreshLayerPosition();
		indicatorContainer.addClass('wcRecentChatsIndicatorActive');
	}

	function onIndicatorClick(event) {
		event.preventDefault();

		if (isLayerOpen()) {
			closeLayer();
		} else {
			openLayer();
		}
	}

	function closeLayer() {
		if (isLayerOpen()) {
			layer.addClass('wcInvisible');
			indicatorContainer.removeClass('wcRecentChatsIndicatorActive');
		}
	}

	function isLayerOpen() {
		return layer !== null && !layer.hasClass('wcInvisible');
	}

	function onWindowClick(event) {
		var target = jQuery(event.target);

		if (isLayerOpen() && target.closest(layer).length === 0 && target.closest(indicatorContainer).length === 0) {
			closeLayer();
		}
	}

	function onWindowResize(event) {
		if (isLayerOpen()) {
			refreshLayerPosition();
		}
	}

	function onRecentChatClick(event) {
		var hash = jQuery(event.target).closest('.wcRecentChatsRow').data('hash');

		if (hash in pendingChats) {
			$this.trigger('recentChatClicked', [pendingChats[hash]]);
			moveToRecentChats(hash);
		} else if (getRecentChat(hash) !== null) {
			$this.trigger('recentChatClicked', [getRecentChat(hash)]);
		}

		closeLayer();
	}

	function moveToRecentChats(pendingChatHash) {
		if (typeof pendingChats[pendingChatHash] === 'undefined') {
			return;
		}

		var newRecentChats = [];
		if (getRecentChat(pendingChatHash) === null) {
			newRecentChats.push(pendingChats[pendingChatHash]);
		}
		for (var i = 0; i < recentChats.length; i++) {
			newRecentChats.push(recentChats[i]);
		}
		recentChats = newRecentChats;

		var userPublicId = pendingChats[pendingChatHash].id;
		delete pendingChats[pendingChatHash];

		refreshIndicator();
		checkPendingChat(userPublicId);
	}

	function checkPendingChat(userPublicId) {
		transceiver.sendUserCommand('checkPendingChat', { userPublicId: userPublicId });
	}

	function onHeartBeat(event, response) {
		nowTime = response.nowTime;
		refreshChatsTime();
	}

	// DOM events:
	jQuery(window).on('click', onWindowClick);
	jQuery(window).on('resize', onWindowResize);

	transceiver.$.bind('pendingChatReceived', onPendingChatReceived);
	transceiver.$.bind('heartBeat', onHeartBeat);
	transceiver.$.bind('messagesArrived', onMessagesArrived);
	savedConversations.$.bind('markedActive', onActiveChatMarked);
	indicatorContainer.on('click', onIndicatorClick);

	// public API:
	this.$ = $this;
};
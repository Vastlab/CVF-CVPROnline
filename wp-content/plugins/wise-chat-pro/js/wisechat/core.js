/**
 * Wise Chat Core namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.core = {};
wisechat.core.instances = {};

/**
 * Global variables.
 *
 * @type {{window: Window, document: HTMLDocument}}
 */
wisechat.core.globals = {
	window: window,
	document: document
};

/**
 * Instance class.
 *
 * @param {Object} options Plugin's global options
 * @constructor
 */
wisechat.core.Instance = function(options) {
	var container = jQuery('#' + options.chatId);
	var channelWindowTitle = container.find('.wcWindowTitle');
	var chatOptions = new wisechat.utils.Options(options);
	var localSettings = new wisechat.core.LocalSettings(options.channelId);
	var logger = new wisechat.ui.VisualLogger(options, container.find('.wcVisualLogger'));
	var notifier = new wisechat.utils.Notifier(options);
	var settings = new wisechat.settings.Settings(options, container, logger);
	var maintenance = new wisechat.maintenance.MaintenanceExecutor(options, logger);
	var transceiver = new wisechat.core.MessagesTransceiver(options, logger);
	var usersList = new wisechat.ui.UsersList(options, container.find('.wcUsersList'), container.find('.wcUserListTitle'), maintenance);
	var channelWindow = new wisechat.ui.Window(options, container, transceiver, maintenance, usersList, settings, notifier, logger);
	channelWindow.setTitleContainer(channelWindowTitle);

	var privateMessagesDecorator = null;
	if (options.enablePrivateMessages && options.allowToReceiveMessages) {
		var tabsContainer = container.find('.wcMessagesContainersTabs');
		var controlsContainer = container.find('.wcControls');
		var controlsElementTemplate = container.find('.wcControls').clone().removeClass('wcControls' + options.channelId);
		var recentChats = new wisechat.pm.RecentChats(options, container.find('.wcRecentChatsIndicator'), transceiver);

		var privateMessages = new wisechat.pm.PrivateMessages(
			options, usersList, recentChats,
			transceiver, maintenance, settings, notifier, logger,
			controlsElementTemplate, container
		);



		if (options.sidebarMode) {
			// Sidebar view for private messages:
			privateMessagesDecorator = new wisechat.pm.SidebarUI(
				chatOptions, container, privateMessages, channelWindow, usersList, tabsContainer, controlsContainer, maintenance, transceiver
			);
		} else {
			// Tabbed view for private messages:
			privateMessagesDecorator = new wisechat.pm.TabbedUI(
				options, privateMessages, channelWindow, tabsContainer, controlsContainer, maintenance, transceiver
			);
		}
	}

	// start maintenance:
	maintenance.start();

	// start transceiver:
	if (options.allowToReceiveMessages) {
		transceiver.start();
	}

	// maintenance events:
	maintenance.$.bind('reload', function(event, data) {
		if (typeof location.reload !== 'undefined') {
			location.reload();
		}
	});
	maintenance.$.bind('showErrorMessage', function(event, data) {
		logger.logError(data.message);
	});
	maintenance.$.bind('rights', function(event, data) {
		options.rights = data;
	});
	maintenance.$.bind('userData', function(event, data) {
		options.userData = data;

		// load private messages when userData is saved:
		if (options.enablePrivateMessages && options.allowToReceiveMessages) {
			transceiver.loadPrivateMessages();
		}
	});
	maintenance.$.bind('checkSum', function(event, data) {
		if (data !== null) {
			options.checksum = data;
		}
	});
	maintenance.$.bind('refreshUsersCounter', function(event, data) {
		var usersCounter = container.find('.wcUsersCounter span');
		var total = data.total > 0 ? data.total : 1;
		if (options.channelUsersLimit > 0) {
			usersCounter.html(total + " / " + options.channelUsersLimit);
		} else {
			usersCounter.html(total);
		}
	});

	maintenance.$.bind('reportAbsentUsers', function(event, data) {
		if (jQuery.isArray(data.users) && data.users.length > 0) {
			if (options.enableLeaveNotification) {
				for (var y = 0; y < data.users.length; y++) {
					var user = data.users[y];
					var coreText = user.name + ' ' + options.messages.messageHasLeftTheChannel;
					channelWindow.getMessages().showPlainMessage(coreText);
				}
			}
			if (options.leaveSoundNotification && data.users.length > 0) {
				notifier.sendNotificationForEvent('userLeft');
			}
		}
	});
	maintenance.$.bind('reportNewUsers', function(event, data) {
		if (jQuery.isArray(data.users) && data.users.length > 0) {
			if (options.enableJoinNotification) {
				for (var y = 0; y < data.users.length; y++) {
					var user = data.users[y];
					var coreText = user.name + ' ' + options.messages.messageHasJoinedTheChannel;
					channelWindow.getMessages().showPlainMessage(coreText);
				}
			}

			if (options.joinSoundNotification && data.users.length > 0) {
				notifier.sendNotificationForEvent('userJoined');
			}
		}
	});

	jQuery(window).resize(function () {
		if (!options.sidebarMode) {
			container.toggleClass('wcWidth300', container.width() < 300);
			container.toggleClass('wcWidth400', container.width() < 400);
			container.toggleClass('wcWidth450', container.width() < 450);
			container.toggleClass('wcWidth500', container.width() < 500);
		}

		// reset options.fbBottomOffset for narrow screens:
		if (options.fbBottomThreshold > 0) {
			if (container.width() > options.fbBottomThreshold) {
				options.fbBottomOffsetBackup = options.fbBottomOffset;
				options.fbBottomOffset = 0;
			} else if (typeof options.fbBottomOffsetBackup !== 'undefined') {
				options.fbBottomOffset = options.fbBottomOffsetBackup;
			}
		}
	}).trigger('resize');

	if (!options.sidebarMode) {
		new wisechat.ui.UserListToggleButton(options, container, usersList);
	} else {
		var customizations = container.find('.wcCustomizations');
		var usersCounter = container.find('.wcUsersCounter');

		new wisechat.ui.SidebarModeDecorator(
			chatOptions, container, usersList, channelWindow, settings, usersCounter, logger, privateMessagesDecorator
		);
	}

	// show debug box:
	if (options.debugMode) {
		logger.showDebug();
	}
};

/**
 * Returns single instance of the chat for given chatId (options.chatId value).
 *
 * @param {Object} options Plugin's global options
 * @returns {wisechat.core.Instance}
 * @static
 */
wisechat.core.Instance.getInstance = function(options) {
	if (typeof(wisechat.core.instances[options.chatId]) === 'undefined') {
		wisechat.core.instances[options.chatId] = new wisechat.core.Instance(options);
	}

	return wisechat.core.instances[options.chatId];
};

/**
 * MessagesTransceiver class. Sends and receives messages.
 *
 * @param {Object} options Plugin's global options
 * @param {wisechat.ui.VisualLogger} logger
 * @constructor
 */
wisechat.core.MessagesTransceiver = function(options, logger) {
	var $this = jQuery(this);
	var engine = new wisechat.engines.AjaxEngine(options);
	var isEngineInitialized = false;
	var arePrivateMessagesLoaded = false;

	/**
	 * Starts the transceiver. Emits single 'initialize' event.
	 */
	function start() {
		if (isEngineInitialized) {
			return;
		}
		isEngineInitialized = true;
		$this.trigger('initialize', []);
		engine.initialize();
	}

	/**
	 * Loads just private messages.
	 */
	function loadPrivateMessages() {
		if (arePrivateMessagesLoaded) {
			return;
		}
		arePrivateMessagesLoaded = true;
		engine.loadPrivateMessages();
	}

	function onNewMessagesArrived(response) {
		if (response.result && response.result.length > 0) {
			if (options.messagesOrder != 'ascending') {
				response.result.reverse();
			}
			$this.trigger('messagesArrived', [response]);
		}

		// pending chats are received together with messages:
		if (typeof response.pendingChats !== 'undefined') {
			for (x = 0; x < response.pendingChats.length; x++) {
				$this.trigger('pendingChatReceived', [response.pendingChats[x]]);
			}
		}

		$this.trigger('heartBeat', [{
			nowTime: response.nowTime
		}]);
	}

	function onMessageArrivalError(error) {
		logger.logError(error);
	}

	function onDebugLogMessage(message) {
		logger.logDebug(message);
	}

	function sendMessage(message, attachments, customParameters, successListener, progressListener, errorListener) {
		var successListenerInterceptor = function(response) {
			if (typeof response.userMapping !== 'undefined') {
				$this.trigger('userMappingArrived', [response.userMapping]);
			}
			successListener(response);
		};

		engine.sendMessage({
				content: message,
				attachments: attachments,
				customParameters: customParameters,
				channelId: options.channelId,
				checksum: options.checksum
			},
			successListenerInterceptor,
			progressListener,
			errorListener
		);
	}

	/**
	 * Loads a single message.
	 *
	 * @param {Number} messageId
	 * @param {String} channelId
	 * @param {String} checksum
	 */
	function getMessage(messageId, channelId, checksum) {
		engine.getMessage({
				messageId: messageId,
				channelId: channelId,
				checksum: checksum
			},
			function(response) {
				if (typeof response.result !== 'undefined' && jQuery.isArray(response.result)) {
					for (var x = 0; x < response.result.length; x++) {
						$this.trigger('messageArrived', [response.result[x]]);
					}
				}
				if (typeof response.nowTime !== 'undefined') {
					$this.trigger('heartBeat', [{
						nowTime: response.nowTime
					}]);
				}
			},
			function(error) {

			}
		);
	}

	/**
	 * Sends user command.
	 *
	 * @param {String} command
	 * @param {Object} parameters
	 */
	function sendUserCommand(command, parameters) {
		engine.sendUserCommand(
			command, parameters,
			function(response) {

			},
			function(error) {

			}
		);
	}

	engine.setMessagesCallback(onNewMessagesArrived);
	engine.setMessagesErrorCallback(onMessageArrivalError);
	engine.setDebugLoggerCallback(onDebugLogMessage);

	// public API:
	this.$ = $this;
	this.sendMessage = sendMessage;
	this.getMessage = getMessage;
	this.loadPrivateMessages = loadPrivateMessages;
	this.sendUserCommand = sendUserCommand;
	this.start = start;
};

/**
 * Manages the local settings for given channel.
 *
 * @param {String} channelId
 * @constructor
 */
wisechat.core.LocalSettings = function(channelId) {
	var LOCAL_STORAGE_KEY = "WiseChatLocalSettings";

	/**
	 * Saves key-value pair.
	 *
	 * @param {String} key
	 * @param {*} value
	 */
	function set(key, value) {
		var data = getData();

		if (typeof data[channelId] === 'undefined') {
			data[channelId] = {};
		}
		data[channelId][key] = value;

		saveData(data);
	}

	/**
	 * Gets value for the given key.
	 *
	 * @param {String} key
	 * @return {*|null}
	 */
	function get(key) {
		var data = getData();

		if (typeof data[channelId] !== 'undefined' && typeof data[channelId][key] !== 'undefined') {
			return data[channelId][key];
		} else {
			return null;
		}
	}

	/**
	 * Drops value for the given key.
	 *
	 * @param {String} key
	 */
	function drop(key) {
		var data = getData();

		if (typeof data[channelId] !== 'undefined' && typeof data[channelId][key] !== 'undefined') {
			delete data[channelId][key];

			if (jQuery.isEmptyObject(data[channelId])) {
				delete data[channelId];
			}
			saveData(data);
		}
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
	this.set = set;
	this.get = get;
	this.drop = drop;
};

// chat init script:
jQuery(window).load(function() {
	jQuery(".wcContainer[data-wc-config]").each(function(index) {
		var config = jQuery(this).data('wc-config');
		
		if (typeof config !== 'object') {
			jQuery(this).prepend('<strong style="color:#f00;">Error: invalid Wise Chat Pro configuration</strong>');
			return;
		}
		
		var messages = jQuery(this).find('.wcMessages');
		if (messages.length > 0 && config.messagesOrder == 'ascending') {
			setTimeout(function() {
                messages.scrollTop(messages[0].scrollHeight);
            }, 100);
		}
		
		wisechat.core.Instance.getInstance(config);
	});
	
	jQuery(".wcContainer[data-wc-pre-config]").each(function(index) {
		var config = jQuery(this).data('wc-pre-config');
		
		if (typeof config !== 'object') {
			jQuery(this).prepend('<strong style="color:#f00;">Error: invalid Wise Chat Pro configuration</strong>');
			return;
		}
		if (!config.sidebarMode) {
			return;
		}
		
		var chatId = config.chatId;
		var channelId = config.channelId;

		wisechat.utils.htmlUtils.adjustTitleToContent(chatId);
		wisechat.utils.htmlUtils.addMinimalizeFeature(chatId, channelId, config.fbBottomOffset, config.fbBottomThreshold);
		if (config.isDefaultTheme) {
			wisechat.utils.htmlUtils.adjustContainerBackgroundColorToParent(chatId);
		}
		wisechat.utils.htmlUtils.adjustBottomOffset(chatId, config.fbBottomOffset, config.fbBottomThreshold);
	});
}); 
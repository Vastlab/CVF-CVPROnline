/**
 * Wise Chat Engines namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.engines = {};

/**
 * AjaxEngine class.
 *
 * @param {Object} options Plugin's global options
 * @constructor
 */
wisechat.engines.AjaxEngine = function(options) {
	var MESSAGES_REFRESH_TIMEOUT = options.messagesRefreshTime;
	var messagesEndpoint = options.apiMessagesEndpointBase + '?action=wise_chat_messages_endpoint';
	var messageEndpoint = options.apiWPEndpointBase + '?action=wise_chat_message_endpoint';
	var messageGetEndpoint = options.apiEndpointBase + '?action=wise_chat_get_message_endpoint';
	var userCommandEndpoint = options.apiWPEndpointBase + '?action=wise_chat_user_command_endpoint';
	var idsCache = {};
	var lastId = options.lastId;
	var isInitialized = false;
	var currentRequest = null;
	var currentPMRequest = null;
	var messagesCallback = function() { };
	var messagesErrorCallback = function() { };
	var debugLoggerCallback = function() { };

	/**
	 * Starts the engine.
	 */
	function initialize() {
		if (isInitialized == true) {
			return;
		}
		isInitialized = true;
		setInterval(checkNewMessages, MESSAGES_REFRESH_TIMEOUT);
	}

	function debug(message) {
		debugLoggerCallback('[wisechat.engines.AjaxEngine] ' + message);
	}

	/**
	 * Makes AJAX request for checking new messages.
	 * It does not make new request if the previous did not complete.
	 */
	function checkNewMessages() {
		if (currentRequest !== null && currentRequest.readyState > 0 && currentRequest.readyState < 4) {
			return;
		}

		var data = {
			channelId: options.channelId,
			lastId: lastId,
			checksum: options.checksum
		};
		if (options.isMultisite === true) {
			data['blogId'] = options.blogId;
		}

		currentRequest = jQuery.ajax({
				type: "GET",
				url: messagesEndpoint,
				data: data
			})
			.done(onNewMessagesArrived)
			.fail(onMessageArrivalError);
	}

	/**
	 * Loads just private messages.
	 */
	function loadPrivateMessages() {
		if (currentPMRequest !== null && currentPMRequest.readyState > 0 && currentPMRequest.readyState < 4) {
			return;
		}

		var data = {
			channelId: options.channelId,
			lastId: 0,
			checksum: options.checksum,
			privateMessages: 1
		};
		if (options.isMultisite === true) {
			data['blogId'] = options.blogId;
		}

		currentPMRequest = jQuery.ajax({
				type: "GET",
				url: messagesEndpoint,
				data: data
			})
			.done(onNewMessagesArrived)
			.fail(onMessageArrivalError);
	}

	/**
	 * Executed when AJAX request completes successfully.
	 *
	 * @param {String} result
	 */
	function onNewMessagesArrived(result) {
		try {
			var response = result;
			if (response.result) {
				var messagesFiltered = [];
				for (var x = 0; x < response.result.length; x++) {
					var msg = response.result[x];
					var messageId = parseInt(msg['id']);
					if (messageId > lastId) {
						lastId = messageId;
					}
					if (!idsCache[messageId]) {
						messagesFiltered.push(msg);
						idsCache[messageId] = true;
					}
				}
				response.result = messagesFiltered;
				messagesCallback(response);
			}
		} catch (e) {
			debug('[onNewMessagesArrived] [result] ' + result);
			debug('[onNewMessagesArrived] [exception] ' + e.toString());

			var errorDetails = '';
			if (jQuery.type(result) === "string") {
				var split = result.split("\n");
				if (split.length > 1) {
					errorDetails = ", " + split[1];
				}
			} else {
				errorDetails = result;
			}
			messagesErrorCallback('Server error: ' + e.toString() + errorDetails);
		}
	}

	/**
	 * Executed when AJAX request completes with an error.
	 *
	 * @param {Object} jqXHR
	 * @param {String} textStatus
	 * @param {Object} errorThrown
	 */
	function onMessageArrivalError(jqXHR, textStatus, errorThrown) {
		// ignore network problems:
		if (typeof(jqXHR.status) != 'undefined' && jqXHR.status == 0) {
			return;
		}

		try {
			var response = jQuery.parseJSON(jqXHR.responseText);
			if (response.error) {
				messagesErrorCallback(response.error);
			}
		} catch (e) {
			debug('[onMessageArrivalError] [responseText] ' + jqXHR.responseText);
			debug('[onMessageArrivalError] [errorThrown] ' + errorThrown);
			messagesErrorCallback('Server error: ' + errorThrown);
		}
	}

	/**
	 * Sends a message using AJAX call. All the listeners must be specified.
	 *
	 * @param {Object} message
	 * @param {Function} successListener
	 * @param {Function} progressListener
	 * @param {Function} errorListener
	 */
	function sendMessage(message, successListener, progressListener, errorListener) {
		if (!jQuery.isFunction(successListener) || !jQuery.isFunction(progressListener) || !jQuery.isFunction(errorListener)) {
			throw new Error('Missing listeners');
		}

		progressListener(0);

		var data = {};
		if (jQuery.isPlainObject(message.customParameters)) {
			data = message.customParameters;
		}
		data['attachments'] = message.attachments;
		data['channelId'] = message.channelId;
		data['message'] = message.content;
		data['checksum'] = message.checksum;
		jQuery.ajax({
				type: "POST",
				url: messageEndpoint,
				data: data,
				progressUpload: function(evt) {
					if (evt.lengthComputable) {
						var percent = parseInt(evt.loaded / evt.total * 100);
						if (percent > 100) {
							percent = 100;
						}

						progressListener(percent);
					}
				}
			})
			.done(function(result) {
				try {
					var response = result;
					if (response.error) {
						errorListener(response.error);
					} else {
						successListener(response);
						if (options.allowToReceiveMessages) {
							checkNewMessages();
						}
					}
				} catch (e) {
					debug('[onMessageSent] [result] ' + result);
					debug('[onMessageSent] [exception] ' + e.toString());
					errorListener('Unknown error: ' + e.toString());
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				if (typeof(jqXHR.status) != 'undefined' && jqXHR.status == 0) {
					errorListener('No network connection');
					return;
				}

				try {
					var response = jQuery.parseJSON(jqXHR.responseText);
					if (response.error) {
						errorListener(response.error);
					} else {
						errorListener('Unknown server error occurred: ' + errorThrown);
					}
				} catch (e) {
					debug('[onMessageSentError] [responseText] ' + jqXHR.responseText);
					debug('[onMessageSentError] [errorThrown] ' + errorThrown);
					debug('[onMessageSentError] [exception] ' + e.toString());
					errorListener('Server error: ' + errorThrown);
				}
			});
	}

	/**
	 * Gets a message using AJAX call. All the listeners must be specified.
	 *
	 * @param {Object} message
	 * @param {Function} successListener
	 * @param {Function} errorListener
	 */
	function getMessage(message, successListener, errorListener) {
		if (!jQuery.isFunction(successListener) || !jQuery.isFunction(errorListener)) {
			throw new Error('Missing listeners');
		}

		jQuery.ajax({
				type: "GET",
				url: messageGetEndpoint,
				data: {
					channelId: message.channelId,
					messageId: message.messageId,
					checksum: message.checksum
				}
			})
			.done(function(result) {
				try {
					var response = result;
					if (response.error) {
						errorListener(response.error);
					} else {
						successListener(response);
					}
				} catch (e) {
					debug('[onMessageGet] [result] ' + result);
					debug('[onMessageGet] [exception] ' + e.toString());
					errorListener('Unknown error: ' + e.toString());
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				if (typeof(jqXHR.status) != 'undefined' && jqXHR.status == 0) {
					errorListener('No network connection');
					return;
				}

				try {
					var response = jQuery.parseJSON(jqXHR.responseText);
					if (response.error) {
						errorListener(response.error);
					} else {
						errorListener('Unknown server error occurred: ' + errorThrown);
					}
				} catch (e) {
					debug('[onMessageGet] [responseText] ' + jqXHR.responseText);
					debug('[onMessageGet] [errorThrown] ' + errorThrown);
					debug('[onMessageGet] [exception] ' + e.toString());
					errorListener('Server error: ' + errorThrown);
				}
			});
	}

	/**
	 * Sends user command.
	 *
	 * @param {String} command
	 * @param {Object} parameters
	 * @param {Function} successListener
	 * @param {Function} errorListener
	 */
	function sendUserCommand(command, parameters, successListener, errorListener) {
		if (!jQuery.isFunction(successListener) || !jQuery.isFunction(errorListener)) {
			throw new Error('Missing listeners');
		}

		parameters['command'] = command;
		parameters['channelId'] = options.channelId;
		parameters['checksum'] = options.checksum;

		jQuery.ajax({
				type: "POST",
				url: userCommandEndpoint,
				data: parameters
			})
			.done(function(result) {
				try {
					var response = result;
					if (response && response.error) {
						errorListener(response.error);
					} else {
						successListener(response);
					}
				} catch (e) {
					debug('[sendUserCommand] [result] ' + result);
					debug('[sendUserCommand] [exception] ' + e.toString());
					errorListener('Unknown error: ' + e.toString());
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				if (typeof(jqXHR.status) != 'undefined' && jqXHR.status == 0) {
					errorListener('No network connection');
					return;
				}

				try {
					var response = jQuery.parseJSON(jqXHR.responseText);
					if (response && typeof response.error !== 'undefined') {
						errorListener(response.error);
					} else {
						errorListener('Unknown server error occurred: ' + errorThrown);
					}
				} catch (e) {
					debug('[sendUserCommand] [responseText] ' + jqXHR.responseText);
					debug('[sendUserCommand] [errorThrown] ' + errorThrown);
					debug('[sendUserCommand] [exception] ' + e.toString());
					errorListener('Server error: ' + errorThrown);
				}
			});
	}

	function setMessagesCallback(callback) {
		messagesCallback = callback;
	}

	function setMessagesErrorCallback(callback) {
		messagesErrorCallback = callback;
	}

	function setDebugLoggerCallback(callback) {
		debugLoggerCallback = callback;
	}

	// API:
	this.initialize = initialize;
	this.loadPrivateMessages = loadPrivateMessages;
	this.sendMessage = sendMessage;
	this.getMessage = getMessage;
	this.setMessagesCallback = setMessagesCallback;
	this.setMessagesErrorCallback = setMessagesErrorCallback;
	this.setDebugLoggerCallback = setDebugLoggerCallback;
	this.sendUserCommand = sendUserCommand;
};
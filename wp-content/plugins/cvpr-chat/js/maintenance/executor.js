/**
 * CVPR Chat maintenance services.
 *
 * @author Kainex <contact@kaine.pl>
 */
function CVPRChatMaintenanceExecutor(options, CvprChatMessages, notifier) {
	var REFRESH_TIMEOUT = 20000;
	var ENDPOINT_URL = options.apiEndpointBase + '?action=Cvpr_chat_maintenance_endpoint';
	var lastActionId = options.lastActionId;
	var isInitialized = false;
	var request = null;
	var actionsIdsCache = {};
	
	function initialize() {
		if (isInitialized == true) {
			return;
		}
		isInitialized = true;
		performMaintenanceRequest();
		setInterval(performMaintenanceRequest, REFRESH_TIMEOUT);
	};
	
	function isRequestStillRunning() {
		return request !== null && request.readyState > 0 && request.readyState < 4;
	}

	function onMaintenanceRequestError(jqXHR, textStatus, errorThrown) {
        // network problems ignore:
        if (typeof(jqXHR.status) != 'undefined' && jqXHR.status == 0) {
            return;
        }

		try {
			var response = jQuery.parseJSON(jqXHR.responseText);
			if (response.error) {
				CvprChatMessages.showErrorMessage('Maintenance error: ' + response.error);
			} else {
				CvprChatMessages.showErrorMessage('Unknown maintenance error: ' + errorThrown);
			}
		}
		catch (e) {
            CvprChatMessages.logDebug('[onMaintenanceRequestError] ' + jqXHR.responseText);
            CvprChatMessages.logDebug('[errorThrown] ' + errorThrown);
			CvprChatMessages.showErrorMessage('Maintenance fatal error: ' + errorThrown);
		}
	};
	
	function performMaintenanceRequest() {
		if (isRequestStillRunning()) {
			return;
		}
		
		request = jQuery.ajax({
			url: ENDPOINT_URL,
			data: {
				lastActionId: lastActionId,
                channelId: options.channelId,
				checksum: options.checksum
			}
		})
		.done(analyzeResponse)
		.fail(onMaintenanceRequestError);
	};
	
	function analyzeResponse(data) {
		try {
			var maintenance = data;
			
			if (typeof(maintenance.actions) !== 'undefined') {
				executeActions(maintenance.actions);
			}
			if (typeof(maintenance.events) !== 'undefined') {
				handleEvents(maintenance.events);
			}
			if (typeof(maintenance.error) !== 'undefined') {
				CvprChatMessages.showErrorMessage('Maintenance error occurred: ' + maintenance.error);
			}
		}
		catch (e) {
            CvprChatMessages.logDebug('[analyzeResponse] ' + data);
			CvprChatMessages.showErrorMessage('Maintenance corrupted data: ' + e.message);
		}
	};
	
	function executeActions(actions) {
		for (var x = 0; x < actions.length; x++) {
			var action = actions[x];
			var actionId = action.id;
			var commandName = action.command.name;
			var commandData = action.command.data;
			if (actionId > lastActionId) {
				lastActionId = actionId;
			}
			
			if (!actionsIdsCache[actionId]) {
				actionsIdsCache[actionId] = true;
				
				switch (commandName) {
					case 'deleteMessage':
						CvprChatMessages.hideMessage(commandData.id);
						break;
					case 'deleteMessages':
						deleteMessagesAction(commandData);
						break;
					case 'deleteAllMessagesFromChannel':
						if (commandData.channelId == options.channelId) {
							CvprChatMessages.hideAllMessages();
						}
						break;
					case 'deleteAllMessages':
						CvprChatMessages.hideAllMessages();
						break;
					case 'replaceUserNameInMessages':
						CvprChatMessages.replaceUserNameInMessages(commandData.renderedUserName, commandData.messagesIds);
						break;
					case 'refreshPlainUserName':
						CvprChatMessages.refreshPlainUserName(commandData.name);
						break;
					case 'showErrorMessage':
						CvprChatMessages.showErrorMessage(commandData.message);
						break;
					case 'setMessagesProperty':
						CvprChatMessages.setMessagesProperty(commandData);
						break;
					case 'reload':
						if (typeof location.reload !== 'undefined') {
							location.reload();
						}
						break;
				}
			}
		}
	};
	
	function handleEvents(events) {
		for (var x = 0; x < events.length; x++) {
			var event = events[x];
			var eventData = event.data;
			
			switch (event.name) {
				case 'refreshUsersList':
					CvprChatMessages.refreshUsersList(eventData);
					break;
				case 'refreshUsersCounter':
					CvprChatMessages.refreshUsersCounter(eventData);
					break;
				case 'userData':
					options.userData = eventData;
					break;
				case 'checkSum':
					if (options.checksum !== null) {
						options.checksum = eventData;
					}
					break;
				case 'reportAbsentUsers':
					if (jQuery.isArray(eventData.users) && eventData.users.length > 0) {
						if (options.enableLeaveNotification) {
							for (var y = 0; y < eventData.users.length; y++) {
								var user = eventData.users[y];
								CvprChatMessages.showPlainMessage(user.name + ' ' + options.messages.messageHasLeftTheChannel);
							}
						}
						if (options.leaveSoundNotification && eventData.users.length > 0) {
							notifier.sendNotificationForEvent('userLeft');
						}
					}
					break;
				case 'reportNewUsers':
					if (jQuery.isArray(eventData.users) && eventData.users.length > 0) {
						if (options.enableJoinNotification) {
							for (var y = 0; y < eventData.users.length; y++) {
								var user = eventData.users[y];
								CvprChatMessages.showPlainMessage(user.name + ' ' + options.messages.messageHasJoinedTheChannel);
							}
						}
						if (options.joinSoundNotification && eventData.users.length > 0) {
							notifier.sendNotificationForEvent('userJoined');
						}
					}
					break;
			}
		}
	};
	
	function deleteMessagesAction(data) {
		for (var x = 0; x < data.ids.length; x++) {
			CvprChatMessages.hideMessage(data.ids[x]);
		}
	};
	
	// public API:
	this.start = initialize;
};
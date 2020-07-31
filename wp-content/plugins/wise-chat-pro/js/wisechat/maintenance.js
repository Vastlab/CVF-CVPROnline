/**
 * Wise Chat Maintenance namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.maintenance = {};

/**
 * MaintenanceExecutor class.
 *
 * @param {Object} options
 * @param {wisechat.ui.VisualLogger} logger
 * @constructor
 */
wisechat.maintenance.MaintenanceExecutor = function(options, logger) {
	var $this = jQuery(this);
	var REFRESH_TIMEOUT = 20000;
	var ENDPOINT_URL = options.apiWPEndpointBase + '?action=wise_chat_maintenance_endpoint';
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
	}

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
				logger.logError('Maintenance error: ' + response.error);
			} else {
				logger.logError('Unknown maintenance error: ' + errorThrown);
			}
		} catch (e) {
			debug('[onMaintenanceRequestError] [responseText] ' + jqXHR.responseText);
			debug('[onMaintenanceRequestError] [errorThrown] ' + errorThrown);
			debug('[onMaintenanceRequestError] [exception] ' + e.message);
			logger.logError('Maintenance fatal error: ' + errorThrown);
		}
	}

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
	}

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
				logger.logError('Maintenance error occurred: ' + maintenance.error);
			}
		} catch (e) {
			debug('[analyzeResponse] [data] ' + data);
			debug('[analyzeResponse] [exception] ' + e.message);
			logger.logError('Maintenance error: corrupted data');
		}
	}

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
				$this.trigger(commandName, [commandData]);
			}
		}
	}

	function handleEvents(events) {
		for (var x = 0; x < events.length; x++) {
			var event = events[x];
			$this.trigger(event.name, [event.data]);
		}
	}

	function debug(message) {
		logger.logDebug('[wisechat.maintenance.MaintenanceExecutor] ' + message);
	}

	// public API:
	this.$ = $this;
	this.start = initialize;
};
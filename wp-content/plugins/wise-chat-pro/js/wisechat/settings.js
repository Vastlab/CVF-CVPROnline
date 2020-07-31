/**
 * Wise Chat Settings namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.settings = {};

/**
 * Settings class.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} container Container element for displaying messages
 * @param {wisechat.ui.VisualLogger} logger
 * @constructor
 */
wisechat.settings.Settings = function(options, container, logger) {
	var $this = jQuery(this);
	var settingsEndpoint = options.apiWPEndpointBase + '?action=wise_chat_settings_endpoint';
	var customizationsSection = container.find('.wcCustomizations');
	var customizeButton = container.find('a.wcCustomizeButton');
	var customizationsPanel = container.find('.wcCustomizationsPanel');
	var userNameInput = container.find('.wcCustomizationsPanel input.wcUserName');
	var userNameApproveButton = container.find('.wcCustomizationsPanel input.wcUserNameApprove');
	var muteSoundCheckbox = container.find('.wcCustomizationsPanel input.wcMuteSound');
	var enableNotificationsCheckbox = container.find('.wcCustomizationsPanel input.wcEnableNotifications');
	var textColorInput = container.find('.wcCustomizationsPanel input.wcTextColor');
	var textColorResetButton = container.find('.wcCustomizationsPanel input.wcTextColorReset');

	/**
	 * Saves given property on the server side using AJAX call.
	 *
	 * @param {String} propertyName
	 * @param {String} propertyValue
	 * @param {Function} successCallback
	 * @param {Function} errorCallback
	 */
	function saveProperty(propertyName, propertyValue, successCallback, errorCallback) {
		jQuery.ajax({
				type: "POST",
				url: settingsEndpoint,
				data: {
					property: propertyName,
					value: propertyValue,
					channelId: options.channelId,
					checksum: options.checksum
				}
			})
			.done(function(result) {
				onPropertySaveRequestSuccess(result, successCallback);
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[wisechat.settings.Settings] [saveProperty] [responseText] ' + jqXHR.responseText);
				onPropertySaveRequestError(jqXHR, textStatus, errorThrown, errorCallback);
			});
	}

	/**
	 * Processes AJAX success response.
	 *
	 * @param {String} result
	 * @param {Function} callback
	 */
	function onPropertySaveRequestSuccess(result, callback) {
		try {
			var response = result;
			if (response.error) {
				logger.logError(response.error);
			} else {
				if (typeof(callback) !== 'undefined') {
					callback.apply(this, [response]);
				}
			}
		} catch (e) {
			logger.logDebug('[wisechat.settings.Settings] [onPropertySaveRequestSuccess] [result] ' + result);
			logger.logDebug('[wisechat.settings.Settings] [onPropertySaveRequestSuccess] [exception] ' + e.toString());
			logger.logError('Settings error: ' + e.toString());
		}
	}

	/**
	 * Processes AJAX error response.
	 *
	 * @param {Object} jqXHR
	 * @param {String} textStatus
	 * @param {String} errorThrown
	 * @param {Function} callback
	 */
	function onPropertySaveRequestError(jqXHR, textStatus, errorThrown, callback) {
		try {
			var response = jQuery.parseJSON(jqXHR.responseText);
			if (response.error) {
				logger.logError(response.error);
			} else {
				logger.logError('Settings error: ' + errorThrown);
			}
		} catch (e) {
			logger.logDebug('[wisechat.settings.Settings] [onPropertySaveRequestError] [responseText] ' + jqXHR.responseText);
			logger.logDebug('[wisechat.settings.Settings] [onPropertySaveRequestError] [errorThrown] ' + errorThrown);
			logger.logDebug('[wisechat.settings.Settings] [onPropertySaveRequestError] [exception] ' + e.toString());
			logger.logError('Fatal error: ' + errorThrown);
		}

		if (typeof(callback) != 'undefined') {
			callback.apply(this, [errorThrown]);
		}
	}

	function onUserNameApproveButtonClick(e) {
		var userNameInputElement = userNameInput[0];
		if (typeof (userNameInputElement.checkValidity) == 'function') {
			userNameInputElement.checkValidity();
		}

		var userName = userNameInput.val().replace(/^\s+|\s+$/g, '');
		if (userName.length > 0) {
			saveProperty('userName', userName, function(response) {
				$this.trigger('userNameChange', [response.value]);
				hide();
			});
		}
	}

	function onMuteSoundCheckboxChange(e) {
		saveProperty('muteSounds', muteSoundCheckbox.is(':checked'), function(response) {
			options.userSettings.muteSounds = muteSoundCheckbox.is(':checked');
			hide();
		});
	}

	function onEnableNotificationsCheckboxChange(e) {
		saveProperty('disableNotifications', !enableNotificationsCheckbox.is(':checked'), function(response) {
			hide();
		});
	}

	function onTextColorChange(id, value) {
		saveProperty('textColor', value, function(response) {
			options.userSettings.textColor = value != 'null' ? value : '';
			hide();
		});
	}

	function onTextColorResetButtonClick(e) {
		onTextColorChange('', 'null');
		textColorInput.parent().find('.colorPicker-picker').css({
			backgroundColor: textColorInput.parent().css('color')
		})
	}

	function getContainer() {
		return customizationsSection;
	}

	function show() {
		customizationsPanel.fadeIn(400, function() {
			customizationsSection.addClass('wcCustomizationsOpen');
			$this.trigger('show', []);
		});

	}

	function hide() {
		customizationsPanel.fadeOut(400, function() {
			customizationsSection.removeClass('wcCustomizationsOpen');
			$this.trigger('hide', []);
		});
	}

	// DOM events:
	customizeButton.click(function(e) {
		e.preventDefault();

		if (customizationsPanel.is(":visible")) {
			hide();
		} else {
			show();
		}
	});
	userNameApproveButton.click(onUserNameApproveButtonClick);
	muteSoundCheckbox.change(onMuteSoundCheckboxChange);
	enableNotificationsCheckbox.change(onEnableNotificationsCheckboxChange);

	if (typeof textColorInput.colorPicker != 'undefined') {
		var colorsPalette = [
			'330000', '331900', '333300', '193300', '003300', '003319', '003333', '001933',
			'000033', '190033', '330033', '330019', '000000', '660000', '663300', '666600', '336600',
			'006600', '006633', '006666', '003366', '000066', '330066', '660066', '660033', '202020',
			'990000', '994c00', '999900', '4c9900', '009900', '00994c', '009999', '004c99', '000099',
			'4c0099', '990099', '99004c', '404040', 'cc0000', 'cc6600', 'cccc00', '66cc00', '00cc00',
			'00cc66', '00cccc', '0066cc', '0000cc', '6600cc', 'cc00cc', 'cc0066', '606060', 'ff0000',
			'ff8000', 'ffff00', '80ff00', '00ff00', '00ff80', '00ffff', '0080ff', '0000ff', '7f00ff',
			'ff00ff', 'ff007f', '808080', 'ff3333', 'ff9933', 'ffff33', '99ff33', '33ff33', '33ff99',
			'33ffff', '3399ff', '3333ff', '9933ff', 'ff33ff', 'ff3399', 'a0a0a0', 'ff6666', 'ffb266',
			'ffff66', 'b2ff66', '66ff66', '66ffb2', '66ffff', '66b2ff', '6666ff', 'b266ff', 'ff66ff',
			'ff66b2', 'c0c0c0', 'ff9999', 'ffcc99', 'ffff99', 'ccff99', '99ff99', '99ffcc', '99ffff',
			'99ccff', '9999ff', 'cc99ff', 'ff99ff', 'ff99cc', 'e0e0e0', 'ffcccc', 'ffe5cc', 'ffffcc',
			'e5ffcc', 'ccffcc', 'ccffe5', 'ccffff', 'cce5ff', 'ccccff', 'e5ccff', 'ffccff', 'ffcce5',
			'ffffff'
		];
		var defaultColor = options.userSettings.textColor;
		if (typeof defaultColor == "undefined" || defaultColor.length == 0) {
			defaultColor = textColorInput.parent().css('color');
		}

		textColorInput.colorPicker({
			pickerDefault: defaultColor,
			colors: colorsPalette,
			showHexField: false,
			onColorChange: onTextColorChange
		});
		textColorResetButton.click(onTextColorResetButtonClick);
	}

	// fixing palette location:
	if (typeof jQuery.fn.colorPicker !== 'undefined') {
		jQuery.extend(true, jQuery.fn.colorPicker, {
			_superA: jQuery.fn.colorPicker.showPalette,
			showPalette: function (palette) {
				this._superA(palette);

				palette.css({
					top: palette.offset().top - (palette.outerHeight()) - 20,
					left: palette.offset().left - (2 * palette.outerWidth() / 3)
				});
			}
		});
	}

	// public API:
	this.$ = $this;
	this.getContainer = getContainer;
};
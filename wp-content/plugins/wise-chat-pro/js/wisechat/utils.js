/**
 * Wise Chat Utilities namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.utils = wisechat.utils || {};

/**
 * Messages history utility.
 * It uses Local Storage to store and retrieve historical chat messages typed by the current user.
 *
 * @constructor
 */
wisechat.utils.MessagesHistory = function() {
	var LOCAL_STORAGE_KEY_MESSAGES_KEY = "WiseChatMessagesStack";
	var pointer = -1;
	var size = 50;

	this.getPointer = function() {
		return pointer;
	};

	this.resetPointer = function() {
		pointer = -1;
	};

	this.getCurrentSize = function() {
		var stack = getStack();

		if (stack !== null) {
			return stack.length;
		}

		return 0;
	};

	this.addMessage = function(message) {
		var stack = getStack();
		if (stack == null || typeof(stack) === "undefined") {
			stack = new Array();
		}
		if (stack.length >= size) {
			stack.shift();
		}
		stack.push(message);

		setStack(stack);
	};

	this.getPreviousMessage = function() {
		var stack = getStack();

		if (stack != null) {
			if (pointer < size - 1 && pointer < this.getCurrentSize() - 1) {
				pointer++;
			}

			var message = stack[(stack.length - 1) - pointer];

			return message;
		}

		return null;
	};

	this.getNextMessage = function() {
		var stack = getStack();

		if (stack != null) {
			if (pointer > 0) {
				pointer--;
			}

			var message = stack[(stack.length - 1) - pointer];

			return message;
		}

		return null;
	};

	function getStack() {
		if (typeof(Storage) !== "undefined") {
			var stack = window.localStorage.getItem(LOCAL_STORAGE_KEY_MESSAGES_KEY);

			if (stack != null) {
				return JSON.parse(stack);
			}
		}

		return null;
	};

	function setStack(stack) {
		if (typeof(Storage) !== "undefined") {
			window.localStorage.setItem(LOCAL_STORAGE_KEY_MESSAGES_KEY, JSON.stringify(stack));
		}
	};
};
wisechat.utils.messagesHistory = new wisechat.utils.MessagesHistory();

/**
 * Wise Chat Pro options class.
 *
 * @param {Object} options Plugin's global options as pain object
 * @constructor
 */
wisechat.utils.Options = function(options) {

	function getPlain() {
		return options;
	}

	function getEffectiveChatHeightBasedOnPercentageHeight() {
		var effectiveChatHeight = null;

		if (options.chatHeight.match(/%$/)) {
			var percentage = parseInt(options.chatHeight.replace(/%$/, ''));
			if (!isNaN(percentage)) {
				effectiveChatHeight = (jQuery(window).height() - options.fbBottomOffset - options.fbUsersListTopOffset) * (percentage / 100);
				if (effectiveChatHeight < 200) {
					effectiveChatHeight = 200;
				}
			}
		}

		return effectiveChatHeight;
	}

	// public API:
	this.getPlain = getPlain;
	this.getEffectiveChatHeightBasedOnPercentageHeight = getEffectiveChatHeightBasedOnPercentageHeight;
};

/**
 * HTML utility.
 *
 * @constructor
 */
wisechat.utils.HtmlUtils = function() {
	function decodeEntities(encodedString) {
		var textArea = document.createElement('textarea');
		textArea.innerHTML = encodedString;

		return textArea.value;
	}

	function getAncestorBackgroundColor(element) {
		function isTransparent(bgcolor){
			return (bgcolor == "transparent" || bgcolor.substring(0,4) == "rgba");
		}

		var bgColor = element.css('background-color');
		if (isTransparent(bgColor)) {
			element.parents().each(function() {
				if (!isTransparent(jQuery(this).css('background-color'))){
					bgColor = jQuery(this).css('background-color');
					return false;
				}
			});
		}

		return bgColor;
	}

	function adjustTitleToContent(chatId) {
		var content = jQuery('#' + chatId + ' .wcWindowContent');
		jQuery('#' + chatId + ' .wcWindowTitle').css({
			bottom: content.outerHeight() > 0 ? content.outerHeight() : 0,
			width: content.outerWidth(),
			right: 0
		});
	}

	function adjustContainerBackgroundColorToParent(chatId) {
		var container = jQuery('#' + chatId + ' > *');
		container.css('background-color', wisechat.utils.htmlUtils.getAncestorBackgroundColor(container));
	}

	function adjustBottomOffset(chatId, bottomOffset, bottomOffsetThreshold) {
		if (bottomOffset > 0) {
			if (bottomOffsetThreshold > 0) {
				if (jQuery(window).width() > bottomOffsetThreshold) {
					bottomOffset = 0;
				}
			}

			var content = jQuery('#' + chatId + ' .wcWindowContent');
			content.css('bottom', bottomOffset);
			jQuery('#' + chatId + ' .wcWindowTitle').css(
				'bottom', bottomOffset + content.outerHeight()
			);
		}
	}

	function addMinimalizeFeature(chatId, channelId, bottomOffset, bottomOffsetThreshold) {
		var container = jQuery('#' + chatId);
		var content = jQuery('#' + chatId + ' .wcWindowContent');
		var title = jQuery('#' + chatId + ' .wcWindowTitle');
		var localSettings = new wisechat.core.LocalSettings(channelId);

		jQuery(window).resize(function () {
			container.toggleClass('wcWidth600', jQuery(window).width() < 600);
			wisechat.utils.htmlUtils.adjustTitleToContent(chatId);
			wisechat.utils.htmlUtils.adjustBottomOffset(chatId, bottomOffset, bottomOffsetThreshold);
		}).trigger('resize');

		if (localSettings.get('channelWindowMinimized') === true) {
			content.addClass('wcInvisible');
			title.find('.wcWindowTitleMinMaxLink').addClass('wcWindowTitleMinimized');
		}

		title.find('.wcWindowTitleMinMaxLink').click(function(event) {
			if (!content.hasClass('wcInvisible')) {
				localSettings.set('channelWindowMinimized', true);
				content.addClass('wcInvisible');
				jQuery(this).addClass('wcWindowTitleMinimized');
			} else {
				localSettings.set('channelWindowMinimized', false);
				content.removeClass('wcInvisible');
				jQuery(this).removeClass('wcWindowTitleMinimized');
			}
			wisechat.utils.htmlUtils.adjustTitleToContent(chatId);
			wisechat.utils.htmlUtils.adjustBottomOffset(chatId, bottomOffset, bottomOffsetThreshold);
		});

		wisechat.utils.htmlUtils.adjustTitleToContent(chatId);
		wisechat.utils.htmlUtils.adjustBottomOffset(chatId, bottomOffset, bottomOffsetThreshold);

		container.css({
			width: 0, padding: '0', margin: 0, height: 0, border: 'none'
		});
	}

	// public API:
	this.decodeEntities = decodeEntities;
	this.getAncestorBackgroundColor = getAncestorBackgroundColor;
	this.adjustTitleToContent = adjustTitleToContent;
	this.adjustContainerBackgroundColorToParent = adjustContainerBackgroundColorToParent;
	this.addMinimalizeFeature = addMinimalizeFeature;
	this.adjustBottomOffset = adjustBottomOffset;
};
wisechat.utils.htmlUtils = new wisechat.utils.HtmlUtils();

/**
 * DateFormatter class. Formats dates given in UTC format.
 * @constructor
 */
wisechat.utils.DateFormatter = function() {

	function makeLeadZero(number) {
		return (number < 10 ? '0' : '') + number;
	}

	/**
	 * Parses date given in ISO format.
	 *
	 * @param {String} isoDate Date in ISO format
	 *
	 * @return {Date} Parsed date
	 */
	function parseISODate(isoDate) {
		var s = isoDate.split(/\D/);

		return new Date(Date.UTC(s[0], --s[1]||'', s[2]||'', s[3]||'', s[4]||'', s[5]||'', s[6]||''))
	}

	/**
	 * Determines whether two dates have equal day, month and year.
	 *
	 * @param {Date} firstDate
	 * @param {Date} secondDate
	 *
	 * @return {Boolean}
	 */
	function isSameDate(firstDate, secondDate) {
		var dateFormatStr = 'Y-m-d';

		return formatDate(firstDate, dateFormatStr) == formatDate(secondDate, dateFormatStr);
	}

	/**
	 * Returns formatted date.
	 *
	 * @param {Date} date Date to format as a string
	 * @param {String} format Desired date format
	 *
	 * @return {String} Formatted date
	 */
	function formatDate(date, format) {
		format = format.replace(/Y/, date.getFullYear());
		format = format.replace(/m/, makeLeadZero(date.getMonth() + 1));
		format = format.replace(/d/, makeLeadZero(date.getDate()));
		format = format.replace(/H/, makeLeadZero(date.getHours()));
		format = format.replace(/i/, makeLeadZero(date.getMinutes()));

		return format;
	}

	/**
	 * Returns localized time without seconds.
	 *
	 * @param {Date} date Date to format as a string
	 *
	 * @return {String} Localized time
	 */
	function getLocalizedTime(date) {
		if (typeof (date.toLocaleTimeString) != "undefined") {
			var timeLocale = date.toLocaleTimeString();
			if ((timeLocale.match(/:/g) || []).length == 2) {
				timeLocale = timeLocale.replace(/:\d\d$/, '');
				timeLocale = timeLocale.replace(/:\d\d /, ' ');
				timeLocale = timeLocale.replace(/[A-Z]{2,4}\-\d{1,2}/, '');
				timeLocale = timeLocale.replace(/[A-Z]{2,4}/, '');
			}

			return timeLocale;
		} else {
			return formatDate(date, 'H:i');
		}
	}

	/**
	 * Returns localized date.
	 *
	 * @param {Date} date Date to format as a string
	 *
	 * @return {String} Localized date
	 */
	function getLocalizedDate(date) {
		if (typeof (date.toLocaleDateString) != "undefined") {
			return date.toLocaleDateString();
		} else {
			return formatDate(date, 'Y-m-d');
		}
	}

	// public API:
	this.formatDate = formatDate;
	this.parseISODate = parseISODate;
	this.isSameDate = isSameDate;
	this.getLocalizedTime = getLocalizedTime;
	this.getLocalizedDate = getLocalizedDate;
};
wisechat.utils.dateFormatter = new wisechat.utils.DateFormatter();

/**
 * ImageViewer class. Provides simple images viewer.
 * @constructor
 */
wisechat.utils.ImageViewer = function() {
	var HOURGLASS_ICON = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wQEDB4ktAYXpwAAAb5JREFUSMe1lr9qFFEUh78rg8gWW1ikSLEWgkVq2SoYsbBIk1dYEAsxaJt3sLAIFkEEX0FSRlgMhKAPkEIQwZDChATSBLMQP5uz4bKZmZ3ZxR+cYs75nT9z7rlnJpFBfQC8B24xG/4Cz1NK38eKYoKwADxiPiwA1wnSpFUdAO+A+y0D/wBeppQ+5sqihHgAdIBRSumsSWT1bvgcNCF31Et1tWnp6mr4dCZtNw4zpXQB7AJrLdqzBuyGb6OKBuq52m3A7QZ3UGZPVW0CfgJvgc/As4r4H4CnwGvgXkrpDy36uh6VPVRPvYnTsJ2r662HWS3U/ZDH6kkW/CR0Y3sx041Re+qh+kXtq59C+qE7VHt1MWpXQkrpF7ACdIFhZhqGbiU4syX474gWHUU7FjP9YuiOprVo2iF/jUO8U3Hj94NTzJLgVYxgL0v4JqTI3rD9mEZ1v9WN7Hk7G9Pt8d5RN4LbaZPgelWE7JVctL3MXrkqqhLsqFvqbXVoNYbB2VJ32rTnMlbwptOxWbeuyxL0w/GJetUgwVVwVfuT8crGawm4AEbAi4ZdHYXPEvCtrvpl58dy3Rscx9dsnt+W41zxD60+eUN8VNiNAAAAAElFTkSuQmCC";

	var container = jQuery('body');
	var imagePreview = container.find('.wcImagePreview');
	if (imagePreview.length === 0) {
		container.append('<div class="wcImagePreview wcHide"> </div>');
		imagePreview = container.find('.wcImagePreview');
	}

	function show(imageSource) {
		clearRemnants();

		addAndShowHourGlass();

		var imageElement = jQuery('<img style="display:none;" />');
		imageElement.on('load', function() {
			removeHourGlass();
			var image = jQuery(this);

			image.show();
		});
		imageElement.attr('src', imageSource);
		imageElement.appendTo(imagePreview);
		imageElement.click(hide);
	}

	function hide() {
		clearRemnants();
		imagePreview.addClass('wcHide');
		imagePreview.removeClass('wcShow');
		jQuery('body').removeClass('wcScrollOff');
	}

	function clearRemnants() {
		imagePreview.find('img').remove();
	}

	function addAndShowHourGlass() {
		var imageElement = jQuery('<img class="wcHourGlass" />');

		imageElement.attr('src', HOURGLASS_ICON);
		imageElement.appendTo(imagePreview);
		
		imagePreview.removeClass('wcHide');
		imagePreview.addClass('wcShow');
		jQuery('body').addClass('wcScrollOff');
	}

	function removeHourGlass() {
		container.find('.wcHourGlass').remove();
	}

	// DOM events:
	imagePreview.click(hide);

	// public API:
	this.show = show;
	this.hide = hide;
};
wisechat.utils.imageViewer = new wisechat.utils.ImageViewer();

/**
 * Notifier class. Provides window title and sound notifiers.
 *
 * @param {Object} options Plugin's global options
 * @constructor
 */
wisechat.utils.Notifier = function(options) {
	var isWindowFocused = true;
	var isTitleNotificationVisible = false;
	var rawTitle = document.title;
	var notificationNumber = 0;
	var newMessageSoundNotification = null;
	var userLeftSoundNotification = null;
	var userJoinedSoundNotification = null;
	var userMentioningSoundNotification = null;

	function initializeSoundFeatures(soundFile, eventID) {
		if (soundFile != null && soundFile.length > 0) {
			var elementId = 'wcMessagesNotificationAudio' + eventID;
			var soundNotificationElement = jQuery('#' + elementId);
			if (soundNotificationElement.length > 0) {
				return soundNotificationElement;
			}

			var soundFileURLWav = options.baseDir + 'sounds/' + soundFile + '.wav';
			var soundFileURLMp3 = options.baseDir + 'sounds/' + soundFile + '.mp3';
			var soundFileURLOgg = options.baseDir + 'sounds/' + soundFile + '.ogg';
			var container = jQuery('body');

			container.append(
				'<audio id="' + elementId + '" preload="auto">' +
				'<source src="' + soundFileURLWav + '" type="audio/x-wav" />' +
				'<source src="' + soundFileURLOgg + '" type="audio/ogg" />' +
				'<source src="' + soundFileURLMp3 + '" type="audio/mpeg" />' +
				'</audio>'
			);

			return jQuery('#' + elementId);
		}

		return null;
	}

	function showTitleNotification() {
		if (!isTitleNotificationVisible) {
			isTitleNotificationVisible = true;
			rawTitle = document.title;
		}
		notificationNumber++;
		document.title = '(' + notificationNumber + ') (!) ' + rawTitle;
		setTimeout(function() { showTitleNotificationAnimStep1(); }, 1500);
	}

	function showTitleNotificationAnimStep1() {
		if (isTitleNotificationVisible) {
			document.title = '(' + notificationNumber + ') ' + rawTitle;
		}
	}

	function hideTitleNotification() {
		if (isTitleNotificationVisible) {
			document.title = rawTitle;
			isTitleNotificationVisible = false;
			notificationNumber = 0;
		}
	}

	function onWindowBlur() {
		isWindowFocused = false;
	}

	function onWindowFocus() {
		isWindowFocused = true;
		hideTitleNotification();
	}

	function sendNotifications() {
		if (options.enableTitleNotifications && !isWindowFocused) {
			showTitleNotification();
		}
		if (!options.userSettings.muteSounds) {
			if (newMessageSoundNotification !== null && newMessageSoundNotification[0].play) {
				newMessageSoundNotification[0].play();
			}
		}
	}

	function sendNotificationForEvent(eventName) {
		if (!options.userSettings.muteSounds) {
			if (eventName == 'userLeft') {
				if (userLeftSoundNotification !== null && userLeftSoundNotification[0].play) {
					userLeftSoundNotification[0].play();
				}
			} else if (eventName == 'userJoined') {
				if (userJoinedSoundNotification !== null && userJoinedSoundNotification[0].play) {
					userJoinedSoundNotification[0].play();
				}
			} else if (eventName == 'userMentioning') {
				if (userMentioningSoundNotification !== null && userMentioningSoundNotification[0].play) {
					userMentioningSoundNotification[0].play();
				}
			}
		}
	}

	// start-up actions:
	newMessageSoundNotification = initializeSoundFeatures(options.soundNotification, 'NewMessage');
	userLeftSoundNotification = initializeSoundFeatures(options.leaveSoundNotification, 'UserLeft');
	userJoinedSoundNotification = initializeSoundFeatures(options.joinSoundNotification, 'UserJoined');
	userMentioningSoundNotification = initializeSoundFeatures(options.mentioningSoundNotification, 'UserMentioned');

	// DOM events:
	jQuery(window).blur(onWindowBlur);
	jQuery(window).focus(onWindowFocus);

	// public API:
	this.sendNotifications = sendNotifications;
	this.sendNotificationForEvent = sendNotificationForEvent;
};

/**
 * jQuery extensions setup.
 */
(function() {
	/**
	 * Adds support for "progress" and "progressUpload" events. Required for progress bars.
	 */
	function addXhrProgressEvent() {
		var originalXhr = jQuery.ajaxSettings.xhr;
		jQuery.ajaxSetup({
			xhr: function() {
				var req = originalXhr.call(jQuery.ajaxSettings), that = this;
				if (req) {
					if (typeof req.addEventListener == "function" && that.progress !== undefined) {
						req.addEventListener("progress", function(evt) {
							that.progress(evt);
						}, false);
					}
					if (typeof req.upload == "object" && that.progressUpload !== undefined) {
						req.upload.addEventListener("progress", function(evt) {
							that.progressUpload(evt);
						}, false);
					}
				}
				return req;
			}
		});
	};

	addXhrProgressEvent();
})();
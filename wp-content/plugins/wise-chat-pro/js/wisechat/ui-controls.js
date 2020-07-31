/**
 * Wise Chat UI Controls namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.ui = wisechat.ui || {};

/**
 * DateAndTimeRenderer class. Renders date and time next to each message according to the settings.
 *
 * @param {Object} options Plugin's global options
 * @constructor
 */
wisechat.ui.DateAndTimeRenderer = function(options) {
	var dateFormatter = wisechat.utils.dateFormatter;
	var spanDate = '<span class="wcMessageTimeDate">';
	var spanTime = '<span class="wcMessageTimeHour">';
	var spanClose = '</span>';
	var dateAndTimeMode = options.messagesTimeMode;

	function formatFullDateAndTime(date, nowDate, element) {
		var timeFormatted = typeof options.messagesTimeFormat !== 'undefined' && options.messagesTimeFormat.length > 0
			? wisechat.utils.moment(date).format(options.messagesTimeFormat)
			: dateFormatter.getLocalizedTime(date);

		if (dateFormatter.isSameDate(nowDate, date)) {
			element.html(spanTime + timeFormatted + spanClose);
		} else {
			var dateFormatted = typeof options.messagesDateFormat !== 'undefined' && options.messagesDateFormat.length > 0
				? wisechat.utils.moment(date).format(options.messagesDateFormat)
				: dateFormatter.getLocalizedDate(date);
			element.html(
				spanDate + dateFormatted + spanClose + ' ' +
				spanTime + timeFormatted + spanClose
			);
		}
		element.attr('data-fixed', '1');
	}

	function formatElapsedDateAndTime(date, nowDate, element) {
		var yesterdayDate = new Date();
		var diffSeconds = parseInt((nowDate.getTime() - date.getTime()) / 1000);
		yesterdayDate.setDate(nowDate.getDate() - 1);

		var formattedDateAndTime = '';
		var isFixed = false;
		var timeFormatted = typeof options.messagesTimeFormat !== 'undefined' && options.messagesTimeFormat.length > 0
			? wisechat.utils.moment(date).format(options.messagesTimeFormat)
			: dateFormatter.getLocalizedTime(date);

		if (diffSeconds < 60) {
			if (diffSeconds <= 0) {
				diffSeconds = 1;
			}
			formattedDateAndTime = spanTime + diffSeconds + ' ' + options.messages.messageSecAgo + spanClose;
		} else if (diffSeconds < 60 * 60) {
			formattedDateAndTime = spanTime + parseInt(diffSeconds / 60) + ' ' + options.messages.messageMinAgo + spanClose;
		} else if (dateFormatter.isSameDate(nowDate, date)) {
			formattedDateAndTime = spanTime + timeFormatted + spanClose;
			isFixed = true;
		} else if (dateFormatter.isSameDate(yesterdayDate, date)) {
			formattedDateAndTime = spanDate + options.messages.messageYesterday + spanClose + ' ' + spanTime + timeFormatted + spanClose;
			isFixed = true;
		} else {
			var dateFormatted = typeof options.messagesDateFormat !== 'undefined' && options.messagesDateFormat.length > 0
				? wisechat.utils.moment(date).format(options.messagesDateFormat)
				: dateFormatter.getLocalizedDate(date);

			formattedDateAndTime = spanDate + dateFormatted + spanClose + ' ' + spanTime + timeFormatted + spanClose;
			isFixed = true;
		}

		element.html(formattedDateAndTime);
		if (isFixed) {
			element.attr('data-fixed', '1');
		}
	}

	/**
	 * Finds all child nodes described by wcMessageTime class and fills their content using converted "data-utc" attribute.
	 *
	 * @param {jQuery} parentContainer Parent node to search child nodes
	 * @param {String} nowISODate Current date in ISO format
	 */
	function convertUTCMessagesTime(parentContainer, nowISODate) {
		if (dateAndTimeMode === 'hidden') {
			return;
		}

		parentContainer.find('.wcMessageTime:not([data-fixed])').each(function(index, element) {
			element = jQuery(element);

			var date = dateFormatter.parseISODate(element.data('utc'));
			var nowDate = dateFormatter.parseISODate(nowISODate);
			if (dateAndTimeMode === 'elapsed') {
				formatElapsedDateAndTime(date, nowDate, element);
			} else {
				formatFullDateAndTime(date, nowDate, element);
			}
		});
	}

	// public API:
	this.convertUTCMessagesTime = convertUTCMessagesTime;
	this.formatElapsedDateAndTime = formatElapsedDateAndTime;
};

/**
 * WiseChatProgressBar - controls the main progress bar.
 */

/**
 * ProgressBar class. Controls the given progress bar and extends jQuery.xhr in order to support progress events.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery) progressBar jQuery object referencing HTML progress elements
 * @constructor
 */
wisechat.ui.ProgressBar = function(options, progressBar) {
	function show() {
		progressBar.show();
	}

	function hide() {
		progressBar.hide();
	}

	function setValue(value) {
		progressBar.attr("value", value);
	}

	// public API:
	this.show = show;
	this.hide = hide;
	this.setValue = setValue;
};

/**
 * Dialog class for displaying modal dialog windows.
 *
 * @param {String} type Type of the dialog window
 * @param {jQuery} container Container element for the dialog window
 * @param {Object} options Dialog options
 * @constructor
 */
wisechat.ui.Dialog = function(type, container, options) {
	var backgroundLayer = null;
	var modalWindow = null;
	var header = null;
	var footer = null;
	var content = null;

	function show() {
		if (backgroundLayer == null) {
			backgroundLayer = jQuery('<div />').addClass('wcModalBackgroundLayer');
			container.append(backgroundLayer);
			container.css('overflow', 'hidden');

			modalWindow = jQuery('<div />')
				.addClass('wcModalWindow');

			backgroundLayer.append(modalWindow);

			header = jQuery('<div />')
				.addClass('wcModalHeader')
				.html(options.title);
			modalWindow.append(header);

			content = jQuery('<div />')
				.addClass('wcModalContent')
				.css('background-color', getBackgroundColor())
				.html(options.text);
			modalWindow.append(content);

			footer = jQuery('<div />')
				.addClass('wcModalFooter');
			modalWindow.append(footer);

			if (type == 'alert') {
				content.html(
					jQuery('<div />')
					.addClass('wcError')
					.html(options.text)
				);
			} else {
				content.html(options.text);
			}

			if (jQuery.isArray(options.buttons)) {
				jQuery.each(options.buttons, function(index, buttonSpec) {
					var button = jQuery('<input />')
						.addClass('wcModalFooterButton')
						.attr('type', 'button')
						.attr('value', wisechat.utils.htmlUtils.decodeEntities(buttonSpec.label));

					footer.append(button);

					if (buttonSpec.onClickClose && buttonSpec.onClickClose == true) {
						button.click(hide);
					}
					if (buttonSpec.onClick) {
						button.click(buttonSpec.onClick);
					}
				});

				footer.append('<br class="wcClear" />');
			}

			// auto-center:
			center();
			jQuery(window).on('resize', center);
		} else {
			backgroundLayer.show();
		}
	}

	function getBackgroundColor() {
		function isTransparent(bgcolor){
			return (bgcolor == "transparent" || bgcolor.substring(0,4) == "rgba");
		}

		var bgColor = container.css('background-color');
		if (isTransparent(bgColor)) {
			container.parents().each(function() {
				if (!isTransparent(jQuery(this).css('background-color'))){
					bgColor = jQuery(this).css('background-color');
					return false;
				}
			});
		}

		return bgColor;
	}

	function center() {
		if (modalWindow != null) {
			modalWindow.css({
				'top': ( backgroundLayer.outerHeight() - modalWindow.outerHeight() ) / 2  + "px",
				'left': ( backgroundLayer.outerWidth() - modalWindow.outerWidth() ) / 2 + "px"
			});
		}
	}

	function hide() {
		jQuery(window).off('resize', center);
		backgroundLayer.remove();
		container.css('overflow', '');
	}

	// public API:
	this.show = show;
	this.close = hide;
};

/**
 * EmoticonsPanel class is responsible for displaying emoticons layer and inserting selected emoticon
 * into message input field.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} insertEmoticonButton Button that opens this emoticons button
 * @param {jQuery} container Container element for displaying messages
 * @constructor
 */
wisechat.ui.EmoticonsPanel = function(options, insertEmoticonButton, container) {
	var $this = jQuery(this);
	var setupDone = false;
	var EMOTICONS_1 = [
		'zip-it', 'blush', 'angry', 'not-one-care', 'laugh-big', 'please', 'cool', 'minishock',
		'devil', 'silly', 'smile', 'devil-laugh', 'heart', 'not-guilty', 'hay',
		'in-love', 'meow', 'tease', 'gift', 'kissy', 'sad', 'speechless', 'goatse',
		'fools', 'why-thank-you', 'wink', 'angel', 'annoyed', 'flower', 'surprised',
		'female', 'laugh', 'ill', 'total-shock', 'zzz', 'clock', 'oh', 'mail', 'crazy',
		'cry', 'boring', 'geek'
	];
	var EMOTICONS_SHORTCUTS_1 = {
		'smile': ':)', 'wink': ';)', 'laugh': ':D',
		'sad': ':(', 'cry': ';(', 'kissy': ':*', 'silly': ':P',
		'crazy': ';P', 'angry': ':[', 'devil-laugh': ':>', 'devil': ':]', 'goatse': ':|'
	};
	var FILES_EXTENSION_1 = 'png';
	var SUBDIRECTORY_1 = '';
	var LAYER_WIDTH_1 = null;

	var EMOTICONS_2 = [
		'angry', 'bulb', 'cafe', 'clap', 'clouds', 'cry', 'devil', 'gift', 'handshake',
		'heart', 'kissy', 'laugh-big', 'no', 'ok', 'feel_peace', 'oh_please', 'rain', 'scared',
		'silly', 'snail', 'sun', 'baloons', 'bye', 'cake', 'cleaver', 'cool', 'cry_big',
		'drink', 'hat', 'heart_big', 'laugh', 'moon', 'offended', 'omg', 'a_phone',
		'question', 'sad', 'shy', 'smile', 'stars', 'wine'
	];
	var EMOTICONS_SHORTCUTS_2 = {
		'smile': ':)', 'wink': ';)', 'laugh': ':D',
		'sad': ':(', 'cry': ';(', 'kissy': ':*', 'silly': ':P',
		'crazy': ';P', 'angry': ':[', 'devil-laugh': ':>', 'devil': ':]'
	};
	var FILES_EXTENSION_2 = 'gif';
	var SUBDIRECTORY_2 = 'set_2/';
	var LAYER_WIDTH_2 = 235;

	var EMOTICONS_3 = [
		'angel', 'confused', 'cthulhu', 'drugged', 'grinning', 'horrified', 'kawaii', 'madness',
		'shy', 'spiteful', 'terrified', 'tongue_out', 'tongue_out_up_left', 'winking_grinning',
		'angry', 'cool', 'cute', 'frowning', 'happy', 'hug', 'kissing', 'malicious', 'sick',
		'stupid', 'thumbs_down', 'tongue_out_laughing', 'unsure', 'winking_tongue_out', 'aww',
		'creepy', 'cute_winking', 'gasping', 'happy_smiling', 'irritated', 'laughing', 'naww',
		'smiling', 'surprised', 'thumbs_up', 'tongue_out_left', 'unsure_2', 'blushing', 'crying',
		'devil', 'greedy', 'heart', 'irritated_2', 'lips_sealed', 'i_am_pouting', 'speechless',
		'surprised_2', 'tired', 'tongue_out_up', 'winking'
	];
	var EMOTICONS_SHORTCUTS_3 = {
		'smiling': ':)', 'winking': ';)', 'laughing': ':D',
		'frowning': ':(', 'crying': ';(', 'kissing': ':*', 'tongue_out': ':P',
		'winking_tongue_out': ';P', 'angry': ':[', 'devil': ':>', 'devil': ':]', 'irritated': ':|'
	};
	var FILES_EXTENSION_3 = 'png';
	var SUBDIRECTORY_3 = 'set_3/';
	var LAYER_WIDTH_3 = 195;

	var EMOTICONS_4 = [
		'angel', 'beer', 'clock', 'crying', 'drink', 'eyeroll', 'glasses-cool', 'jump',
		'mad-tongue', 'sad', 'sick', 'smile-big', 'thinking', 'wilt',
		'angry', 'bomb', 'cloudy', 'cute', 'drool', 'fingers-crossed', 'go-away',
		'kiss', 'mail', 'shock', 'silly', 'smirk', 'tongue', 'wink',
		'arrogant', 'bye', 'coffee', 'devil', 'embarrassed', 'freaked-out', 'good',
		'laugh', 'mean', 'shout', 'sleepy', 'star', 'vampire', 'worship',
		'bad', 'cake', 'confused', 'disapointed', 'excruciating', 'giggle', 'in-love',
		'love', 'neutral', 'rotfl', 'shut-mouth', 'smile', 'struggle', 'weep', 'yawn',
		'beauty', 'hypnotized', 'island', 'quiet', 'rose', 'soccerball'
	];
	var EMOTICONS_SHORTCUTS_4 = {
		'smile': ':)', 'wink': ';)', 'laugh': ':D',
		'sad': ':(', 'crying': ';(', 'kiss': ':*', 'tongue': ':P',
		'silly': ';P', 'angry': ':[', 'devil': ':>', 'devil': ':]', 'neutral': ':|'
	};
	var FILES_EXTENSION_4 = 'png';
	var SUBDIRECTORY_4 = 'set_4/';
	var LAYER_WIDTH_4 = 280;

	var EMOTICONS = EMOTICONS_1;
	var EMOTICONS_SHORTCUTS = EMOTICONS_SHORTCUTS_1;
	var FILES_EXTENSION = FILES_EXTENSION_1;
	var SUBDIRECTORY = SUBDIRECTORY_1;
	var LAYER_WIDTH = null;
	switch (options.emoticonsSet) {
		case 2:
			EMOTICONS = EMOTICONS_2;
			EMOTICONS_SHORTCUTS = EMOTICONS_SHORTCUTS_2;
			FILES_EXTENSION = FILES_EXTENSION_2;
			SUBDIRECTORY = SUBDIRECTORY_2;
			LAYER_WIDTH = LAYER_WIDTH_2;
			break;
		case 3:
			EMOTICONS = EMOTICONS_3;
			EMOTICONS_SHORTCUTS = EMOTICONS_SHORTCUTS_3;
			FILES_EXTENSION = FILES_EXTENSION_3;
			SUBDIRECTORY = SUBDIRECTORY_3;
			LAYER_WIDTH = LAYER_WIDTH_3;
			break;
		case 4:
			EMOTICONS = EMOTICONS_4;
			EMOTICONS_SHORTCUTS = EMOTICONS_SHORTCUTS_4;
			FILES_EXTENSION = FILES_EXTENSION_4;
			SUBDIRECTORY = SUBDIRECTORY_4;
			LAYER_WIDTH = LAYER_WIDTH_4;
			break;
	}

	var layer = jQuery('<div />')
		.attr('class', 'wcEmoticonsLayer')
		.hide();
	jQuery('body').append(layer);
	var subLayer = jQuery('<div />')
		.attr('class', 'wcEmoticonsSubLayer');
	layer.append(subLayer);

	if (options.sidebarMode) {
		layer.css('position', 'fixed');
	}

	function setup() {
		if (setupDone) {
			return true;
		}

		// build buttons:
		if (jQuery.isArray(options.emoticons)) {
			if (options.customEmoticonsPopupHeight > 0) {
				subLayer.css({
					height: options.customEmoticonsPopupHeight,
					overflowY: 'auto'
				});
			}

			EMOTICONS_SHORTCUTS = [];
			for (var j = 0; j < options.emoticons.length; j++) {
				var emoticon = options.emoticons[j];
				var id = 'emoticon-' + emoticon.id;
				var img = jQuery('<img />').attr('src', emoticon.url);
				if (options.customEmoticonsEmoticonMaxWidthInPopup > 0) {
					img.css('max-width', options.customEmoticonsEmoticonMaxWidthInPopup);
				}

				var button = jQuery('<a />')
					.attr('href', 'javascript://')
					.attr('title', emoticon.alias && emoticon.alias.length > 0 ? emoticon.alias : id)
					.append(img)
					.click(function (emoticon) {
						return function () {
							onEmoticonClick(emoticon);
						}
					}(id));
				subLayer.append(button);

				if (emoticon.alias.length > 0) {
					EMOTICONS_SHORTCUTS[id] = emoticon.alias;
				}
			}

			if (options.customEmoticonsPopupWidth > 0) {
				LAYER_WIDTH = options.customEmoticonsPopupWidth;
			}
		} else {
			for (var i = 0; i < EMOTICONS.length; i++) {
				var emoticon = EMOTICONS[i];
				var imageSrc = options.emoticonsBaseURL + SUBDIRECTORY + emoticon + '.' + FILES_EXTENSION;
				var button = jQuery('<a />')
					.attr('href', 'javascript://')
					.attr('title', emoticon)
					.append(jQuery('<img />').attr('src', imageSrc))
					.click(function (emoticon) {
						return function () {
							onEmoticonClick(emoticon);
						}
					}(emoticon));
				subLayer.append(button);
			}
		}

		if (LAYER_WIDTH !== null) {
			layer.css('width', LAYER_WIDTH + 'px');
		}

		setupDone = true;

		return false;
	}

	function hideLayer() {
		layer.hide();
		jQuery(document).unbind("mousedown", onDocumentMouseDown);
	}

	function showLayer() {
		if (setup()) {
			layer.show();
		} else {
			setTimeout(function() { layer.show() }, 300);
		}
		jQuery(document).bind("mousedown", onDocumentMouseDown);
	}

	function onDocumentMouseDown(event) {
		if (insertEmoticonButton[0] != event.target && layer[0] != event.target && !jQuery.contains(layer[0], event.target)) {
			hideLayer();
		}
	}

	function onInsertEmoticonButtonClick(e) {
		e.preventDefault();

		if (!layer.is(':visible')) {
			var leftValue = insertEmoticonButton.offset().left - layer.outerWidth() - 5;
			if (leftValue < 0) {
				leftValue = container.offset().left;

				if (container.outerWidth() < LAYER_WIDTH) {
					layer.css({
						width: container.outerWidth() + 'px'
					});

				} else {
					layer.css('width', LAYER_WIDTH + 'px');
				}
			}
			var offsetBottom = jQuery(window).height() - insertEmoticonButton.offset().top + (options.sidebarMode ? jQuery(window).scrollTop() : 0);

			layer.css({
				bottom: offsetBottom,
				left: leftValue
			});
			showLayer();
		} else {
			hideLayer();
		}
	}

	var onEmoticonClick = function(emoticon) {
		var emoticonCode = EMOTICONS_SHORTCUTS[emoticon] ? EMOTICONS_SHORTCUTS[emoticon] : '<' + emoticon + '>';
		$this.trigger('emoticonSelected', [emoticonCode]);
		hideLayer();
	};

	insertEmoticonButton.click(onInsertEmoticonButtonClick);

	// public API:
	this.$ = $this;
};

/**
 * MessageAttachments class. Attachments management.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} container Container element for the attachments management
 * @param {wisechat.ui.ProgressBar} progressBar
 */
wisechat.ui.MessageAttachments = function(options, container, progressBar) {
	var IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif'];
	var prepareImageEndpoint = options.apiEndpointBase + '?action=wise_chat_prepare_image_endpoint';
	var messageAttachmentsPanel = container.find('.wcMessageAttachments');
	var imageUploadPreviewImage = container.find('.wcImageUploadPreview');
	var imageUploadFile = container.find('.wcImageUploadFile');
	var attachmentClearButton = container.find('.wcAttachmentClear');
	var fileUploadFile = container.find('.wcFileUploadFile');
	var fileUploadNamePreview = container.find('.wcFileUploadNamePreview');
	var attachments = [];

	function showErrorMessage(message) {
		alert(message);
	}

	function addAttachment(type, data, name) {
		attachments.push({ type: type, data: data, name: name });
	}

	function showImageAttachment() {
		if (attachments.length > 0 && attachments[0].type === 'image') {
			wisechat.utils.imageViewer.show(attachments[0].data);
		}
	}

	function onImageUploadFileChange() {
		var fileInput = imageUploadFile[0];
		if (typeof FileReader === 'undefined' || fileInput.files.length === 0) {
			showErrorMessage('Unsupported operation');
			return;
		}

		var fileDetails = fileInput.files[0];
		if (fileDetails.size && fileDetails.size > options.imagesSizeLimit) {
			showErrorMessage(options.messages.messageSizeLimitError);
			return;
		}

		if (IMAGE_TYPES.indexOf(getExtension(fileDetails)) > -1) {
			var fileReader = new FileReader();
			fileReader.onload = function(event) {
				clearAttachments();
				prepareImage(event.target.result, function(preparedImageData) {
					if (typeof preparedImageData !== 'undefined' && preparedImageData.length > 0) {
						addAttachment('image', preparedImageData);

						imageUploadPreviewImage.show();
						imageUploadPreviewImage.attr('src', preparedImageData);
						messageAttachmentsPanel.show();
						imageUploadFile.val('');
					} else {
						showErrorMessage('Cannot prepare image due to server error');
					}
				});
			};
			fileReader.readAsDataURL(fileDetails);
		} else {
			showErrorMessage(options.messages.messageUnsupportedTypeOfFile);
		}
	}

	function prepareImage(imageSource, successCallback) {
		var that = this;

		progressBar.setValue(0);
		progressBar.show();

		jQuery.ajax({
				type: "POST",
				url: prepareImageEndpoint,
				data: {
					data: imageSource,
					checksum: options.checksum
				},
				progressUpload: function(event) {
					if (event.lengthComputable) {
						var percent = parseInt(event.loaded / event.total * 100);
						if (percent > 100) {
							percent = 100;
						}
						progressBar.setValue(percent);
					}
				}
			})
			.done(function(result) {
				progressBar.hide();
				successCallback.apply(that, [result]);
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				progressBar.hide();
				try {
					var response = jQuery.parseJSON(jqXHR.responseText);
					if (typeof response.error != 'undefined') {
						showErrorMessage(response.error);
					} else {
						showErrorMessage('Image preparation error');
					}
				}
				catch (e) {
					showErrorMessage('Unknown error occurred');
				}
			});
	}

	function onFileUploadFileChange() {
		var fileInput = fileUploadFile[0];
		if (typeof FileReader === 'undefined' || fileInput.files.length === 0) {
			showErrorMessage('Unsupported operation');
			return;
		}

		var fileDetails = fileInput.files[0];
		if (options.attachmentsValidFileFormats.indexOf(getExtension(fileDetails)) > -1) {
			var fileReader = new FileReader();
			var fileName = fileDetails.name;

			if (fileDetails.size > options.attachmentsSizeLimit) {
				showErrorMessage(options.messages.messageSizeLimitError);
			} else {
				fileReader.onload = function(event) {
					clearAttachments();
					addAttachment('file', event.target.result, fileName);
					fileUploadNamePreview.html(fileName);
					fileUploadNamePreview.show();
					messageAttachmentsPanel.show();
				};
				fileReader.readAsDataURL(fileDetails);
			}
		} else {
			showErrorMessage(options.messages.messageUnsupportedTypeOfFile);
		}
	}

	function getExtension(fileDetails) {
		if (typeof fileDetails.name !== 'undefined') {
			var splitted = fileDetails.name.split('.');
			if (splitted.length > 1) {
				return splitted.pop().toLowerCase();
			}
		}

		return null;
	}

	function resetInput(inputField) {
		inputField.wrap('<form>').parent('form').trigger('reset');
		inputField.unwrap();
	}

	/**
	 * Returns an array of prepared attachments.
	 *
	 * @return {Array}
	 */
	function getAttachments() {
		return attachments;
	}

	/**
	 * Clears all added attachments, resets and hides UI related to added attachments.
	 */
	function clearAttachments(e) {
		if (typeof e !== 'undefined') {
			e.preventDefault();
		}
		attachments = [];
		messageAttachmentsPanel.hide();
		fileUploadNamePreview.hide();
		fileUploadNamePreview.html('');
		imageUploadPreviewImage.hide();
		resetInput(fileUploadFile);
		resetInput(imageUploadFile);
	}

	// DOM events:
	imageUploadFile.change(onImageUploadFileChange);
	fileUploadFile.change(onFileUploadFileChange);
	attachmentClearButton.click(clearAttachments);
	imageUploadPreviewImage.click(showImageAttachment);

	// public API:
	this.getAttachments = getAttachments;
	this.clearAttachments = clearAttachments;
};

/**
 * Displays messages log for the user.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} container
 * @constructor
 */
wisechat.ui.VisualLogger = function(options, container) {
	var AUTO_CLOSE_DELAY = 60000;
	var innerContainer = container.find('.wcVisualLoggerInner');
	var lastErrorText = '';
	var debugLog = [];


	/**
	 * Logs info message.
	 *
	 * @param {String} text
	 */
	function logInfo(text) {
		logMessages(text, 'wcVisualLoggerInfoMessage');
	}

	/**
	 * Logs error message.
	 *
	 * @param {String} text
	 */
	function logError(text) {
		if (options.errorMode) {
			logMessages(text, 'wcVisualLoggerErrorMessage');
		}
	}

	/**
	 * Logs error message.
	 *
	 * @param {String} text
	 */
	function logMessages(text, className) {
		text = text + '';
		text = text.replace(/^\s+|\s+$/g, '');
		if (text.length === 0 || lastErrorText == text) {
			return;
		}

		container.removeClass('wcInvisible');
		var log = jQuery('<div />').addClass('wcVisualLoggerMessage').addClass(className).hide();
		var closeButton = jQuery('<button />')
			.attr('type', 'button')
			.append(
				jQuery('<span />').html('&times;')
			);

		log.text(text);
		log.prepend(closeButton);
		log.fadeIn(200);
		innerContainer.prepend(log);

		closeButton.click(onLogCloseButtonClick);
		setTimeout(function() {
			onLogCloseButtonClick.apply(closeButton, []);
		}, AUTO_CLOSE_DELAY);

		lastErrorText = text;
	}

	function onLogCloseButtonClick() {
		jQuery(this).closest('div').fadeOut(200, function() {
			jQuery(this).remove();

			// hide the container if there is no other logs:
			if (innerContainer.find('div').length === 0) {
				container.addClass('wcInvisible');
				lastErrorText = '';
			}
		});
	}

	/**
	 * Logs debug message.
	 *
	 * @param {String} text
	 */
	function logDebug(text) {
		if (debugLog.length > 20) {
			debugLog.shift();
		}
		var nowDate = new Date();
		var dateTimeShort =  nowDate.getDate() + '/' + (nowDate.getMonth() + 1) + '/' + nowDate.getFullYear();
		dateTimeShort += ' ' + nowDate.getHours() + ':' + nowDate.getMinutes() + ':' + nowDate.getSeconds();
		debugLog.push(jQuery('<div />').text('[' + dateTimeShort + '] ' + text));

		if (options.debugMode) {
			showDebug();
		}
	}

	/**
	 * Renders debug log.
	 */
	function showDebug() {
		var debugContainer = container.parent().find('.wcDebug');
		if (debugContainer.length == 0) {
			debugContainer = jQuery('<div>').attr('class', 'wcDebug');
			if (!options.sidebarMode) {
				container.parent().append(jQuery('<div>').html('  [ DEBUG MODE error log. Select and copy the text below: ]  '));
			}
			container.parent().append(debugContainer);

			debugContainer.css('backgroundColor', wisechat.utils.htmlUtils.getAncestorBackgroundColor(debugContainer));
		}
		debugContainer.html('');
		if (options.sidebarMode) {
			debugContainer.append('<strong>[ DEBUG MODE error log ]</strong>');
		}
		debugContainer.append(debugLog);
	}

	function setBottom(value) {
		container.css('bottom', value + 'px');
	}

	function setRight(value) {
		container.css('right', value + 'px');
	}

	function setWidth(value) {
		container.css('width', value + 'px');
	}

	// public API:
	this.logInfo = logInfo;
	this.logError = logError;
	this.logDebug = logDebug;
	this.showDebug = showDebug;
	this.setBottom = setBottom;
	this.setRight = setRight;
	this.setWidth = setWidth;
};

/**
 * Displays info window.
 *
 * @param {Object} options
 * @param {String} contentSource
 * @param {String} cssClass
 * @param {jQuery} targetContainer
 * @param {String} targetSelector
 * @param {String} targetID
 * @constructor
 */
wisechat.ui.InfoWindow = function(options, contentSource, cssClass, targetContainer, targetSelector, targetID) {
	var container = jQuery('body');
	var infoWindow = null;

	function setup() {
		infoWindow = jQuery('<div class="wcInfoWindow" />');
		if (options.theme.length === 0) {
			infoWindow.css({
				backgroundColor: wisechat.utils.htmlUtils.getAncestorBackgroundColor(container.find('.wcContainer'))
			});
		}
		infoWindow.hide();
		infoWindow.addClass(cssClass);
		container.append(infoWindow);
		infoWindow.on('mouseenter', onInfoWindowMouseEnter);
		infoWindow.on('mouseleave', onInfoWindowMouseLeave);
		infoWindow.on('click', 'a, input[type="button"], button', onInfoWindowMouseClickableClick);
		infoWindow.html(contentSource);

		targetContainer.on('mouseenter', targetSelector, onMouseEnter);
		targetContainer.on('mouseleave', targetSelector, onMouseLeave);
		targetContainer.on('click', targetSelector, onClick);
	}

	function onMouseEnter(e) {
		var element = jQuery(e.target).closest(targetSelector);
		if (element.data('info-window-id') != targetID) {
			return;
		}

		infoWindow.data('showing', 1);
		setTimeout(function() {
			if (typeof infoWindow.data('showing') === 'undefined') {
				return;
			}
			var offsetTop = element.offset().top;
			var rightValue = jQuery(window).width() - element.offset().left + 5;
			infoWindow.css({
				position: 'absolute',
				top: offsetTop,
				right: rightValue
			});

			infoWindow.show();
			infoWindow.removeData('showing');
			infoWindow.data('visible', 1);
		}, 750);
	}

	function onMouseLeave(e) {
		var element = jQuery(e.target).closest(targetSelector);
		if (element.data('info-window-id') != targetID) {
			return;
		}

		infoWindow.removeData('showing');

		setTimeout(function() {
			if (typeof infoWindow.data('showing') !== 'undefined') {
				return;
			}
			infoWindow.hide();
		}, 450);
	}

	function onClick(e) {
		var element = jQuery(e.target).closest(targetSelector);
		if (element.data('info-window-id') != targetID) {
			return;
		}
		infoWindow.removeData('showing');
		infoWindow.hide();
	}

	function onInfoWindowMouseEnter(e) {
		infoWindow.data('showing', 1);
	}

	function onInfoWindowMouseLeave(e) {
		infoWindow.removeData('showing');
		infoWindow.hide();
	}

	function onInfoWindowMouseClickableClick(e) {
		infoWindow.removeData('showing');
		infoWindow.hide();
	}

	function updateContent(contentSource) {
		infoWindow.html(contentSource);
	}

	setup();

	// public API:
	this.updateContent = updateContent;
}
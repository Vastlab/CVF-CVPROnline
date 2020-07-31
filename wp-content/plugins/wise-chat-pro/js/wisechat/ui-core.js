/**
 * Wise Chat UI Core namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.ui = wisechat.ui || {};

/**
 * Messages class is responsible for displaying messages in container.
 *
 * @param {wisechat.ui.Window} window
 * @param {jQuery} container Container element for displaying messages
 * @param {wisechat.ui.Controls} controls
 * @constructor
 */
wisechat.ui.Messages = function(window, container, controls) {
	var options = window.options;
	var notifier = window.notifier;
	var logger = window.logger;
	var maintenance = window.maintenance;
	var transceiver = window.transceiver;

	var MESSAGES_ORDER = options.messagesOrder;
	var messageApproveEndpoint = options.apiWPEndpointBase + '?action=wise_chat_approve_message_endpoint';
	var messageDeleteEndpoint = options.apiWPEndpointBase + '?action=wise_chat_delete_message_endpoint';
	var messageSaveEndpoint = options.apiWPEndpointBase + '?action=wise_chat_save_message_endpoint';
	var userKickEndpoint = options.apiWPEndpointBase + '?action=wise_chat_user_kick_endpoint';
	var spamReportEndpoint = options.apiWPEndpointBase + '?action=wise_chat_spam_report_endpoint';
	var userBanEndpoint = options.apiWPEndpointBase + '?action=wise_chat_user_ban_endpoint';
	var dateAndTimeRenderer = new wisechat.ui.DateAndTimeRenderer(options);
	var lastMessageText = null;
	var needsScrollImmediately = false;
	var editingMessagesCache = {};

	function isAscendingOrder() {
		return MESSAGES_ORDER == 'ascending';
	}

	/**
	 * Moves the scrollbar to the top (descending order mode) or to the bottom (ascending order mode).
	 */
	function scrollMessages() {
		if (container.length === 0) {
			return;
		}

		var scrollPosition = isAscendingOrder() ? container[0].scrollHeight : 0;
		needsScrollImmediately = container.hasClass('wcInvisible');

		if (!needsScrollImmediately) {
			setTimeout(function () {
				setMessagesScrollPosition(scrollPosition);
			}, 200);
		}
	}

	/**
	 * Refreshes the container.
	 */
	function refresh() {
		if (container.length === 0) {
			return;
		}

		if (needsScrollImmediately) {
			var scrollPosition = isAscendingOrder() ? container[0].scrollHeight : 0;
			setMessagesScrollPositionNow(scrollPosition);
			needsScrollImmediately = false;
		}
	}

	/**
	 * Checks whether the scrollbar is set to the top (descending order mode) or to the bottom (ascending order mode).
	 *
	 * @return {Boolean}
	 */
	function isFullyScrolled() {
		if (container.length === 0) {
			return true;
		}

		if (isAscendingOrder()) {
			var padding = container.innerHeight() - container.height();
			return (container.height() + container.scrollTop() + padding) >= container[0].scrollHeight;
		} else {
			return container.scrollTop() == 0;
		}
	}

	function setMessagesScrollPosition(scrollPosition) {
		container.stop().animate({ scrollTop: scrollPosition }, '500', 'swing');
	}

	function setMessagesScrollPositionNow(scrollPosition) {
		container.stop().scrollTop(scrollPosition);
	}

	/**
	 * Corrects position of the scrollbar when new messages are appended or prepended.
	 * It prevents from slight movement of the scrollbar.
	 *
	 * @param {Number} previousMessagesScrollPosition Previous position of the scrollbar
	 * @param {Number} previousMessagesScrollHeight Previous height of the scroll area
	 */
	function correctMessagesScrollPosition(previousMessagesScrollPosition, previousMessagesScrollHeight) {
		if (container.length === 0) {
			return;
		}

		var messagesNewScrollHeight = container[0].scrollHeight;
		var scrollDifference = isAscendingOrder() ? 0 : messagesNewScrollHeight - previousMessagesScrollHeight;
		setMessagesScrollPosition(previousMessagesScrollPosition + scrollDifference);
	}

	function showMessage(message) {
		var wasFullyScrolled = isFullyScrolled();
		var messagesScrollPosition = container.scrollTop();
		var messagesScrollHeight = container.length > 0 ? container[0].scrollHeight : 0;
		var parsedMessage = jQuery(message.text);
		if (isAscendingOrder()) {
			container.append(parsedMessage);
		} else {
			container.prepend(parsedMessage);
		}

		// user mentioning notification:
		var userMentioned = false;
		if (options.mentioningSoundNotification.length > 0 && message.text.length > 0 && typeof options.userData !== 'undefined') {
			var regexp = new RegExp("@" + options.userData.name, "g");
			if (message.text.match(regexp)) {
				notifier.sendNotificationForEvent('userMentioning');
				userMentioned = true;
			}
		}

		// send regular notifications instead:
		var disableNotifications = false;
		if (options.fbDisableChannel && (typeof message.isPrivate === 'undefined' || message.isPrivate === false)) {
			disableNotifications = true;
		}
		if (!disableNotifications && !userMentioned && !parsedMessage.hasClass('wcInvisible') && typeof message.noNotifications === 'undefined' || message.noNotifications === false) {
			notifier.sendNotifications();
		}

		if (wasFullyScrolled) {
			scrollMessages();
		} else {
			correctMessagesScrollPosition(messagesScrollPosition, messagesScrollHeight);
		}

		container.triggerHandler('messageAdded');
		lastMessageText = message;
	}

	function showPlainMessage(message) {
		showMessage({
			text: '<div class="wcMessage wcPlainMessage"><span class="wcMessageContent">' + message + '</span></div>'
		});
	}

	function hideMessage(messageId) {
		container.find('div[data-id="' + messageId + '"]').remove();
	}

	function hideAllMessages() {
		container.find('div.wcMessage').remove();
	}

	function getContainer() {
		return container;
	}

	function refreshMessages(nowISODate) {
		dateAndTimeRenderer.convertUTCMessagesTime(container, nowISODate);
	}

	function refreshMessagesControls() {
		processAdminButtons();
	}

	function processAdminButtons() {
		container.find('.wcAdminAction:not([data-admin-actions-processed])').each(function(index, element) {
			element = jQuery(element);
			var messageElement = element.closest('.wcMessage');
			var isHidden = messageElement.length > 0 && messageElement.hasClass('wcMessageHidden');

			if (element.hasClass('wcMessageApproveButton')) {
				if (options.rights.approveMessages == true && isHidden) {
					element.removeClass('wcInvisible');
				} else {
					element.addClass('wcInvisible');
				}
			}

			if (element.hasClass('wcMessageDeleteButton')) {
				if (options.rights.deleteMessages == true) {
					element.removeClass('wcInvisible');
				} else {
					element.addClass('wcInvisible');
				}
			}

			if (!options.rights.editMessages && element.hasClass('wcMessageEditButton') && messageElement.hasClass('wcCurrentUserMessage')) {
				if (options.rights.editOwnMessages == true) {
					element.removeClass('wcInvisible');
				} else {
					element.addClass('wcInvisible');
				}
			}

			if (element.hasClass('wcMessageEditButton') && element.hasClass('wcInvisible')) {
				if (options.rights.editMessages == true) {
					element.removeClass('wcInvisible');
				}
			}

			if (element.hasClass('wcUserBanButton')) {
				if (options.rights.banUsers == true) {
					element.removeClass('wcInvisible');
				} else {
					element.addClass('wcInvisible');
				}
			}

			if (element.hasClass('wcUserKickButton')) {
				if (options.rights.kickUsers == true) {
					element.removeClass('wcInvisible');
				} else {
					element.addClass('wcInvisible');
				}
			}

			if (element.hasClass('wcSpamReportButton')) {
				if (options.rights.spamReport == true) {
					element.removeClass('wcInvisible');
				} else {
					element.addClass('wcInvisible');
				}
			}

			element.attr('data-admin-actions-processed', '1');
		});
	}

	function setMessagesProperty(data) {
		container.find('div[data-chat-user-id="' + data.chatUserId + '"]').each(function(index, element) {
			if (data.propertyName == 'textColor') {
				if (jQuery.inArray('message', options.textColorAffectedParts) !== -1) {
					jQuery(element).find('.wcMessageContent').css({color: data.propertyValue});
				}
				if (jQuery.inArray('messageUserName', options.textColorAffectedParts) !== -1) {
					jQuery(element).find('.wcMessageUser, .wcMessageUser a').css({color: data.propertyValue});
				}
			}
		});
	}

	function replaceUserNameInMessages(renderedUserName, messagesIds) {
		for (var t = 0; t < messagesIds.length; t++) {
			container.find('div[data-id="' + messagesIds[t] + '"] .wcMessageUser').html(renderedUserName);
		}
	}

	function requestMessageRefresh(messageId) {
		if (container.find('div[data-id="' + messageId + '"]').length > 0) {
			transceiver.getMessage(messageId, options.channelId, options.checksum);
		}
	}

	function show() {
		container.removeClass('wcInvisible');
	}

	function hide() {
		container.addClass('wcInvisible');
	}

	/**
	 * @returns {boolean}
	 */
	function isVisible() {
		return container.length > 0 && !container.hasClass('wcInvisible');
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

	function setHeight(value) {
		container.css('height', value + 'px');
	}

	function getWidth() {
		return container.length > 0 && !container.hasClass('wcInvisible') ? container.outerWidth() : 0;
	}

	function getHeight() {
		return container.length > 0 && !container.hasClass('wcInvisible') ? container.outerHeight() : 0;
	}

	// maintenance events:
	maintenance.$.bind('deleteMessage', function(event, data) {
		hideMessage(data.id);
	});
	maintenance.$.bind('deleteMessages', function(event, data) {
		for (var x = 0; x < data.ids.length; x++) {
			hideMessage(data.ids[x]);
		}
	});
	maintenance.$.bind('deleteAllMessagesFromChannel', function(event, data) {
		if (data.channelId == options.channelId) {
			hideAllMessages();
		}
	});
	maintenance.$.bind('deleteAllMessages', function(event, data) {
		hideAllMessages();
	});
	maintenance.$.bind('replaceUserNameInMessages', function(event, data) {
		replaceUserNameInMessages(data.renderedUserName, data.messagesIds);
	});
	maintenance.$.bind('setMessagesProperty', function(event, data) {
		setMessagesProperty(data);
	});
	maintenance.$.bind('refreshMessage', function(event, data) {
		requestMessageRefresh(data.id);
	});

	// transceiver events:
	transceiver.$.bind('messageArrived', function(event, message) {
		var sourceElement = container.find('div[data-id="' + message.id + '"]');
		if (sourceElement.length > 0 && !sourceElement.data('no-refresh')) {
			var newElement = jQuery(message.text).hide();
			sourceElement.replaceWith(newElement);
			newElement.fadeIn(400, function() {
				scrollMessages();
			});
		}
	});

	function onMessageApprove(e) {
		e.preventDefault();

		if (options.enableApprovalConfirmation && !confirm('Are you sure you want to approve this message?')) {
			return;
		}

		var approveButton = jQuery(this);
		var messageId = approveButton.data('id');

		jQuery.ajax({
				type: "POST",
				url: messageApproveEndpoint,
				data: {
					channelId: options.channelId,
					messageId: messageId,
					checksum: options.checksum
				}
			})
			.done(function() {
				if (options.approvingMessagesMode == 2) {
					hideMessage(messageId);
				} else {
					approveButton.addClass('wcInvisible');
					approveButton.closest('.wcMessage').removeClass('wcMessageHidden');
					approveButton.closest('.wcMessage').data('no-refresh', 1);
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[onMessageApprove] ' + jqXHR.responseText);
				logger.logError('Server error: ' + errorThrown);
			});
	}

	function onMessageDelete(e) {
		e.preventDefault();

		if (!confirm('Are you sure you want to delete this message?')) {
			return;
		}

		var deleteButton = jQuery(this);
		var messageId = deleteButton.data('id');
		jQuery.ajax({
				type: "POST",
				url: messageDeleteEndpoint,
				data: {
					channelId: options.channelId,
					messageId: messageId,
					checksum: options.checksum
				}
			})
			.done(function() {
				hideMessage(messageId);
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[onMessageDelete] ' + jqXHR.responseText);
				logger.logError('Server error: ' + errorThrown);
			});
	}

	function onMessageEdit(e) {
		e.preventDefault();

		var editButton = jQuery(this);

		editButton.closest('.wcMessage').find('.wcMessageContentInternal').prop('contentEditable', true).focus();
		editButton.closest('.wcMessage').find('.wcAdminAction').addClass('wcAdminActionInvisibleOnEdit');
		editButton.closest('.wcMessage').addClass('wcMessageEditing');

		editButton.after(jQuery(
			'<a href="javascript://" class="wcAdminAction wcAdminActionSpaceAfter wcEditedMessageSaveButton" title="Save the message"></a>'
		));
		editButton.after(jQuery(
			'<a href="javascript://" class="wcAdminAction wcEditedMessageCancelButton" title="Cancel"></a>'
		));

		editingMessagesCache[editButton.closest('.wcMessage').data('id')] = editButton.closest('.wcMessage').find('.wcMessageContentInternal').html();

		// disable images resizing in Mozilla:
		if (jQuery.browser.mozilla) {
			document.execCommand("enableObjectResizing", null, false);
		}
	}

	function onMessageEditSave(e) {
		e.preventDefault();

		var buttonContainer = jQuery(this).closest('.wcMessage');
		var messageId = buttonContainer.data('id');

		// check message characters limit:
		var nodes = textNodesUnder(buttonContainer.find('.wcMessageContentInternal')[0]);
		var totalLength = 0;
		for (var i = 0; i < nodes.length; i++) {
			totalLength += nodes[i].length;
		}
		if (totalLength > options.messageMaxLength) {
			alert(options.messages.messageError13);
			return;
		}

		jQuery.ajax({
				type: "POST",
				url: messageSaveEndpoint,
				data: {
					channelId: options.channelId,
					messageId: messageId,
					content: buttonContainer.find('.wcMessageContentInternal').html(),
					checksum: options.checksum
				}
			})
			.done(function() {
				buttonContainer.find('.wcMessageContentInternal').prop('contentEditable', false);
				buttonContainer.find('.wcEditedMessageSaveButton, .wcEditedMessageCancelButton').remove();
				buttonContainer.find('.wcAdminActionInvisibleOnEdit').removeClass('wcAdminActionInvisibleOnEdit');
				buttonContainer.removeClass('wcMessageEditing');

				delete editingMessagesCache[buttonContainer.data('id')];
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[onMessageEditSave] ' + jqXHR.responseText);
				logger.logError('Server error: ' + errorThrown);
			});
	}

	function textNodesUnder(node) {
		var all = [];

		for (node = node.firstChild; node; node = node.nextSibling) {
			if (node.nodeType == 3) {
				all.push(node);
			} else {
				all = all.concat(textNodesUnder(node));
			}
		}

		return all;
	}

	function onMessageEditCancel(e) {
		e.preventDefault();

		var buttonContainer = jQuery(this).closest('.wcMessage');

		buttonContainer.find('.wcMessageContentInternal').prop('contentEditable', false);
		buttonContainer.find('.wcEditedMessageSaveButton, .wcEditedMessageCancelButton').remove();
		buttonContainer.find('.wcMessageEditButton').removeClass('wcInvisible');
		buttonContainer.find('.wcAdminActionInvisibleOnEdit').removeClass('wcAdminActionInvisibleOnEdit');
		buttonContainer.removeClass('wcMessageEditing');

		buttonContainer.find('.wcMessageContentInternal').html(editingMessagesCache[buttonContainer.data('id')]);
		delete editingMessagesCache[buttonContainer.data('id')];
	}

	function onUserBan(e) {
		e.preventDefault();

		if (!confirm('Are you sure you want to ban this user?')) {
			return;
		}

		var messageId = jQuery(this).data('id');
		jQuery.ajax({
				type: "POST",
				url: userBanEndpoint,
				data: {
					channelId: options.channelId,
					messageId: messageId,
					checksum: options.checksum
				}
			})
			.done(function(result) {
				try {
					var response = result;
					if (response.error) {
						logger.logError(response.error);
					}
				}
				catch (e) {
					logger.logError('Server error: ' + e.toString());
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[onUserBan] ' + jqXHR.responseText);
				logger.logError('Server error occurred: ' + errorThrown);
			});
	}

	function onUserKick(e) {
		e.preventDefault();

		if (!confirm('Are you sure you want to kick this user?')) {
			return;
		}

		var messageId = jQuery(this).data('id');
		jQuery.ajax({
				type: "POST",
				url: userKickEndpoint,
				data: {
					channelId: options.channelId,
					messageId: messageId,
					checksum: options.checksum
				}
			})
			.done(function(result) {
				try {
					var response = result;
					if (response.error) {
						logger.logError(response.error);
					}
				}
				catch (e) {
					logger.logError('Server error: ' + e.toString());
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[onUserKick] ' + jqXHR.responseText);
				logger.logError('Server error occurred: ' + errorThrown);
			});
	}

	function onSpamReportClick(e) {
		e.preventDefault();

		if (!confirm(options.messages.messageSpamReportQuestion)) {
			return;
		}

		var messageId = jQuery(this).data('id');
		jQuery.ajax({
				type: "POST",
				url: spamReportEndpoint,
				data: {
					channelId: options.channelId,
					messageId: messageId,
					url: wisechat.core.globals.window.location.href,
					checksum: options.checksum
				}
			})
			.done(function(result) {
				try {
					var response = result;
					if (response.error) {
						logger.logError(response.error);
					}
				}
				catch (e) {
					logger.logError('Server error: ' + e.toString());
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				logger.logDebug('[onSpamReport] ' + jqXHR.responseText);
				logger.logError('Server error occurred: ' + errorThrown);
			});
	}

	function onUserNameClick() {
		var userName = jQuery(this).text().replace(/^\s+|\s+$/g, '');
		controls.appendTextToInput('@' + userName + ': ');
	}

	/**
	 * Converts pasted HTML text to the plain text.
	 *
	 * @param e
	 */
	function onContentEditablePaste(e) {
		e.preventDefault();

		var text = '';
		if (e.clipboardData || e.originalEvent.clipboardData) {
			text = (e.originalEvent || e).clipboardData.getData('text/plain');
		} else if (wisechat.core.globals.window.clipboardData) {
			text = wisechat.core.globals.window.clipboardData.getData('Text');
		}

		if (document.queryCommandSupported('insertText')) {
			// Browsers: Edge, Chrome, Firefox, Safari
			document.execCommand('insertText', false, text);
		} else {
			// Browsers: IE 11
			var selection = null;
			if (wisechat.core.globals.window.getSelection) {
				selection = wisechat.core.globals.window.getSelection();
			} else {
				if (console) {
					console.error('getSelection unsupported');
				}
				return;
			}

			var range = selection.getRangeAt(0);
			range.deleteContents();

			var textNode = document.createTextNode(text);
			range.insertNode(textNode);
			range.selectNodeContents(textNode);
			range.collapse(false);

			var selection = wisechat.core.globals.window.getSelection();
			selection.removeAllRanges();
			selection.addRange(range);
		}
	};

	/**
	 * Prevent element from receiving dragged content.
	 *
	 * @param {Event} e
	 * @returns {boolean}
	 */
	function onDragOverAndDrop(e) {
		e.preventDefault();
		return false;
	};

	// messages actions:
	container.on('click', 'a.wcMessageApproveButton', onMessageApprove);
	container.on('click', 'a.wcMessageDeleteButton', onMessageDelete);
	container.on('click', 'a.wcMessageEditButton', onMessageEdit);
	container.on('click', 'a.wcEditedMessageSaveButton', onMessageEditSave);
	container.on('click', 'a.wcEditedMessageCancelButton', onMessageEditCancel);
	container.on('paste', '[contenteditable]', onContentEditablePaste);
	container.on('dragover drop', '*', onDragOverAndDrop);
	container.on('click', 'a.wcUserKickButton', onUserKick);
	container.on('click', 'a.wcUserBanButton', onUserBan);
	container.on('click', 'a.wcMessageUserReplyTo', onUserNameClick);
	container.on('click', 'a.wcSpamReportButton', onSpamReportClick);

	// public API:
	this.scrollMessages = scrollMessages;
	this.refresh = refresh;
	this.showMessage = showMessage;
	this.showPlainMessage = showPlainMessage;
	this.getContainer = getContainer;
	this.refreshMessages = refreshMessages;
	this.refreshMessagesControls = refreshMessagesControls;
	this.getContainer = getContainer;
	this.show = show;
	this.hide = hide;
	this.isVisible = isVisible;
	this.setBottom = setBottom;
	this.setRight = setRight;
	this.getWidth = getWidth;
	this.setWidth = setWidth;
	this.getHeight = getHeight;
	this.setHeight = setHeight;
};

/**
 * Controls class. Represents controls set for typing and sending messages.
 *
 * @param {wisechat.ui.Window} chatWindow
 * @param {jQuery} container Container element for displaying messages
 * @constructor
 */
wisechat.ui.Controls = function(chatWindow, container) {
	var INPUT_COMPACT_MODE_MAX_WIDTH = 280;

	var $this = jQuery(this);
	var options = chatWindow.options;
	var transceiver = chatWindow.transceiver;
	var maintenance = chatWindow.maintenance;
	var settings = chatWindow.settings;
	var logger = chatWindow.logger;

	var progressBar = new wisechat.ui.ProgressBar(options, container.find('.wcMainProgressBar'));
	var attachments = new wisechat.ui.MessageAttachments(options, container, progressBar);
	var emoticonsPanel = new wisechat.ui.EmoticonsPanel(options, container.find('.wcInsertEmoticonButton'), container);
	var inputContainer = container.find('.wcInputContainer');
	var messageAttachments = container.find('.wcMessageAttachments');
	var messagesInput = container.find('.wcInput');
	var currentUserName = container.find('.wcCurrentUserName');
	var submitButton = container.find('.wcSubmitButton');
	var isMessageMultiline = options.multilineSupport;
	var messageCustomParameters = {};

	function setBusyState(showProgress) {
		submitButton.attr({
			disabled: '1',
			readonly: '1'
		});
		messagesInput.attr({
			placeholder: wisechat.utils.htmlUtils.decodeEntities(options.messages.message_sending),
			readonly: '1'
		});
		if (showProgress == true) {
			progressBar.show();
			progressBar.setValue(0);
		}
	}

	function setIdleState() {
		submitButton.attr({
			disabled: null,
			readonly: null
		});
		messagesInput.attr({
			placeholder: wisechat.utils.htmlUtils.decodeEntities(options.messages.hint_message),
			readonly: null
		});
		progressBar.hide();
	}

	function onSendMessageSuccess(response) {
		setIdleState();
		messagesInput.blur();
		messagesInput.focus();

		// show a proper message when posted message is hidden:
		if (typeof response.message !== 'undefined') {
			if (response.message.hidden) {
				logger.logInfo(options.messages.messageInfo3);
			}
		}
	}

	function onSendMessageError(errorMessage) {
		setIdleState();
		logger.logError(errorMessage);
	}

	function onSendMessageProgress(progressValue) {
		progressBar.setValue(progressValue);
	}

	function sendMessage() {
		var message = messagesInput.val().replace(/^\s+|\s+$/g, '');
		if (message == '[debug]') {
			logger.showDebug();
			messagesInput.val('');
			return;
		}

		var selectedAttachments = attachments.getAttachments();
		attachments.clearAttachments();

		if (message.length > 0 || selectedAttachments.length > 0) {
			setBusyState(selectedAttachments.length > 0);
			transceiver.sendMessage(message, selectedAttachments, messageCustomParameters, onSendMessageSuccess, onSendMessageProgress, onSendMessageError);
			messagesInput.val('');
			if (!isMessageMultiline) {
				switchToSingle();
			} else {
				fitMultilineTextInput();
			}

			// add message to the history:
			if (!isMessageMultiline && message.length > 0) {
				wisechat.utils.messagesHistory.resetPointer();
				if (wisechat.utils.messagesHistory.getPreviousMessage() != message) {
					wisechat.utils.messagesHistory.addMessage(message);
				}
				wisechat.utils.messagesHistory.resetPointer();
			}
		}
	}

	function switchToMultiline() {
		// check if it was executed already:
		if (messagesInput.is('textarea')) {
			return;
		}

		// create new textarea and put a message into it:
		var textarea = jQuery('<textarea />');
		textarea.hide();
		textarea.addClass('wcInput');
		textarea.attr('placeholder', wisechat.utils.htmlUtils.decodeEntities(options.messages.hint_message));
		textarea.attr('maxlength', options.messageMaxLength);
		textarea.css('overflow-y', 'hidden');
		textarea.keypress(onMessageInputKeyPress);
		textarea.val(messagesInput.val() + "\n");

		// remove single-lined input:
		messagesInput.after(textarea);
		messagesInput.off('keypress');
		messagesInput.off('keydown');
		messagesInput.remove();

		textarea.show();
		textarea.focus();
		messagesInput = textarea;
	}

	function switchToSingle() {
		// check if it was executed already:
		if (messagesInput.is('input')) {
			return;
		}

		// create new textarea and put a message into it:
		var input = jQuery('<input />');
		input.hide();
		input.addClass('wcInput');
		input.attr('placeholder', wisechat.utils.htmlUtils.decodeEntities(options.messages.hint_message));
		input.attr('maxlength', options.messageMaxLength);
		input.attr('type', 'text');
		input.attr('title', wisechat.utils.htmlUtils.decodeEntities(options.messages.messageInputTitle));
		input.keypress(onMessageInputKeyPress);
		input.keydown(onMessageInputKeyDown);

		// remove single-lined input:
		messagesInput.after(input);
		messagesInput.off('keypress');
		messagesInput.remove();

		input.show();
		input.focus();
		messagesInput = input;

		// Safari fix - unknown new line appears, should be cleared:
		setTimeout(function () {
			messagesInput.val('');
		}, 200);
	}

	function fitMultilineTextInput() {
		var lines = messagesInput.val().replace(/^\s+|\s+$/g, '').split("\n").length;
		var lineHeight = parseInt(messagesInput.css('line-height'));
		lines++;
		if (lines > 0 && !isNaN(lineHeight) && lineHeight > 0) {
			messagesInput.css('height', (lines * lineHeight + 10) + 'px');
		}
	}

	function onMessageInputKeyPress(e) {
		if (e.which == 13) {
			if (e.shiftKey) {
				if (!isMessageMultiline) {
					switchToMultiline();
				}
				fitMultilineTextInput();

				// move cursor to the end:
				if (!isMessageMultiline) {
					messagesInput.focus();
					var text = messagesInput.val();
					messagesInput.val('');
					messagesInput.val(text);
					messagesInput.focus();
				}
			} else {
				sendMessage();
			}
		}
	}

	function onAppendTextToInputRequest(e, text) {
		messagesInput.val(messagesInput.val() + text);
		messagesInput.focus();
	}

	function onMessageInputKeyDown(e) {
		if (!isMessageMultiline) {
			var keyCode = e.which;
			var messageCandidate = null;

			if (keyCode == 38) {
				messageCandidate = wisechat.utils.messagesHistory.getPreviousMessage();
			} else if (keyCode == 40) {
				messageCandidate = wisechat.utils.messagesHistory.getNextMessage();
			}
			if (messageCandidate !== null) {
				messagesInput.val(messageCandidate);
			}
		}
	}

	function onSettingsUserNameChange(event, userName) {
		currentUserName.html(userName + ':');
	}

	function setMessageCustomParameters(params) {
		messageCustomParameters = params;
	}

	function onWindowResize() {
		// logic for input compact mode on narrow screens:
		var totalButtonsWidth = 0;
		container.find('.wcSubmitButton, .wcToolButton').each(function(index) {
			totalButtonsWidth += parseInt(jQuery(this).width(), 10);
		});

		if (container.outerWidth() > 0 && totalButtonsWidth > 0 && (container.outerWidth() - totalButtonsWidth) < INPUT_COMPACT_MODE_MAX_WIDTH) {
			if (!container.hasClass('wcInputsCompactMode')) {
				container.addClass('wcInputsCompactMode');
				if (currentUserName.length > 0) {
					inputContainer.insertAfter(currentUserName);
				} else {
					inputContainer.prependTo(container);
				}

				var brElement = jQuery('<br />').addClass('wcClear wcClearForCompactMode');
				if (messageAttachments.length > 0) {
					brElement.insertBefore(messageAttachments);
				} else {
					container.append(brElement);
				}
			}
		} else {
			if (container.hasClass('wcInputsCompactMode')) {
				container.removeClass('wcInputsCompactMode');
				if (messageAttachments.length > 0) {
					inputContainer.insertBefore(messageAttachments);
				} else {
					container.append(inputContainer);
				}
				container.find('.wcClearForCompactMode').remove();
			}
		}
	}

	function focus() {
		messagesInput.focus();
	}

	/**
	 * @returns {Boolean}
	 */
	function isFocused() {
		return messagesInput.is(":focus");
	}

	function onInputFocus() {
		$this.trigger('inputFocus', []);
	}

	function show() {
		container.removeClass('wcInvisible');
	}

	function hide() {
		container.addClass('wcInvisible');
	}

	function isVisible() {
		return container.length > 0 && !container.hasClass('wcInvisible');
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

	function getHeight() {
		return container.length > 0 && !container.hasClass('wcInvisible') ? container.outerHeight() : 0;
	}

	function getWidth() {
		return container.length > 0 && !container.hasClass('wcInvisible') ? container.outerWidth() : 0;
	}

	/**
	 * Refreshes the container.
	 */
	function refresh() {
		onWindowResize();
	}

	// maintenance events:
	maintenance.$.bind('refreshPlainUserName', function(event, data) {
		currentUserName.html(data.name + ':');
	});

	// events:
	emoticonsPanel.$.bind('emoticonSelected', onAppendTextToInputRequest);
	settings.$.bind('userNameChange', onSettingsUserNameChange);
	messagesInput.keydown(onMessageInputKeyDown);
	messagesInput.keypress(onMessageInputKeyPress);
	messagesInput.focus(onInputFocus);
	submitButton.click(sendMessage);
	jQuery(window).resize(onWindowResize).trigger('resize');

	// public API:
	this.$ = $this;
	this.appendTextToInput = function(text) {
		onAppendTextToInputRequest(null, text);
	};
	this.setMessageCustomParameters = setMessageCustomParameters;
	this.refresh = refresh;
	this.isFocused = isFocused;
	this.focus = focus;
	this.show = show;
	this.hide = hide;
	this.isVisible = isVisible;
	this.setBottom = setBottom;
	this.setRight = setRight;
	this.setWidth = setWidth;
	this.getHeight = getHeight;
	this.getWidth = getWidth;
};

/**
 * UsersList class.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} container Container element for displaying messages
 * @param {jQuery} titleContainer Container element for displaying title
 * @param {wisechat.maintenance.MaintenanceExecutor} maintenance
 * @constructor
 */
wisechat.ui.UsersList = function(options, container, titleContainer, maintenance) {
	var objectId = Math.random().toString(36).substr(2, 9);
	var $this = jQuery(this);
	var windowContainer = container.closest('.wcContainer');
	var onTopPosition = false;
	var autoHidePerformed = false;
	var searchBoxInput = container.find('.wcUsersListSearchBox .wcInput');
	var searchBoxCancelButton = container.find('.wcUsersListSearchBox .wcUsersListSearchBoxCancelButton');
	var infoWindows = {};

	function onWindowResize() {
		if (options.showUsersList && options.autoHideUsersList && !onTopPosition && !options.sidebarMode) {
			if (windowContainer.width() < options.autoHideUsersListWidth) {
				hide();
				$this.trigger('autoHide', []);
				autoHidePerformed = true;
			} else {
				unhide();
				$this.trigger('autoShow', []);
			}
		}
	}

	function isHidden() {
		return !container.is(":visible");
	}

	function hide(justTheContainer) {
		if (justTheContainer) {
			container.addClass('wcInvisible');
		} else {
			windowContainer.removeClass('wcUsersListIncluded');
			container.addClass('wcInvisible');
			container.next('.wcClear').hide();
			windowContainer.find('.wcMessages').css('width', 'auto');
		}
	}

	function unhide(justTheContainer) {
		if (justTheContainer) {
			container.removeClass('wcInvisible');
		} else {
			windowContainer.addClass('wcUsersListIncluded');
			container.removeClass('wcInvisible');
			container.next('.wcClear').show();
			windowContainer.find('.wcMessages').css('width', '');
		}
	}

	function setOnTop() {
		if (options.showUsersList) {
			onTopPosition = true;
			unhide();
			windowContainer.find('.wcMessages').hide();
			container.addClass('wcUserListOnTop');
		}
	}

	function clearTopPosition() {
		if (options.showUsersList) {
			onTopPosition = false;
			onWindowResize();
			windowContainer.find('.wcMessages').show();
			container.removeClass('wcUserListOnTop');
		}
	}

	function wasAutoHidePerformed() {
		return autoHidePerformed;
	}

	function getWidth() {
		return container.length > 0 ? container.outerWidth() : 0;
	}

	function getMinMaxButton() {
		return titleContainer.find('.wcUserListMinMaxLink');
	}

	function setHeight(value) {
		if (isTitleVisible() && titleContainer.outerHeight() > 0) {
			value -= titleContainer.outerHeight();
		}
		container.css('height', value + 'px');
	}

	function setTop(value) {
		if (isTitleVisible() && titleContainer.outerHeight() > 0) {
			if (!getMinMaxButton().hasClass('wcUserListMinimized')) {
				titleContainer.css('top', value);
			}
			value += titleContainer.outerHeight();
		}
		container.css("top", value);
	}

	function setTitlePosition(top, bottom) {
		titleContainer.css('top', top);
		titleContainer.css('bottom', bottom);
	}

	function hideTitle() {
		return titleContainer.hide();
	}

	function showTitle() {
		return titleContainer.show();
	}

	function isTitleVisible() {
		return titleContainer.length > 0 && titleContainer.is(":visible");
	}

	function onSearchBoxInputKeyUp(e) {
		if (e.keyCode == 27) {
			searchBoxInput.val('');
		}
		searchBoxCancelButton.toggleClass('wcInvisible', searchBoxInput.val().length == 0);
		filterUsers();
	}

	function onSearchBoxCancelButtonClick(e) {
		e.preventDefault();
		searchBoxInput.val('');
		searchBoxCancelButton.addClass('wcInvisible');
		filterUsers();
	}

	function filterUsers() {
		var phrase = searchBoxInput.length > 0 ? searchBoxInput.val() : '';

		if (phrase.length === 0) {
			container.find('.wcUsersListContainer > a').removeClass('wcInvisible');
			container.find('.wcUsersListContainer > br').removeClass('wcInvisible');
			return;
		}

		container.find('.wcUsersListContainer > a').each(function(index, element) {
			element = jQuery(element);

			if (element.text().match(new RegExp(phrase, "i"))) {
				element.removeClass('wcInvisible');
				element.next('br').removeClass('wcInvisible');
			} else {
				element.addClass('wcInvisible');
				element.next('br').addClass('wcInvisible');
			}
		});
	}

	// maintenance events:
	maintenance.$.bind('refreshUsersList', function(event, data) {
		container.find('.wcUsersListContainer').html(data);
		filterUsers();

		// setup info windows:
		if (options.showUsersListInfoWindows) {
			container.find('.wcUsersListContainer > a').each(function (index, element) {
				element = jQuery(element);

				if (!element.data('info-window') || element.data('info-window').length === 0) {
					return;
				}

				var hash = element.data('hash');
				var infoWindowContent = element.data('info-window');

				element.data('info-window-id', hash);
				if (typeof infoWindows[hash] === 'undefined') {
					infoWindows[hash] = new wisechat.ui.InfoWindow(
						options, infoWindowContent, 'wcUserListInfoWindow wcUserListInfoWindow_' + objectId, container, 'a.wcUserInChannel', hash
					);
				} else {
					infoWindows[hash].updateContent(infoWindowContent);
				}
			});
		}
	});

	// DOM events:
	jQuery(window).resize(onWindowResize).trigger('resize');
	container.on('click', 'a.wcUserInChannel', function() {
		var element = jQuery(this);
		$this.trigger('userClick', [element.data('public-id'), element.data('hash'), element.data('name'), element.hasClass('wcCurrentUser'), element.data('allowed') == '1']);
	});
	container.on('click', 'a.wcMessageUserReplyTo', function() {
		var element = jQuery(this);
		$this.trigger('userReplyToClick', [element.data('name')]);
	});

	// scan for buttons that try to initiate private message:
	jQuery('.wise-chat-send-message').click(function(e) {
		e.preventDefault();
		var userId = jQuery(this).data('user-id');

		var element = container.find('.wcUsersListContainer > a[data-wp-id="' + userId + '"]');
		if (element.length > 0) {
			$this.trigger('userClick', [element.data('public-id'), element.data('hash'), element.data('name'), element.hasClass('wcCurrentUser'), element.data('allowed') == '1']);
		} else {
			alert(options.messages.messageUserNotFoundInChat);
		}
	});
	jQuery('.wise-chat-send-message').each(function(index, element) {
		element = jQuery(element);
		var userId = element.data('user-id');
		if (container.find('.wcUsersListContainer > a[data-wp-id="' + userId + '"]').length === 0) {
			element.addClass('wcUserIsNotInTheChat');
		}
	});
	jQuery('body').on('click', '.wcUserListInfoWindow_' + objectId + ' button.wcUserListInfoWindowPrivateMessageButton', function(e) {
		var userHash = jQuery(e.target).closest('button').data('hash');
		if (userHash) {
			var element = container.find('.wcUsersListContainer > a[data-hash="' + userHash + '"]');
			if (element.length > 0) {
				$this.trigger('userClick', [element.data('public-id'), element.data('hash'), element.data('name'), element.hasClass('wcCurrentUser'), element.data('allowed') == '1']);
			}
		}
	});

	searchBoxInput.keyup(onSearchBoxInputKeyUp);
	searchBoxCancelButton.click(onSearchBoxCancelButtonClick);

	// public API:
	this.$ = $this;
	this.setOnTop = setOnTop;
	this.clearTopPosition = clearTopPosition;
	this.wasAutoHidePerformed = wasAutoHidePerformed;
	this.getWidth = getWidth;
	this.setHeight = setHeight;
	this.getMinMaxButton = getMinMaxButton;
	this.setTop = setTop;
	this.setTitlePosition = setTitlePosition;
	this.hideTitle = hideTitle;
	this.showTitle = showTitle;
	this.isTitleVisible = isTitleVisible;
	this.hide = hide;
	this.unhide = unhide;
	this.isHidden = isHidden;
};

/**
 * Window class.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} container Container element for displaying messages
 * @param {wisechat.core.MessagesTransceiver} transceiver
 * @param {wisechat.maintenance.MaintenanceExecutor} maintenance
 * @param {wisechat.ui.UsersList} usersList
 * @param {wisechat.settings.Settings} settings
 * @param {wisechat.utils.Notifier} notifier
 * @param {wisechat.ui.VisualLogger} logger
 * @constructor
 */
wisechat.ui.Window = function(options, container, transceiver, maintenance, usersList, settings, notifier, logger) {
	var $this = jQuery(this);

	// public fields:
	this.options = options;
	this.maintenance = maintenance;
	this.transceiver = transceiver;
	this.settings = settings;
	this.notifier = notifier;
	this.logger = logger;

	var messagesContainer = container.find('.wcMessages');
	var controlsContainer = container.find('.wcControls');
	var titleContainer = null;
	var controls = new wisechat.ui.Controls(this, controlsContainer);
	var messages = new wisechat.ui.Messages(this, messagesContainer, controls);
	var messagesFilter = function(message) {
		return !message.isPrivate;
	};

	function onTransceiverInitialize(event) {
		messages.refreshMessages(options.nowTime);
		setTimeout(function() {
			messages.refreshMessagesControls();
			messages.scrollMessages();
		}, 2000);
		messages.scrollMessages();
	}

	function onMessagesArrived(event, response) {
		for (var x = 0; x < response.result.length; x++) {
			var message = response.result[x];
			if (messagesFilter(message)) {
				messages.showMessage(message);
				$this.trigger('messageShow', [message]);
			}
		}
	}

	function onHeartBeat(event, response) {
		messages.refreshMessages(response.nowTime);
		messages.refreshMessagesControls();
	}

	function onInsideOutsideClick(event) {
		var target = jQuery(event.target);

		if (target.closest(messagesContainer).length > 0 || target.closest(controlsContainer).length > 0) {
			$this.trigger('clickInside', [event]);
		} else {
			$this.trigger('clickOutside', [event]);
		}
	}

	function onControlsInputFocus(event) {
		$this.trigger('windowFocus', [jQuery(event.target)]);
	}

	function setMessagesFilter(customMessagesFilter) {
		messagesFilter = customMessagesFilter;
	}

	function insertAfter(previousMessagesContainer, previousControlsContainer) {
		previousMessagesContainer.after(messagesContainer);
		previousControlsContainer.after(controlsContainer);
	}

	function hide() {
		messagesContainer.addClass('wcInvisible');
		controlsContainer.addClass('wcInvisible');
	}

	function show() {
		messagesContainer.removeClass('wcInvisible');
		controlsContainer.removeClass('wcInvisible');
	}

	function focus() {
		controls.focus();
	}

	function isFocused() {
		return controls.isFocused();
	}

	function isActive() {
		return titleContainer.hasClass('wcWindowTitleActive');
	}

	function setActive() {
		titleContainer.addClass('wcWindowTitleActive');
	}

	function setInactive() {
		titleContainer.removeClass('wcWindowTitleActive');
	}

	function refresh() {
		messages.scrollMessages();
		controls.refresh();
	}

	function getMessagesContainer() {
		return messagesContainer;
	}

	function getControlsContainer() {
		return controlsContainer;
	}

	/**
	 * @param {jQuery} newTitleContainer
	 */
	function setTitleContainer(newTitleContainer) {
		titleContainer = newTitleContainer;
	}

	function getTitleContainer() {
		return titleContainer;
	}

	function showUnreadMessagesFlag() {
		titleContainer.find('.wcUnreadMessagesFlag').show();
	}

	function hideUnreadMessagesFlag() {
		titleContainer.find('.wcUnreadMessagesFlag').hide();
	}

	/**
	 * @returns {wisechat.ui.Controls}
	 */
	function getControls() {
		return controls;
	}

	/**
	 * @returns {wisechat.ui.Messages}
	 */
	function getMessages() {
		return messages;
	}

	/**
	 * @param {Event} event
	 * @param {String} name
	 */
	function onUserListUserReplyToClick(event, name) {
		controls.appendTextToInput('@' + name + ': ');
	}

	// DOM events:
	jQuery(window).click(onInsideOutsideClick);

	// transceiver events:
	transceiver.$.bind('messagesArrived', onMessagesArrived);
	transceiver.$.bind('heartBeat', onHeartBeat);
	transceiver.$.bind('initialize', onTransceiverInitialize);

	// user list events:
	usersList.$.bind('userReplyToClick', onUserListUserReplyToClick);

	// controls events:
	controls.$.bind('inputFocus', onControlsInputFocus);

	// public API:
	this.$ = $this;
	this.setMessagesFilter = setMessagesFilter;
	this.insertAfter = insertAfter;
	this.setMessageCustomParameters = controls.setMessageCustomParameters;
	this.hide = hide;
	this.show = show;
	this.focus = focus;
	this.isFocused = isFocused;
	this.isActive = isActive;
	this.setActive = setActive;
	this.setInactive = setInactive;
	this.refresh = refresh;
	this.getMessagesContainer = getMessagesContainer;
	this.getControlsContainer = getControlsContainer;
	this.getControls = getControls;
	this.getMessages = getMessages;
	this.setTitleContainer = setTitleContainer;
	this.getTitleContainer = getTitleContainer;
	this.showUnreadMessagesFlag = showUnreadMessagesFlag;
	this.hideUnreadMessagesFlag = hideUnreadMessagesFlag;
};

/**
 * UserListToggleButton class.
 *
 * @param {Object} options Plugin's global options
 * @param {jQuery} container Container of the chat
 * @param {wisechat.ui.UsersList} usersList
 * @constructor
 */
wisechat.ui.UserListToggleButton = function(options, container, usersList) {
	var topControls = container.find('.wcTopControls');
	var toggleButton = container.find('.wcUserListToggle');

	function showButton() {
		topControls.removeClass('wcInvisible');
		toggleButton.removeClass('wcInvisible');
	}

	function hideButton() {
		topControls.addClass('wcInvisible');
		toggleButton.addClass('wcInvisible');
	}

	// show the button if users list has been auto-hidden  (by changing width of the window):
	usersList.$.bind('autoHide', function(event, data) {
		showButton();
	});

	// hide the button if users list has been auto-displayed (by changing width of the window):
	usersList.$.bind('autoShow', function(event, data) {
		hideButton();
	});

	// hide users list if user name on the list has been clicked:
	usersList.$.bind('userClick', function(event) {
		if (toggleButton.hasClass('wcUserListToggleEnabled')) {
			toggleButton.removeClass('wcUserListToggleEnabled');
			usersList.clearTopPosition();
		}
	});

	toggleButton.click(function() {
		if (toggleButton.hasClass('wcUserListToggleEnabled')) {
			toggleButton.removeClass('wcUserListToggleEnabled');
			usersList.clearTopPosition();
		} else {
			toggleButton.addClass('wcUserListToggleEnabled');
			usersList.setOnTop();
		}
	});

	// when auto-hide event was already fired:
	if (usersList.wasAutoHidePerformed()) {
		showButton();
	}
};

/**
 * Wise Chat Sidebar namespace.
 *
 * @author Kainex <contact@kaine.pl>
 * @see https://kaine.pl/projects/wp-plugins/wise-chat-pro
 */

var wisechat = wisechat || {};
wisechat.ui = wisechat.ui || {};

/**
 * SidebarDecorator class.
 *
 * @param {wisechat.utils.Options} chatOptions Plugin's global options
 * @param {jQuery} container Container element for the chat
 * @param {wisechat.ui.UsersList} usersList
 * @param {wisechat.ui.Window} channelWindow
 * @param {wisechat.settings.Settings} settings
 * @param {jQuery} usersCounter
 * @param {wisechat.ui.VisualLogger} logger
 * @param {wisechat.pm.SidebarUI} privateMessagesDecorator
 * @constructor
 */
wisechat.ui.SidebarModeDecorator = function(chatOptions, container, usersList, channelWindow, settings, usersCounter, logger, privateMessagesDecorator) {
	var options = chatOptions.getPlain();
	var localSettings = new wisechat.core.LocalSettings(options.channelId);
	var controls = channelWindow.getControls();
	var messages = channelWindow.getMessages();
	var channelWindowTitle = channelWindow.getTitleContainer();
	var mobileNavigation = container.find('.wcSidebarModeMobileNavigation');
	var currentWindowDisplayed = null;
	var allowWindowOpenReact = false;
	var customizations = settings.getContainer();

	function setup() {
		// container is hidden by default in sidebar mode:
		container.show();

		if (isMobileModeEnabled()) {
			setupMobileMode();
		} else {
			setupRegularMode();
		}
	}

	function setupMobileMode() {
		usersList.hideTitle();
		if (isUsersListEnabled()) {
			setupMobileModeWithUsersList();
		} else {
			setupMobileModeWithoutUsersList();
		}
	}

	function setupRegularMode() {
		if (isUsersListEnabled()) {
			setupRegularModeWithUsersList();
		} else {
			setupRegularModeWithoutUsersList();
		}
	}

	function moveRecentChatIndicatorToMobileNavigation() {
		var indicator = container.find('.wcRecentChatsIndicator');
		if (!indicator.parent().is(mobileNavigation)) {
			indicator.prependTo(mobileNavigation);
		}
	}

	function moveRecentChatIndicatorToUsersListTitle() {
		var indicator = container.find('.wcRecentChatsIndicator');
		var target = container.find('.wcUserListTitle');
		if (!indicator.parent().is(target)) {
			indicator.appendTo(target);
		}
	}

	function setupMobileModeWithUsersList() {
		var isSidebarShow = container.hasClass('wcSidebarModeUsersListTogglerEnabled');
		mobileNavigation.css('bottom', options.fbBottomOffset);

		if (isSidebarShow) {
			usersList.unhide();
			usersCounter.removeClass('wcInvisible');
			customizations.removeClass('wcInvisible');

			var bottomValue = mobileNavigation.outerHeight() !== null ? mobileNavigation.outerHeight() : 0;
			bottomValue += options.fbBottomOffset;

			// customization section position:
			customizations.css({
				bottom: bottomValue,
				right: 0,
				width: usersList.getWidth()
			});
			if (customizations.outerHeight() !== null) {
				bottomValue += customizations.outerHeight()
			}

			// users counter section position:
			usersCounter.css({
				bottom: bottomValue,
				right: 0,
				width: usersList.getWidth()
			});

			// set users list height based on the two previous sections:
			var usersListHeight = jQuery(window).height() - options.fbUsersListTopOffset - mobileNavigation.outerHeight() - options.fbBottomOffset;
			if (customizations.outerHeight() !== null) {
				usersListHeight -= customizations.outerHeight();
			}
			if (usersCounter.outerHeight() !== null) {
				usersListHeight -= usersCounter.outerHeight();
			}
			usersList.setHeight(usersListHeight);
			container.find('.wcUsersList').css("top", options.fbUsersListTopOffset);
		} else {
			usersCounter.addClass('wcInvisible');
			customizations.addClass('wcInvisible');
			usersList.hide();
		}

		if (options.fbDisableChannel) {
			messages.hide();
			controls.hide();
			channelWindowTitle.hide();
		} else {
			// handle channel window according to minimized state:
			if (!isChannelWindowMinimized()) {
				messages.show();
				controls.show();
				channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMinimize);
			} else {
				messages.hide();
				controls.hide();
				channelWindowTitle.find('.wcWindowTitleMinMaxLink').addClass('wcWindowTitleMinimized');
				channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMaximize);
				channelWindow.setInactive();
			}
			channelWindow.hideUnreadMessagesFlag();

			bottomValue = mobileNavigation.outerHeight();
			bottomValue += options.fbBottomOffset;

			// controls section position:
			controls.setBottom(bottomValue);
			controls.setRight(0);
			controls.refresh();
			if (controls.getHeight() > 0) {
				bottomValue += controls.getHeight();
			}

			// messages section position:
			messages.setBottom(bottomValue);
			messages.setRight(0);
			var effectiveChatHeight = chatOptions.getEffectiveChatHeightBasedOnPercentageHeight();
			if (effectiveChatHeight !== null) {
				effectiveChatHeight -= (controls.getHeight() + channelWindowTitle.outerHeight() + mobileNavigation.outerHeight());
				messages.setHeight(effectiveChatHeight);
			}
			if (messages.getHeight() > 0) {
				bottomValue += messages.getHeight();
			}

			// window title section position:
			channelWindowTitle.removeClass('wcInvisible');
			channelWindowTitle.css('right', 0);
			channelWindowTitle.css('width', '100%');
			channelWindowTitle.css('bottom', bottomValue);
		}

		// logger section position:
		logger.setRight(0);
		logger.setBottom(options.fbBottomOffset);
		logger.setWidth(jQuery(window).width());

		moveRecentChatIndicatorToMobileNavigation();
	}

	function setupMobileModeWithoutUsersList() {
		var bottomValue = options.fbBottomOffset;
		usersList.hide();

		// handle channel window according to minimized state:
		if (!isChannelWindowMinimized()) {
			usersCounter.removeClass('wcInvisible');
			customizations.removeClass('wcInvisible');

			// customization section position:
			customizations.css({
				bottom: bottomValue,
				right: 0,
				width: '100%'
			});
			if (customizations.outerHeight() !== null) {
				bottomValue += customizations.outerHeight()
			}

			// users counter section position:
			usersCounter.css({
				bottom: bottomValue,
				right: 0,
				width: '100%'
			});
			if (usersCounter.outerHeight() !== null) {
				bottomValue += usersCounter.outerHeight()
			}

			messages.show();
			controls.show();
			channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMinimize);
		} else {
			usersCounter.addClass('wcInvisible');
			customizations.addClass('wcInvisible');
			messages.hide();
			controls.hide();
			channelWindowTitle.find('.wcWindowTitleMinMaxLink').addClass('wcWindowTitleMinimized');
			channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMaximize);
			channelWindow.setInactive();
		}
		channelWindow.hideUnreadMessagesFlag();

		// controls section position:
		controls.setBottom(bottomValue);
		controls.setRight(0);
		controls.refresh();
		if (controls.getHeight() > 0) {
			bottomValue += controls.getHeight();
		}

		// messages section position:
		messages.setBottom(bottomValue);
		messages.setRight(0);
		var effectiveChatHeight = chatOptions.getEffectiveChatHeightBasedOnPercentageHeight();
		if (effectiveChatHeight !== null) {
			effectiveChatHeight -= (controls.getHeight() + channelWindowTitle.outerHeight() + usersCounter.outerHeight() + customizations.outerHeight());
			messages.setHeight(effectiveChatHeight);
		}
		if (messages.getHeight() > 0) {
			bottomValue += messages.getHeight();
		}

		// window title section position:
		channelWindowTitle.removeClass('wcInvisible');
		channelWindowTitle.css('right', 0);
		channelWindowTitle.css('width', '100%');
		channelWindowTitle.css('bottom', bottomValue);

		// logger section position:
		logger.setRight(0);
		logger.setBottom(options.fbBottomOffset);
		logger.setWidth(jQuery(window).width());

		moveRecentChatIndicatorToMobileNavigation();
	}

	function setupRegularModeWithUsersList() {
		usersList.showTitle();

		// customization section position:
		customizations.css({
			bottom: options.fbBottomOffset,
			right: 0,
			width: usersList.getWidth()
		});

		// users counter section position:
		usersCounter.css({
			bottom: options.fbBottomOffset,
			right: 0,
			width: usersList.getWidth()
		});
		if (customizations.outerHeight() !== null) {
			usersCounter.css('bottom', customizations.outerHeight() + options.fbBottomOffset);
		}

		// set users list height based on the two previous sections:
		var usersListHeight = jQuery(window).height() - options.fbUsersListTopOffset - options.fbBottomOffset;
		if (customizations.outerHeight() !== null) {
			usersListHeight -= customizations.outerHeight();
		}
		if (usersCounter.outerHeight() !== null) {
			usersListHeight -= usersCounter.outerHeight();
		}
		usersList.setHeight(usersListHeight);
		usersList.setTop(options.fbUsersListTopOffset);

		if (options.fbDisableChannel) {
			messages.hide();
			controls.hide();
			channelWindowTitle.hide();
		} else {

			// handle channel window according to minimized state:
			if (!isChannelWindowMinimized()) {
				messages.show();
				controls.show();
				channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMinimize);
			} else {
				messages.hide();
				controls.hide();
				channelWindowTitle.find('.wcWindowTitleMinMaxLink').addClass('wcWindowTitleMinimized');
				channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMaximize);
				channelWindow.setInactive();
			}
			channelWindow.hideUnreadMessagesFlag();

			// controls section position:
			controls.setBottom(options.fbBottomOffset);
			controls.setRight(usersList.getWidth());
			if (messages.getWidth() > 0) {
				// write-only mode case:
				controls.setWidth(messages.getWidth());
			}
			controls.refresh();

			// messages section position and height:
			messages.setBottom((controls.getHeight() > 0 ? controls.getHeight() : 0) + options.fbBottomOffset); // read-only mode case
			messages.setRight(usersList.getWidth());
			var effectiveChatHeight = chatOptions.getEffectiveChatHeightBasedOnPercentageHeight();
			if (effectiveChatHeight !== null) {
				effectiveChatHeight -= (controls.getHeight() + channelWindowTitle.outerHeight());
				messages.setHeight(effectiveChatHeight);
			}

			// window title section position:
			channelWindowTitle.removeClass('wcInvisible');
			channelWindowTitle.css('right', usersList.getWidth());
			if (messages.getWidth() > 0) {
				channelWindowTitle.css('width', messages.getWidth());
			} else if (controls.getWidth() > 0) {
				channelWindowTitle.css('width', controls.getWidth());
			} else if (channelWindow.getMessagesContainer().outerWidth() > 0) {
				channelWindowTitle.css('width', channelWindow.getMessagesContainer().outerWidth());
			} else if (channelWindow.getControlsContainer().outerWidth() > 0) {
				channelWindowTitle.css('width', channelWindow.getControlsContainer().outerWidth());
			}

			var channelWindowTitleBottom = options.fbBottomOffset;
			if (messages.getHeight() > 0) {
				channelWindowTitleBottom += messages.getHeight();
			}
			if (controls.getHeight() > 0) {
				channelWindowTitleBottom += controls.getHeight();
			}
			channelWindowTitle.css('bottom', channelWindowTitleBottom);
		}

		// logger section position:
		logger.setRight(0);
		logger.setBottom(options.fbBottomOffset);
		logger.setWidth(usersList.getWidth());

		moveRecentChatIndicatorToUsersListTitle();
	}

	function setupRegularModeWithoutUsersList() {
		// handle channel window according to minimized state:
		if (!isChannelWindowMinimized()) {
			messages.show();
			controls.show();
			channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMinimize);
		} else {
			messages.hide();
			controls.hide();
			channelWindowTitle.find('.wcWindowTitleMinMaxLink').addClass('wcWindowTitleMinimized');
			channelWindowTitle.find('.wcWindowTitleMinMaxLink').attr('title', options.messages.messageMaximize);
			channelWindow.setInactive();
		}
		channelWindow.hideUnreadMessagesFlag();

		// position controls:
		controls.setRight(0);
		if (messages.getWidth() > 0) {
			// write-only mode case:
			controls.setWidth(messages.getWidth());
		}
		controls.refresh();

		// position messages panel:
		messages.setRight(0);
		var effectiveChatHeight = chatOptions.getEffectiveChatHeightBasedOnPercentageHeight();
		if (effectiveChatHeight !== null) {
			effectiveChatHeight -= (controls.getHeight() + channelWindowTitle.outerHeight() + usersCounter.outerHeight() + customizations.outerHeight());
			messages.setHeight(effectiveChatHeight);
		}

		// position window title:
		channelWindowTitle.removeClass('wcInvisible');
		channelWindowTitle.css('right', 0);

		// calculate common width:
		var commonWidth = channelWindowTitle.outerWidth() > 250 ? channelWindowTitle.outerWidth() : 250;
		if (messages.getWidth() > 0) {
			commonWidth = messages.getWidth();
		} else if (controls.getWidth() > 0) {
			commonWidth = controls.getWidth();
		}

		channelWindowTitle.css('width', commonWidth);

		// customization section position:
		customizations.css({
			bottom: options.fbBottomOffset,
			right: 0,
			width: commonWidth
		});

		// counter:
		usersCounter.css({
			right: 0,
			width: commonWidth
		});

		// logger section position:
		logger.setRight(0);
		logger.setBottom(options.fbBottomOffset);
		logger.setWidth(commonWidth);

		// set bottom values for each section:
		var bottomValue = options.fbBottomOffset;
		if (customizations.outerHeight() !== null) {
			bottomValue += customizations.outerHeight();
		}
		usersCounter.css('bottom', bottomValue);

		if (usersCounter.outerHeight() !== null) {
			bottomValue += usersCounter.outerHeight();
		}
		controls.setBottom(bottomValue);

		if (controls.isVisible()) {
			bottomValue += controls.getHeight();
		}
		messages.setBottom(bottomValue);

		if (messages.isVisible()) {
			bottomValue += messages.getHeight();
		}
		channelWindowTitle.css('bottom', bottomValue);

		moveRecentChatIndicatorToUsersListTitle();
	}

	function onChannelWindowTitleClick(event) {
		if (isChannelWindowMinimized()) {
			localSettings.set('channelWindowMinimized', false);
			jQuery(this).find('.wcWindowTitleMinMaxLink').removeClass('wcWindowTitleMinimized');
			setup();
			channelWindow.hideUnreadMessagesFlag();
			channelWindow.setActive();
			channelWindow.focus();
			channelWindow.refresh();
		} else {
			localSettings.set('channelWindowMinimized', true);
			jQuery(this).find('.wcWindowTitleMinMaxLink').addClass('wcWindowTitleMinimized');
			setup();
			event.stopPropagation();
		}
	}

	function onChannelWindowInsideClick(event, originalEvent) {
		channelWindow.setActive();
		channelWindow.hideUnreadMessagesFlag();
	}

	function onChannelWindowOutsideClick(event, originalEvent) {
		if (jQuery(originalEvent.target).closest(channelWindowTitle).length > 0 && !isChannelWindowMinimized() || channelWindow.isFocused()) {
			channelWindow.setActive();
		} else {
			channelWindow.setInactive();
		}
	}

	function onSidebarModeUsersListTogglerClick() {
		container.toggleClass('wcSidebarModeUsersListTogglerEnabled');
		setup();
		refreshWindows();
	}

	function refreshWindows() {
		if (privateMessagesDecorator === null || currentWindowDisplayed === null) {
			return;
		}

		var pmWindows = privateMessagesDecorator.getWindows();
		var pmWindowsArray = [];
		if (!options.fbDisableChannel) {
			pmWindowsArray.push(channelWindow);
		}

		for (var hash in pmWindows) {
			pmWindowsArray.push(pmWindows[hash]);
		}

		for (var x = 0; x < pmWindowsArray.length; x++) {
			var theWindow = pmWindowsArray[x];
			if (theWindow == currentWindowDisplayed) {
				theWindow.getMessagesContainer().css({
					'right': 0,
					'width': '100%'
				});
				theWindow.getControlsContainer().css({
					'right': 0,
					'width': '100%'
				});
				theWindow.getTitleContainer().css({
					'right': 0,
					'width': '100%'
				});
			} else {
				theWindow.getMessagesContainer().css('right', 10 * theWindow.getMessagesContainer().outerWidth());
				theWindow.getControlsContainer().css('right', 10 * theWindow.getControlsContainer().outerWidth());
				theWindow.getTitleContainer().css('right', 10 * theWindow.getTitleContainer().outerWidth());
			}
		}

		if (getLeftWindow() !== null) {
			container.find('.wcSidebarModeWindowsNavigationLeft').removeClass('wcInvisible');
		} else {
			container.find('.wcSidebarModeWindowsNavigationLeft').addClass('wcInvisible');
		}

		if (getRightWindow() !== null) {
			container.find('.wcSidebarModeWindowsNavigationRight').removeClass('wcInvisible');
		} else {
			container.find('.wcSidebarModeWindowsNavigationRight').addClass('wcInvisible');
		}

	}



	function getLeftWindow() {
		if (privateMessagesDecorator === null) {
			return null;
		}

		var pmWindows = privateMessagesDecorator.getWindows();
		var prevWindow = null;
		var prevWindowProposal = null;
		var pmWindowsArray = [];
		for (var hash in pmWindows) {
			pmWindowsArray.push(pmWindows[hash]);
		}

		if (!jQuery.isEmptyObject(pmWindows)) {
			if (currentWindowDisplayed === channelWindow) {
				for (var hash in pmWindows) {
					prevWindowProposal = pmWindows[hash];
					if (!prevWindowProposal.getTitleContainer().hasClass('wcInvisible')) {
						prevWindow = prevWindowProposal;
						break;
					}
				}
			} else {
				for (var x = 0; x < pmWindowsArray.length; x++) {
					if (currentWindowDisplayed === pmWindowsArray[x] && x < (pmWindowsArray.length - 1)) {
						prevWindowProposal = pmWindowsArray[x + 1];
						if (!prevWindowProposal.getTitleContainer().hasClass('wcInvisible')) {
							prevWindow = prevWindowProposal;
							break;
						}
					}
				}
			}
		}

		return prevWindow;
	}

	function getRightWindow() {
		if (privateMessagesDecorator === null) {
			return null;
		}

		var pmWindows = privateMessagesDecorator.getWindows();
		var nextWindow = null;
		var nextWindowProposal = null;
		var pmWindowsArray = [];
		for (var hash in pmWindows) {
			pmWindowsArray.push(pmWindows[hash]);
		}

		if (!jQuery.isEmptyObject(pmWindows)) {
			for (var x = pmWindowsArray.length - 1; x >= 0 ; x--) {
				if (currentWindowDisplayed === pmWindowsArray[x]) {
					if (x > 0) {
						for (var y = x - 1; y >= 0 ; y--) {
							nextWindowProposal = pmWindowsArray[y];
							if (!nextWindowProposal.getTitleContainer().hasClass('wcInvisible')) {
								nextWindow = nextWindowProposal;
								break;
							}
						}
					}

					if (nextWindow === null) {
						nextWindow = channelWindow;
					}

					break;
				}
			}
		}

		return nextWindow;
	}

	function onSidebarModeWindowsNavigationLeftClick() {
		var prevWindow = getLeftWindow();

		if (prevWindow !== null) {
			currentWindowDisplayed = prevWindow;
			refreshWindows();
		}
	}

	function onSidebarModeWindowsNavigationRightClick() {
		var nextWindow = getRightWindow();

		if (nextWindow !== null) {
			currentWindowDisplayed = nextWindow;
			refreshWindows();
		}
	}

	function isUsersListEnabled() {
		return options.showUsersList === true;
	}

	function isMobileModeEnabled() {
		return container.hasClass('wcWidth600');
	}

	function isChannelWindowMinimized() {
		if (options.fbMinimizeOnStart === true && localSettings.get('channelWindowMinimized') === null) {
			return true;
		}

		return localSettings.get('channelWindowMinimized') === true;
	}

	function hideUnwantedContent() {
		container.css({
			width: 0, padding: '0', margin: 0, height: 0, border: 'none'
		});
		container.find('.wcTopControls').hide();
		container.find('.wcOperationalSection').css({
			width: 0, padding: '0', margin: 0, height: 0
		});
	}

	function onUserListMinMaxButtonClick(e) {
		e.preventDefault();

		var button = jQuery(this);
		if (button.hasClass('wcUserListMinimized')) {
			button.removeClass('wcUserListMinimized');

			usersList.unhide(true);
			usersCounter.show();
			customizations.show();
			usersList.setTitlePosition(0, 'auto');
			setup();
			localSettings.set('usersListMinimized', false);
		} else {
			button.addClass('wcUserListMinimized');

			usersList.hide(true);
			usersCounter.hide();
			customizations.hide();
			usersList.setTitlePosition('auto', options.fbBottomOffset);
			localSettings.set('usersListMinimized', true);
		}
	}

	function setupCommons() {
		// set fixed color to avoid transparencies in child elements:
		if (options.theme.length === 0) {
			container.css('background-color', wisechat.utils.htmlUtils.getAncestorBackgroundColor(container));
		}

		jQuery(window).resize(function () {
			container.toggleClass('wcWidth600', jQuery(window).width() < 600);
			setup();
			refreshWindows();
		}).trigger('resize');

		channelWindowTitle.click(onChannelWindowTitleClick);

		channelWindow.$.bind('clickInside', onChannelWindowInsideClick);
		channelWindow.$.bind('clickOutside', onChannelWindowOutsideClick);

		settings.$.bind('show', setup);
		settings.$.bind('hide', setup);

		if (isMobileModeEnabled()) {
			currentWindowDisplayed = channelWindow;

			container.find('.wcSidebarModeUsersListToggler').click(onSidebarModeUsersListTogglerClick);
			container.find('.wcSidebarModeWindowsNavigationLeft').click(onSidebarModeWindowsNavigationLeftClick);
			container.find('.wcSidebarModeWindowsNavigationRight').click(onSidebarModeWindowsNavigationRightClick);

			if (privateMessagesDecorator !== null) {
				setTimeout(function () {
					allowWindowOpenReact = true;
				}, 2000);

				privateMessagesDecorator.$.bind('windowMinimized', refreshWindows);
				privateMessagesDecorator.$.bind('windowMaximized', refreshWindows);
				privateMessagesDecorator.$.bind('windowHide', function () {
					onSidebarModeWindowsNavigationRightClick();
					refreshWindows();
				});
				privateMessagesDecorator.$.bind('windowsRestored', function () {
					allowWindowOpenReact = true;
					refreshWindows();
				});

				privateMessagesDecorator.$.bind('windowOpen', function (event, openedWindow) {
					if (!allowWindowOpenReact) {
						return;
					}

					if (container.hasClass('wcSidebarModeUsersListTogglerEnabled')) {
						container.removeClass('wcSidebarModeUsersListTogglerEnabled');
						setup();
					}
					currentWindowDisplayed = openedWindow;
					refreshWindows();
				});
			}
		}
	}

	if (options.sidebarMode) {
		setupCommons();
		setup();
		hideUnwantedContent();

		usersList.getMinMaxButton().click(onUserListMinMaxButtonClick);
		if (usersList.isTitleVisible()) {
			if ((options.fbMinimizeOnStart === true && localSettings.get('usersListMinimized') === null) || localSettings.get('usersListMinimized') == true) {
				usersList.getMinMaxButton().trigger("click");
			}
		}
	}
};
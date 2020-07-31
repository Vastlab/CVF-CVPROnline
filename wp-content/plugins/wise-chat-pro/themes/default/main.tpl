{% variable containerClasses %}
	wcContainer 
	{% if showMessageSubmitButton %} wcControlsButtonsIncluded {% endif showMessageSubmitButton %}
	{% if enableImagesUploader %} wcControlsButtonsIncluded {% endif enableImagesUploader %}
	{% if enableAttachmentsUploader %} wcControlsButtonsIncluded {% endif enableAttachmentsUploader %}
    {% if showEmoticonInsertButton %} wcControlsButtonsIncluded {% endif showEmoticonInsertButton %}
	{% if showUsersList %} wcUsersListIncluded wcUsersListEnabled {% endif showUsersList %}
	{% if sidebarMode %} wcSidebarMode {% endif sidebarMode %}
	{% if allowToSendMessages %} wcAllowToSendMessages {% endif allowToSendMessages %}
{% endvariable containerClasses %}

<link rel='stylesheet' id='wise_chat_theme_{{ chatId }}-css' href='{{ themeStyles }}' type='text/css' media='all' />

<div id='{{ chatId }}' class='{{ containerClasses }}' {% if sidebarMode %}style="display: none"{% endif sidebarMode %} data-wc-config="{{ jsOptionsEncoded }}">
	{% if showWindowTitle %}
		<div class='wcWindowTitle {% if sidebarMode %}wcInvisible{% endif sidebarMode %}'>
            {% if sidebarMode %}<sup class="wcUnreadMessagesFlag">*</sup>{% endif sidebarMode %}
            {{ windowTitle }}&#160;
            {% if showRecentChatsIndicatorClassic %}<a href="#" class="wcRecentChatsIndicator"></a>{% endif showRecentChatsIndicatorClassic %}
            {% if sidebarMode %}<a href="javascript://" class="wcWindowTitleMinMaxLink"></a>{% endif sidebarMode %}
        </div>
	{% endif showWindowTitle %}

    {% if allowToReceiveMessages %}
        <div class="wcTopControls wcInvisible">
            <a href="javascript://" class="wcTopControlsButton wcUserListToggle wcInvisible"></a>
        </div>

        {% if inputControlsBottomLocation %}
            {% if allowPrivateMessages %}
                <div class='wcMessagesContainersTabs wcInvisible'> </div>
            {% endif allowPrivateMessages %}

            <div class='wcMessages {% if messagesInline %}wcMessagesInline{% endif messagesInline %} wcMessages{{ channelId }} {% if sidebarMode %}wcInvisible{% endif sidebarMode %}'>{{ messages }}</div>

            {% if showUsersList %}
                {% if showUsersListTitle %}
                    <div class="wcUserListTitle {% if showMinimizeUsersListOption %}wcUserListMinMaxLinkEnabled{% endif showMinimizeUsersListOption %}">
                        {{ usersListTitle }}
                        {% if showRecentChatsIndicatorFB %}<a href="#" class="wcRecentChatsIndicator"></a>{% endif showRecentChatsIndicatorFB %}
                        {% if showMinimizeUsersListOption %}<a href="#" class="wcUserListMinMaxLink"></a>{% endif showMinimizeUsersListOption %}
                    </div>
                {% endif showUsersListTitle %}
                <div class='wcUsersList'>
                    <div class='wcUsersListContainer'>{{ usersList }}</div>
                    {% if showUsersListSearchBox %}
                        <div class='wcUsersListFooter'>
                            <div class='wcUsersListSearchBox'>
                                <input class='wcInput' type='text' placeholder='{{ usersListSearchHint }}' />
                                <a href="#" class="wcUsersListSearchBoxCancelButton wcInvisible">
                                    <img src='{{ baseDir }}/gfx/icons/x.svg' class='wcIcon' />
                                </a>
                            </div>
                        </div>
                    {% endif showUsersListSearchBox %}
                </div>
            {% endif showUsersList %}

            {% if showUsersCounter %}
                <div class='wcUsersCounter'>
                    {{ messageTotalUsers }}: <span>{{ totalUsers }}{% if channelUsersLimit %}&nbsp;/&nbsp;{{ channelUsersLimit }} {% endif channelUsersLimit %}</span>
                </div>
            {% endif showUsersCounter %}
        {% endif inputControlsBottomLocation %}
    {% endif allowToReceiveMessages %}

    {% if allowToSendMessages %}
        <div class="wcOperationalSection">
            <div class="wcControls wcControls{{ channelId }} {% if sidebarMode %}wcInvisible{% endif sidebarMode %}">
                {% if showUserName %}
                    <span class='wcCurrentUserName'>{{ currentUserName }}{% if isCurrentUserNameNotEmpty %}:{% endif isCurrentUserNameNotEmpty %}</span>
                {% endif showUserName %}

                {% if showMessageSubmitButton %}
                    <input type='button' class='wcSubmitButton' value='{{ messageSubmitButtonCaption }}' />
                {% endif showMessageSubmitButton %}

                {% if enableAttachmentsUploader %}
                    <a href="#" class="wcToolButton wcAddFileAttachment" title="{{ messageAttachFileHint }}"><input type="file" accept="{{ attachmentsExtensionsList }}" class="wcFileUploadFile" title="{{ messageAttachFileHint }}" /></a>
                {% endif enableAttachmentsUploader %}

                {% if enableImagesUploader %}
                    <a href="#" class="wcToolButton wcAddImageAttachment" title="{{ messagePictureUploadHint }}"><input type="file" accept="image/*;capture=camera" class="wcImageUploadFile" title="{{ messagePictureUploadHint }}" /></a>
                {% endif enableImagesUploader %}

                {% if showEmoticonInsertButton %}
                    <a href="#" class="wcToolButton wcInsertEmoticonButton" title="{{ messageInsertEmoticon }}"></a>
                {% endif showEmoticonInsertButton %}

                <div class='wcInputContainer'>
                    {% if multilineSupport %}
                        <textarea class='wcInput' maxlength='{{ messageMaxLength }}' placeholder='{{ hintMessage }}'></textarea>
                    {% endif multilineSupport %}
                    {% if !multilineSupport %}
                        <input class='wcInput' type='text' maxlength='{{ messageMaxLength }}' placeholder='{{ hintMessage }}' title="{{ messageInputTitle }} " />
                    {% endif multilineSupport %}

                    <progress class="wcMainProgressBar" max="100" value="0" style="display: none;"> </progress>
                </div>

                {% if enableAttachmentsPanel %}
                    <div class="wcMessageAttachments" style="display: none;">
                        <img src="data:image/gif;base64,R0lGODlhAQABAAAAACwAAAAAAQABAAA=" class="wcImageUploadPreview" alt="Images preview" style="display: none;" />
                        <span class="wcFileUploadNamePreview" style="display: none;"></span>
                        <a href="#" class="wcAttachmentClear"><img src='{{ baseDir }}/gfx/icons/x.svg' alt="Delete attachment" class='wcIcon' /></a>
                    </div>
                {% endif enableAttachmentsPanel %}
            </div>

            {% if showCustomizationsPanel %}
                <div class='wcCustomizations'>
                    <a href='#' class='wcCustomizeButton'>{{ messageCustomize }}</a>
                    <div class='wcCustomizationsPanel' style='display:none;'>
                        {% if allowChangeUserName %}
                            <div class="wcCustomizationsProperty">
                                <label>{{ messageName }}: <input class='wcUserName' type='text' {% if userNameLengthLimit %}maxlength='{{ userNameLengthLimit }}'{% endif userNameLengthLimit %} value='{{ currentUserName }}' required /></label>
                                <input class='wcUserNameApprove' type='button' value='{{ messageSave }}' />
                            </div>
                        {% endif allowChangeUserName %}
                        {% if allowMuteSound %}
                            <div class="wcCustomizationsProperty">
                                <label>{{ messageMuteSounds }} <input class='wcMuteSound' type='checkbox' value='1' {% if muteSounds %} checked {% endif muteSounds %} /></label>
                            </div>
                        {% endif allowMuteSound %}
                        {% if allowControlUserNotifications %}
                            <div class="wcCustomizationsProperty">
                                <label>{{ messageEnableNotifications }} <input class='wcEnableNotifications' type='checkbox' value='1' {% if enableNotifications %} checked {% endif enableNotifications %} /></label>
                            </div>
                        {% endif allowControlUserNotifications %}
                        {% if allowChangeTextColor %}
                            <div class="wcCustomizationsProperty">
                                <label>{{ messageTextColor }}: <input class='wcTextColor' type='text' value="{{ textColor }}" /></label>
                                <input class='wcTextColorReset' type='button' value='{{ messageReset }}' />
                            </div>
                        {% endif allowChangeTextColor %}
                    </div>
                </div>
            {% endif showCustomizationsPanel %}
        </div>
    {% endif allowToSendMessages %}

    {% if allowToReceiveMessages %}
        {% if inputControlsTopLocation %}
            {% if allowPrivateMessages %}
                <div class='wcMessagesContainersTabs wcInvisible'> </div>
            {% endif allowPrivateMessages %}

            <div class='wcMessages {% if messagesInline %}wcMessagesInline{% endif messagesInline %} wcMessages{{ channelId }} {% if sidebarMode %}wcInvisible{% endif sidebarMode %}'>{{ messages }}</div>

            {% if showUsersList %}
                {% if showUsersListTitle %}
                    <div class="wcUserListTitle {% if showMinimizeUsersListOption %}wcUserListMinMaxLinkEnabled{% endif showMinimizeUsersListOption %}">
                        {{ usersListTitle }}
                        {% if showRecentChatsIndicatorFB %}<a href="#" class="wcRecentChatsIndicator"></a>{% endif showRecentChatsIndicatorFB %}
                        {% if showMinimizeUsersListOption %}<a href="#" class="wcUserListMinMaxLink"></a>{% endif showMinimizeUsersListOption %}
                    </div>
                {% endif showUsersListTitle %}
                <div class='wcUsersList'>
                    <div class='wcUsersListContainer'>{{ usersList }}</div>
                    {% if showUsersListSearchBox %}
                        <div class='wcUsersListFooter'>
                            <div class='wcUsersListSearchBox'>
                                <input class='wcInput' type='text' placeholder='{{ usersListSearchHint }}' />
                                <a href="#" class="wcUsersListSearchBoxCancelButton wcInvisible">
                                    <img src='{{ baseDir }}/gfx/icons/x.svg' class='wcIcon' />
                                </a>
                            </div>
                        </div>
                    {% endif showUsersListSearchBox %}
                </div>
            {% endif showUsersList %}
            {% if showUsersCounter %}
                <div class='wcUsersCounter'>
                    {{ messageTotalUsers }}: <span>{{ totalUsers }}{% if channelUsersLimit %}&nbsp;/&nbsp;{{ channelUsersLimit }} {% endif channelUsersLimit %}</span>
                </div>
            {% endif showUsersCounter %}
        {% endif inputControlsTopLocation %}
    {% endif allowToReceiveMessages %}

    <div class="wcVisualLogger wcInvisible">
        <div class="wcVisualLoggerInner"> </div>
    </div>

    {% if sidebarMode %}
        <div class="wcSidebarModeMobileNavigation">
            <a href="javascript://" class="wcSidebarModeMobileNavigationButton wcSidebarModeUsersListToggler">&#160;</a>
            <a href="javascript://" class="wcSidebarModeMobileNavigationButton wcSidebarModeWindowsNavigationRight wcInvisible">&#160;</a>
            <a href="javascript://" class="wcSidebarModeMobileNavigationButton wcSidebarModeWindowsNavigationLeft wcInvisible">&#160;</a>
            <br class="wcClear" />
        </div>
    {% endif sidebarMode %}
</div>

{{ cssDefinitions }}
{{ customCssDefinitions }}

{% if redirectURL %}
	<script>
		window.location.href = '{{ redirectURL }}';
	</script>
{% endif redirectURL %}
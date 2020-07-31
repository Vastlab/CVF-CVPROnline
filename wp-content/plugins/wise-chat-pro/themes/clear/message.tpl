{% variable messageClasses %}
    wcMessage {% if isAuthorWpUser %} wcWpMessage {% endif isAuthorWpUser %} {% if isAuthorCurrentUser %} wcCurrentUserMessage {% endif isAuthorCurrentUser %} {% if hidden %} wcMessageHidden {% endif hidden %} {% if !allowedToGetTheContent %} wcInvisible {% endif allowedToGetTheContent %}{{ cssClasses }}
{% endvariable messageClasses %}

<div class="{{ messageClasses }}" data-id="{{ messageId }}" data-chat-user-id="{{ messageChatUserId }}">
    
    {% if avatarUrl %}
        <span class="wcMessageAvatarContainer"><img class="wcMessageAvatar" src="{{ avatarUrl }}" /></span>
    {% endif avatarUrl %}
    
    <div class="wcMessageContentContainer">
        <div class="wcActionWrapper">
            <a href="#" class="wcAdminAction wcMessageApproveButton wcInvisible" data-id="{{ messageId }}" title="{{ messageApproveMessage }}"></a>
            <a href="#" class="wcAdminAction wcMessageDeleteButton wcInvisible" data-id="{{ messageId }}" title="{{ messageDeleteMessage }}"></a>
            <a href="#" class="wcAdminAction wcMessageEditButton wcInvisible" data-id="{{ messageId }}" title="{{ messageEditMessage }}"></a>
            <a href="#" class="wcAdminAction wcUserBanButton wcInvisible" data-id="{{ messageId }}" title="{{ messageBanThisUser }}"></a>
            <a href="#" class="wcAdminAction wcUserKickButton wcInvisible" data-id="{{ messageId }}" title="{{ messageKickThisUser }}"></a>
            <a href="#" class="wcAdminAction wcSpamReportButton wcInvisible" data-id="{{ messageId }}" title="{{ messageReportSpam }}"></a>
        </div>
        <span class="wcMessageUser" {% if isTextColorSetForUserName %}style="color:{{ textColor }}"{% endif isTextColorSetForUserName %}>
            {{ renderedUserName }}
        </span>
        <span class="wcMessageTime" data-utc="{{ messageTimeUTC }}"></span>
        <span class="wcMessageContent wcMessageContentInternal" {% if isTextColorSetForMessage %}style="color:{{ textColor }}"{% endif isTextColorSetForMessage %}>
            {{ messageContent }}
        </span>
    </div>
    
   
</div>
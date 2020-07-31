<link rel='stylesheet' href='{{ themeStyles }}' type='text/css' media='all' />

<div id='{{ chatId }}' class='wcContainer {% if sidebarMode %} wcSidebarMode {% endif sidebarMode %} {% if windowTitle %}wcWindowTitleIncluded{% endif windowTitle %}' data-wc-pre-config="{{ jsOptionsEncoded }}">
	{% if showWindowTitle %}
		<div class='wcWindowTitle'>{{ windowTitle }}&#160;{% if sidebarMode %}<a href="javascript://" class="wcWindowTitleMinMaxLink"></a>{% endif sidebarMode %}</div>
	{% endif showWindowTitle %}
	
	<div class="wcWindowContent">
		{% if anonymousLogin %}
			{% if anonymousLoginURL %}
				<input class='wcAnonymousLoginButton' type='button' value='{{ loginAnonymously }}' onclick="window.location.href = '{{ anonymousLoginURL }}'" />
			{% endif anonymousLoginURL %}
		{% endif anonymousLogin %}

		{% if loginUsing %}
			<div class="wcExternalLoginHint wcBottomMargin">
				{{ loginUsing }}:
			</div>
		{% endif loginUsing %}

		<div class="wcExternalLoginButtons wcCenter">
			{% if facebook %}
				{% if facebookRedirectURL %}
					<a href="{{ facebookRedirectURL }}" class="wcFacebookLoginButton">Facebook</a>
				{% endif facebookRedirectURL %}
			{% endif facebook %}

			{% if twitter %}
				{% if twitterRedirectURL %}
					<a href="{{ twitterRedirectURL }}" class="wcTwitterLoginButton">Twitter</a>
				{% endif twitterRedirectURL %}
			{% endif twitter %}

			{% if google %}
				{% if googleRedirectURL %}
					<a href="{{ googleRedirectURL }}" class="wcGoogleLoginButton">Google</a>
				{% endif googleRedirectURL %}
			{% endif google %}
		</div>
		
		{% if authenticationError %}
			<div class='wcError wcExternalAuthenticationError'>{{ authenticationError }}</div>
		{% endif authenticationError %}
	</div>
</div>

{{ cssDefinitions }}
{{ customCssDefinitions }}
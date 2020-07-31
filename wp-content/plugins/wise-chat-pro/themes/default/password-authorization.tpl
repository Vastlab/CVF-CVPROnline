<link rel='stylesheet' href='{{ themeStyles }}' type='text/css' media='all' />

<div id='{{ chatId }}' class='wcContainer {% if sidebarMode %} wcSidebarMode {% endif sidebarMode %} {% if windowTitle %}wcWindowTitleIncluded{% endif windowTitle %}' data-wc-pre-config="{{ jsOptionsEncoded }}">
	{% if showWindowTitle %}
		<div class='wcWindowTitle'>{{ windowTitle }}&#160;{% if sidebarMode %}<a href="javascript://" class="wcWindowTitleMinMaxLink"></a>{% endif sidebarMode %}</div>
	{% endif showWindowTitle %}
	
	<div class="wcWindowContent">
		<div class="wcChannelProtectionHint">{{ messageChannelPasswordAuthorizationHint }}</div>
		
		<form method="post" class="wcChannelProtectionForm">
			<input type="hidden" value="1" name="wcChannelAuthorization" />
			<input type="password" name="wcChannelPassword" class="wcChannelPassword" required />
			<input type="submit" value="{{ messageLogin }}" />
		</form>
		
		{% if authorizationError %}
			<div class='wcError wcChannelAuthorizationError'>{{ authorizationError }}</div>
		{% endif authorizationError %}
	</div>
</div>

{{ cssDefinitions }}
{{ customCssDefinitions }}
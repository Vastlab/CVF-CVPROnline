<?php 

/**
 * CVPR Chat admin pro settings tab class.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatProTab extends CVPRChatAbstractTab {

	public function getFields() {
		return array(
			array(
				'_section', 'CVPR Chat Pro Features',
				'<a href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro?source=settings-page"><img src="'.$this->options->getBaseDir().'/gfx/pro/wordpress-Cvpr-chat-pro.png" /></a>'.
				'<style type="text/css">#Cvpr-chat-proContainer .button { display: none; } #Cvpr-chat-proContainer ul li { font-size: 1.3em; }</style>'.
				'<br />'.
				'<h2>Boost user engagement, build a community, increase conversion!</h2>'.
				'<h2 style="padding-top: 1px; font-size: 20px;">Try CVPR Chat Pro plugin for WordPress and BuddyPress</h2>'.
				'<br />'.
				'<a class="button-secondary wcAdminButtonPro" target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro?source=settings-page" title="Check CVPR Chat Pro">
					Check CVPR Chat <strong>Pro</strong>
				</a>'.
				' <a class="button-secondary wcAdminButtonPro wcAdminButtonProDemo" target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro/demo/?source=settings-page" title="Check CVPR Chat Pro">
					<strong>See Demo</strong>
				</a>'.
				'<br /><h3 style="font-size: 17px;">CVPR Chat Pro features:</h3>'.
				'<ul>'.
				'<li>&#8226; All the features of CVPR Chat free edition</li>'.
				'<li>&#8226; Private one-to-one messages</li>'.
				'<li>&#8226; Sending private messages to offline users</li>'.
				'<li>&#8226; Avatars</li>'.
				'<li>&#8226; Facebook-like chat mode</li>'.
				'<li>&#8226; BuddyPress integration: friends and groups</li>'.
				'<li>&#8226; Custom emoticons</li>'.
				'<li>&#8226; E-mail notifications to admin</li>'.
				'<li>&#8226; Pending messages (fully moderated messages)</li>'.
				'<li>&#8226; External authentication (via Facebook, Twitter or Google+)</li>'.
				'<li>&#8226; WordPress multisite support</li>'.
				'<li>&#8226; Three Pro themes</li>'.
				'<li>&#8226; Users list search option</li>'.
				'<li>&#8226; Chat button on profile page</li>'.
				'<li>&#8226; Edit posted messages</li>'.
				'<li>&#8226; Free update for 6, 12 or 18 months</li>'.
				'<li>&#8226; Eternal license</li>'.
				'<li>&#8226; Pay once, use forever</li>'.
				'</ul>'.
				'<a target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro?source=settings-page" title="Check CVPR Chat Pro">
					<img src="'.$this->options->getBaseDir().'/gfx/pro/Cvpr-chat-pro-lead.png" />
				</a>'.
				'<br />'.
				'<a class="button-secondary wcAdminButtonPro" target="_blank" href="https://kaine.pl/projects/wp-plugins/Cvpr-chat-pro?source=settings-page" title="Check CVPR Chat Pro">
					Check CVPR Chat <strong>Pro</strong>
				</a>'
			),

		);
	}
	
	public function getDefaultValues() {
		return array(

		);
	}
}
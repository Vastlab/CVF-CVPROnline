<?php
	session_start();
	
	define('DOING_AJAX', true);
	define('SHORTINIT', true);
	
	if (!isset($_REQUEST['action'])) {
		header('HTTP/1.0 404 Not Found');
		die('');
	}

	ini_set('html_errors', 0);

	require_once(dirname(__FILE__).'/../CVPRChatContainer.php');
	CVPRChatContainer::load('CVPRChatInstaller');
	CVPRChatContainer::load('CVPRChatOptions');
	
	require_once(dirname(__FILE__).'/../../../../../wp-load.php');
	

	header('Content-Type: text/html');
	send_nosniff_header();

	// disable caching:
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	
	function requireIfExists($file) {
		if (file_exists(ABSPATH.WPINC.'/'.$file)) {
			require_once(ABSPATH.WPINC.'/'.$file);
		}
	}
	
	
	require_once(ABSPATH.WPINC.'/default-filters.php');
	require_once(ABSPATH.WPINC.'/l10n.php');
	requireIfExists('session.php');
	
	// features enabled:
	requireIfExists('class-wp-query.php');
	require_once(ABSPATH.WPINC.'/formatting.php');
	require_once(ABSPATH.WPINC.'/query.php');
	require_once(ABSPATH.WPINC.'/comment.php');
	require_once(ABSPATH.WPINC.'/meta.php');
	requireIfExists('class-wp-meta-query.php');
	require_once(ABSPATH.WPINC.'/post.php');
	requireIfExists('class-wp-post.php');
	require_once(ABSPATH.WPINC.'/shortcodes.php');
	require_once(ABSPATH.WPINC.'/media.php');
	require_once(ABSPATH.WPINC.'/user.php');
	require_once(ABSPATH.WPINC.'/taxonomy.php');
	requireIfExists('class-wp-tax-query.php');
	require_once(ABSPATH.WPINC.'/link-template.php');
	require_once(ABSPATH.WPINC.'/rewrite.php');
	require_once(ABSPATH.WPINC.'/author-template.php');
	requireIfExists('class-wp-rewrite.php');
	requireIfExists('rest-api.php');
	require_once(ABSPATH.WPINC.'/rewrite.php');
	require_once(ABSPATH.WPINC.'/kses.php');
	require_once(ABSPATH.WPINC.'/revision.php');
	require_once(ABSPATH.WPINC.'/capabilities.php');
	requireIfExists('class-wp-roles.php');
	requireIfExists('class-wp-role.php');
	require_once(ABSPATH.WPINC.'/pluggable.php');
	require_once(ABSPATH.WPINC.'/pluggable-deprecated.php');
	
	requireIfExists('class-wp-user.php');
	requireIfExists('class-wp-user-query.php');
	
	$GLOBALS['wp_rewrite'] = new WP_Rewrite();
	
	// NOTICE: hack for warning in plugin_basename() function:
	$wp_plugin_paths = array();
	
	wp_plugin_directory_constants();
	wp_cookie_constants();

	if (CVPRChatOptions::getInstance()->isOptionEnabled('enabled_debug', false)) {
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
	}
	
	// removing images downloaded by the chat:
	$CvprChatImagesService = CVPRChatContainer::get('services/CVPRChatImagesService');
	add_action('delete_attachment', array($CvprChatImagesService, 'removeRelatedImages'));
	
	$actionsMap = array(
		'Cvpr_chat_messages_endpoint' => 'messagesEndpoint',
		'Cvpr_chat_message_endpoint' => 'messageEndpoint',
		'Cvpr_chat_delete_message_endpoint' => 'messageDeleteEndpoint',
		'Cvpr_chat_user_ban_endpoint' => 'userBanEndpoint',
		'Cvpr_chat_user_kick_endpoint' => 'userKickEndpoint',
		'Cvpr_chat_spam_report_endpoint' => 'spamReportEndpoint',
		'Cvpr_chat_maintenance_endpoint' => 'maintenanceEndpoint',
		'Cvpr_chat_settings_endpoint' => 'settingsEndpoint',
		'Cvpr_chat_prepare_image_endpoint' => 'prepareImageEndpoint'
	);
	$CvprChatEndpoints = CVPRChatContainer::get('endpoints/CVPRChatEndpoints');
	
	$action = $_REQUEST['action'];
	if (array_key_exists($action, $actionsMap)) {
		$method = $actionsMap[$action];
		$CvprChatEndpoints->$method();
	} else {
		header('HTTP/1.0 400 Bad Request');
	}
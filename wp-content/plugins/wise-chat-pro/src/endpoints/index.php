<?php
	session_start();
	
	define('DOING_AJAX', true);
	define('SHORTINIT', true);
	
	if (!isset($_REQUEST['action'])) {
		header('HTTP/1.0 404 Not Found');
		die('');
	}
	header('Content-Type: text/html');
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');

	ini_set('html_errors', 0);

	require_once(dirname(__DIR__).'/WiseChatContainer.php');
	WiseChatContainer::load('WiseChatInstaller');
	WiseChatContainer::load('WiseChatOptions');
	require_once(dirname(__FILE__).'/wp_core.php');

	send_nosniff_header();

	if (WiseChatOptions::getInstance()->isOptionEnabled('enabled_debug', false)) {
		error_reporting(E_ALL);
		ini_set("display_errors", 1);
	}

	// removing images downloaded by the chat:
	$wiseChatImagesService = WiseChatContainer::get('services/WiseChatImagesService');
	add_action('delete_attachment', array($wiseChatImagesService, 'removeRelatedImages'));
	
	$actionsMap = array(
		'wise_chat_messages_endpoint' => 'messagesEndpoint',
		'wise_chat_message_endpoint' => 'messageEndpoint',
		'wise_chat_get_message_endpoint' => 'getMessageEndpoint',
		'wise_chat_approve_message_endpoint' => 'messageApproveEndpoint',
		'wise_chat_delete_message_endpoint' => 'messageDeleteEndpoint',
		'wise_chat_user_ban_endpoint' => 'userBanEndpoint',
		'wise_chat_user_kick_endpoint' => 'userKickEndpoint',
		'wise_chat_spam_report_endpoint' => 'spamReportEndpoint',
		'wise_chat_maintenance_endpoint' => 'maintenanceEndpoint',
		'wise_chat_settings_endpoint' => 'settingsEndpoint',
		'wise_chat_prepare_image_endpoint' => 'prepareImageEndpoint'
	);
	$wiseChatEndpoints = WiseChatContainer::get('endpoints/WiseChatEndpoints');
	
	$action = $_REQUEST['action'];
	if (array_key_exists($action, $actionsMap)) {
		$method = $actionsMap[$action];
		$wiseChatEndpoints->$method();
	} else {
		header('HTTP/1.0 400 Bad Request');
	}
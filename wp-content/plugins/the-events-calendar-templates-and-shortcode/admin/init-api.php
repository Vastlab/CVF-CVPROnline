<?php
namespace ECTREG;
class ECT_ApiConf{
    const PLUGIN_NAME = 'The Events Calendar - Shortcode And Templates Pro';
    const PLUGIN_VERSION = '2.0';
    const PLUGIN_PREFIX = 'ect';
    const PLUGIN_AUTH_PAGE = 'ect-settings';
    const PLUGIN_URL = ECT_PRO_PLUGIN_URL;
}

    require_once 'class.settings-api.php';
    require_once 'ect-base.php';
    require_once 'api-auth-settings.php';

	new ECT_Settings();
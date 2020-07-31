<?php
/*
Plugin Name:  Live Simple Clock
Description:  Live Simple Clock is a real time display plugin. It takes into account time zones and hours system formats 24h and 12h (PM, AM). With its short code you can display it anywhere on your site with precision.
Version:      1.3
Author:       Armel Tissi
Author URI:   https://armeltissi.com
License: 	  GPLv2 or later
Text Domain:  Live Simple Clock
*/
//Exit if accesed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require ( plugin_dir_path( __FILE__ ) . 'tka-simple-clock-admin.php');
require ( plugin_dir_path( __FILE__ ) . 'tka-simple-clock-front-end.php');


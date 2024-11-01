<?php

/*
 * Plugin Name: WP Dailymotion Video Fetcher
 * Plugin URI: http://www.wpdigger.com
 * Description: Add widget to your site to show any usert latest video from Dailymotion
 * Version: 1
 * Author: WPDigger
 * Author URI: http://www.wpdigger.com
 * License: GPL2
 *
 * Text Domain: wp_dailymotion_latest_video
 * Domain Path: /languages
 *
 */


if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
	die( "You're runing WordPress on outdated PHP version. Please contact your hosting company and updgrade PHP to 5.3 or above. Learn more about new features in PHP 5.3 - http://www.php.net/manual/en/migration53.new-features.php For cPanel users - you may easily switch PHP version using your hosting settings." );
}
require_once( __DIR__ . '/app/init.php' );
WST_Module_Init::WST_Init();

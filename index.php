<?php
/*
  Plugin Name: rtMedia for WordPress, BuddyPress and bbPress
  Plugin URI: https://rtmedia.io/?utm_source=dashboard&utm_medium=plugin&utm_campaign=buddypress-media
  Description: This plugin adds missing media rich features like photos, videos and audio uploading to BuddyPress which are essential if you are building social network, seriously!
  Version: 4.4
  Author: rtCamp
  Text Domain: buddypress-media
  Author URI: http://rtcamp.com/?utm_source=dashboard&utm_medium=plugin&utm_campaign=buddypress-media
  Domain Path: /languages/
 */

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    BuddyPressMedia
 * @subpackage Main
 */

if ( ! defined( 'RTMEDIA_VERSION' ) ) {
	/**
	 * The version of the plugin
	 *
	 */
	define( 'RTMEDIA_VERSION', '4.4' );
}

if ( ! defined( 'RTMEDIA_PATH' ) ) {

	/**
	 *  The server file system path to the plugin directory
	 *
	 */
	define( 'RTMEDIA_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'BP_MEDIA_PATH' ) ) {

	/**
	 *  Legacy support
	 *
	 */
	define( 'BP_MEDIA_PATH', RTMEDIA_PATH );
}


if ( ! defined( 'RTMEDIA_URL' ) ) {

	/**
	 * The url to the plugin directory
	 *
	 */
	define( 'RTMEDIA_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'RTMEDIA_BASE_NAME' ) ) {

	/**
	 * The url to the plugin directory
	 *
	 */
	define( 'RTMEDIA_BASE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * Auto Loader Function
 *
 * Autoloads classes on instantiation. Used by spl_autoload_register.
 *
 * @param string $class_name The name of the class to autoload
 */
function rtmedia_autoloader( $class_name ) {
	$rtlibpath = array(
		'app/services/' . $class_name . '.php',
		'app/helper/' . $class_name . '.php',
		'app/helper/db/' . $class_name . '.php',
		'app/admin/' . $class_name . '.php',
		'app/main/interactions/' . $class_name . '.php',
		'app/main/routers/' . $class_name . '.php',
		'app/main/routers/query/' . $class_name . '.php',
		'app/main/controllers/upload/' . $class_name . '.php',
		'app/main/controllers/upload/processors/' . $class_name . '.php',
		'app/main/controllers/shortcodes/' . $class_name . '.php',
		'app/main/controllers/template/' . $class_name . '.php',
		'app/main/controllers/media/' . $class_name . '.php',
		'app/main/controllers/group/' . $class_name . '.php',
		'app/main/controllers/privacy/' . $class_name . '.php',
		'app/main/controllers/activity/' . $class_name . '.php',
		'app/main/deprecated/' . $class_name . '.php',
		'app/main/contexts/' . $class_name . '.php',
		'app/main/' . $class_name . '.php',
		'app/main/includes/' . $class_name . '.php',
		'app/main/widgets/' . $class_name . '.php',
		'app/main/upload/' . $class_name . '.php',
		'app/main/upload/processors/' . $class_name . '.php',
		'app/main/template/' . $class_name . '.php',
		'app/log/' . $class_name . '.php',
		'app/importers/' . $class_name . '.php',
		'app/main/controllers/api/' . $class_name . '.php',
	);
	foreach ( $rtlibpath as $path ) {
		$path = RTMEDIA_PATH . $path;
		if ( file_exists( $path ) ) {
			include $path;
			break;
		}
	}
}

/**
 * Register the autoloader function into spl_autoload
 */
spl_autoload_register( 'rtmedia_autoloader' );

/**
 * Instantiate the BuddyPressMedia class.
 */
global $rtmedia;
$rtmedia = new RTMedia();

function is_rtmedia_vip_plugin() {
	return ( defined( 'WPCOM_IS_VIP_ENV' ) && WPCOM_IS_VIP_ENV );
}

/**
 * Do stuff on plugin deactivation.
 */
function rtmedia_plugin_deactivate() {
	update_option( 'is_permalink_reset', 'no' );
}
register_deactivation_hook( __FILE__, 'rtmedia_plugin_deactivate' );

include_once('updater.php');

add_action( 'admin_init', 'test_latest_update' );
function test_latest_update() {
	if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
		$config = array(
			'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
			'proper_folder_name' => 'rtMedia', // this is the name of the folder your plugin lives in
			'api_url' => 'https://api.github.com/repos/BhargavBhandari90/rtMedia', // the GitHub API url of your GitHub repo
			'raw_url' => 'https://raw.github.com/BhargavBhandari90/rtMedia/develop-update-test', // the GitHub raw url of your GitHub repo
			'github_url' => 'https://github.com/BhargavBhandari90/rtMedia', // the GitHub url of your GitHub repo
			'zip_url' => 'https://github.com/BhargavBhandari90/rtMedia/zipball/develop-update-test', // the zip url of the GitHub repo
			'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
			'requires' => '3.0', // which version of WordPress does your plugin require?
			'tested' => '3.3', // which version of WordPress is your plugin tested up to?
			'readme' => 'README.md', // which file to use as the readme for the version number
			'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
		);
		new WP_GitHub_Updater($config);
	}
}

/*
 * Look Ma! Very few includes! Next File: /app/main/RTMedia.php
 */

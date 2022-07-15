<?php
/*
Plugin Name: CrowdFund Me
Version: 5.3
Author: paramir
Description: An open source decentralized solution for Crowdfunding inside WordPress with a content protection system and membership control.
Text Domain: crowdfund-me
Domain Path: /languages/
Requires PHP: 5.6
*/

//Direct access to this file is not permitted
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Do not access this file directly.' );
}

include_once( 'classes/class.crowdfund-me.php' );
include_once( 'classes/class.crwdfnd-cronjob.php' );
include_once( 'crwdfnd-compat.php' );

define( 'CROWDFUND_ME_VER', '4.1.4' );
define( 'CROWDFUND_ME_DB_VER', '1.3' );
define( 'CROWDFUND_ME_SITE_HOME_URL', home_url() );
define( 'CROWDFUND_ME_PATH', dirname( __FILE__ ) . '/' );
define( 'CROWDFUND_ME_URL', plugins_url( '', __FILE__ ) );
define( 'CROWDFUND_ME_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
define( 'CROWDFUND_ME_TEMPLATE_PATH', 'crowd-fund' );
if ( ! defined( 'COOKIEHASH' ) ) {
	define( 'COOKIEHASH', md5( get_site_option( 'siteurl' ) ) );
}
define( 'CROWDFUND_ME_AUTH', 'crowdfund_me_' . COOKIEHASH );
define( 'CROWDFUND_ME_SEC_AUTH', 'crowdfund_me_sec_' . COOKIEHASH );

CrwdfndUtils::do_misc_initial_plugin_setup_tasks();

register_activation_hook( CROWDFUND_ME_PATH . 'crowdfund-me.php', 'CrowdFundMe::activate' );
register_deactivation_hook( CROWDFUND_ME_PATH . 'crowdfund-me.php', 'CrowdFundMe::deactivate' );

add_action( 'crwdfnd_login', 'CrowdFundMe::crwdfnd_login', 10, 3 );

$crowdfund_me      = new CrowdFundMe();
$crowdfund_me_cron = new CrwdfndCronJob();


function crwdfnd_add_settings_link( $links, $file ) {
	if ( $file == plugin_basename( __FILE__ ) ) {
		$settings_link = '<a href="admin.php?page=crowdfund_me_settings">Settings</a>';
		array_unshift( $links, $settings_link );
	}
	return $links;
}

add_filter( 'plugin_action_links', 'crwdfnd_add_settings_link', 10, 2 );

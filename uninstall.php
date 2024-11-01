<?php

if( !defined( 'ABSPATH' ) ){

	exit(-1);
}


    if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    
	delete_option('inazo.real.tor.blocker_db_version');

	global $wpdb;
	$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'inazo_tor_logs');
	wp_clear_scheduled_hook('inazo_tor_clean_logs');
?>
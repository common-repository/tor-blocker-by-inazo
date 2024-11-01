<?php
/*
Plugin Name: Tor Blocker by Inazo
Description: This plugin blocks Tor users by preventing them from viewing your website.
Version:     1.1
Author:      Inazo
Author URI:  https://www.kanjian.fr
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
@todo : menu de gestion en BO
@todo : blocage uniquement FO ou BO ou les deux
@todo : logs des blocages
@todo : personalisation du message
*/

if( !defined( 'ABSPATH' ) ){
	
	exit(-1);
}

function my_autoloader($class) {
	
	if (strncmp($class, 'Net_DNS2', 8) == 0) {

		include_once 'netdns2-master/'.str_replace('_', '/', $class) . '.php';
	}
}

/*
 * Create the install of the plugin
 */
function installPluginTor(){
	
	if (! wp_next_scheduled ( 'inazo_tor_clean_logs' )) {
		wp_schedule_event(time(), 'hourly', 'inazo_tor_clean_logs');
	}
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	
	$optionIsOk = get_option('inazo.real.tor.blocker_db_version');
	
	$currentVersion = '1.1';
	
	if( !$optionIsOk  ){
		
		update_option( 'inazo.real.tor.blocker_db_version', $currentVersion );
		
		global $wpdb;
		$table_name = $wpdb->prefix.'inazo_tor_logs';
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				
			$sqlForCreateTable = 'CREATE TABLE '.$table_name.' (
			id_tor_inazo_log int(11) NOT NULL AUTO_INCREMENT,
			ip_concern varchar(255) NOT NULL,
			is_tor int(1) NOT NULL,
			date_of_log datetime NOT NULL,
			PRIMARY KEY id_tor_inazo_log (id_tor_inazo_log)
			) '.$wpdb->get_charset_collate().';';
		
			dbDelta( $sqlForCreateTable );
		}
	}
	else
		add_option( 'inazo.real.tor.blocker_db_version', $currentVersion );
	
		global $wpdb;
		$table_name = $wpdb->prefix.'inazo_tor_logs';
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
			$sqlForCreateTable = 'CREATE TABLE '.$table_name.' (
			id_tor_inazo_log int(11) NOT NULL AUTO_INCREMENT,
			ip_concern varchar(255) NOT NULL,
			is_tor int(1) NOT NULL,
			date_of_log datetime NOT NULL,
			PRIMARY KEY id_tor_inazo_log (id_tor_inazo_log)
			) '.$wpdb->get_charset_collate().';';
	
			dbDelta( $sqlForCreateTable );
		}
	
	
}

spl_autoload_register('my_autoloader');

include_once(plugin_dir_path(__FILE__).'netdns2-master/Net/DNS2.php');

function revaddr ($ip) {
	
  $v = explode('.', $ip);
  return($v[3].'.'.$v[2].'.'.$v[1].'.'.$v[0]);
}

function torel_qh ($ip, $port, $destip) {
	
  $rsrcip = revaddr ($ip);
  $rdstip = revaddr ($destip);
  return("${rsrcip}.${port}.${rdstip}.ip-port.exitlist.torproject.org");
}

function torel_check ($ip, $port, $destip) {

	$ndr = new Net_DNS2_Resolver(); 
	$qh = torel_qh($ip, $port, $destip);

	$ndr->retrans = 2;
	$ndr->retry = 3;
	$ndr->usevc = 0;
	$pkt = false;

	try {
		$pkt = $ndr->query($qh);


		if (! isset($pkt->answer[0])) {
			
			return(0);
		}
		
		// is Tor exit
		return(1);        

	} catch(Net_DNS2_Exception $e) {
		
		if (! $pkt) {

			if (strcmp($ndr->last_error, "NXDOMAIN") == 0) {
				// response but no answer.  does not appear to be Tor exit.
				return (0);
			}
			// search failed: no response or other problem...
			return(-1);
		}		
	}
}

function isInLog( $ip ){

	global $wpdb;
	
	$toReturn = array( 'num_rows' => '', 'valeurs' => '' );

	$sql = $wpdb->prepare('SELECT is_tor FROM '.$wpdb->prefix.'inazo_tor_logs WHERE ip_concern = %s', $ip );
	$wpdb->query( $sql );

	$toReturn['num_rows'] = $wpdb->num_rows;

	if( $wpdb->num_rows > 0 )
		$toReturn['valeurs'] = $wpdb->get_results($sql, ARRAY_A);

	return $toReturn;
}

function checkIsTorConnexion(){

	global $wpdb;
	
	$ip = $myIp = $myPort = 0;
	
	if ( !empty($_SERVER["REMOTE_ADDR"])) {
		
		$ip     = $_SERVER["REMOTE_ADDR"]; 
	}
	
	if ( !empty($_SERVER["SERVER_ADDR"])) {
		
		$myIp   = $_SERVER["SERVER_ADDR"]; 
	}
	
	if ( !empty($_SERVER["SERVER_PORT"])) {
		$myPort = $_SERVER["SERVER_PORT"]; 
	}

	$returnsSql = isInLog( $ip );
	
	
	
	if( $returnsSql['num_rows'] > 0 ){
		
		if( $returnsSql['valeurs'][0]['is_tor'] == 1 ){
			
			echo '<strong>Connections from the Tor network are not allowed on this website.</strong>';
		  wp_die();
		}
		
	}
	else{
		
		$isTor = torel_check($ip, $myPort, $myIp);

		// use $istor as needed for altering page behavior:
		if( $isTor < 0){
			
			
				$wpdb->insert( $wpdb->prefix.'inazo_tor_logs', array( 'ip_concern' => $ip, 'is_tor' => 0, 'date_of_log' => current_time('mysql', 1) ), array( '%s', '%d' ) );
			
		}
		else if ($isTor) {
			
			
		  		$wpdb->insert( $wpdb->prefix.'inazo_tor_logs', array( 'ip_concern' => $ip, 'is_tor' => 1, 'date_of_log' => current_time('mysql', 1) ), array( '%s', '%d' ) );
		  echo '<strong>Connections from the Tor network are not allowed on this website.</strong>';
		  wp_die();
		}
	}
}

register_activation_hook( __FILE__, 'installPluginTor' );
register_deactivation_hook(__FILE__, 'unactivatePlugin');

function unactivatePlugin(){
	
	wp_clear_scheduled_hook('inazo_tor_clean_logs');
}

add_action( 'setup_theme', 'checkIsTorConnexion' );

add_action('inazo_tor_clean_logs', 'inazo_tor_clean_logs_task');

function inazo_tor_clean_logs_task() {
	
	global $wpdb;
	
	$wpdb->query('DELETE FROM '.$wpdb->prefix.'inazo_tor_logs WHERE (TIMESTAMPDIFF(MINUTE, date_of_log, NOW()) / 60 ) >= 3');
}

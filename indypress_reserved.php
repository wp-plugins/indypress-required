<?php

/* 
Plugin Name: IndyPress Reserved
Plugin URI: http://code.autistici.org/p/indypress
Description: Enable some forms for some users only (not-so-open publishing!)
Author: boyska, paskao
Version: 0.1
Author URI: 
License: GPL2
Domain Path: ./languages/
*/

// CONFIG
if( !$indypress_path ) //means: if indypress is not active
		return;
$indypressreserved_url = plugins_url( '', __FILE__ ) . '/indypress_reserved/';
$indypressreserved_relative_path = '/wp-content/plugins/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'indypress_reserved/';
$indypressreserved_path = ABSPATH . 'wp-content/plugins/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'indypress_reserved/';

/* --- Modified by Cap --- Start section*/
load_plugin_textdomain('indypress', '', 'indypress/languages');
/* --- Modified by Cap --- End section*/

// ADMIN PANEL MENU
require_once( $indypressreserved_path . 'inhibit.class.php' );
$indypressreserved = new indypress_inhibit();
if( is_admin() ) {
	// LOAD reserved POST TYPE
	require_once( $indypressreserved_path . 'settings.php' );
	$indypressreserved_settings = new indypressreserved_settings();
}
?>

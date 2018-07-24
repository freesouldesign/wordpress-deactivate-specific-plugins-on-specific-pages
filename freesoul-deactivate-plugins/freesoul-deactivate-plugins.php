<?php
/*
Plugin Name: Freesoul Deactivate Plugins
Description: Freesoul Deactivate Plugins lets you deactivate specific plugins for specific pages. Useful to reach good performance and for support in problem solving even when many plugins are active.
Author: Giuseppe Mortellaro, Eos Koch
Author URI: http://freesoul-design-studio.com
Text Domain: eos-dp
Domain Path: /languages/
Version: 1.1.4
*/
/*  Copyright 2018 Freesoul Design Studio (email: info at freesoul-designstudio.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//Definitions
define( 'EOS_DP_VERSION','1.1.4' );
define( 'EOS_DP_NEED_UPDATE_MU',true );
define( 'EOS_DP_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'EOS_DP_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'EOS_DP_PLUGIN_BASE_NAME', untrailingslashit( plugin_basename( __FILE__ ) ) );
//Actions triggered after plugin activation or after a new site of a multisite installation is created
function eos_dp_initialize_plugin(){
	require EOS_DP_PLUGIN_DIR.'/plugin-activation.php';
}
register_activation_hook( __FILE__, 'eos_dp_initialize_plugin' );
//Actions triggered after plugin deaactivation
function eos_dp_deactivate_plugin(){
	unlink( WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php' );
}
register_deactivation_hook( __FILE__, 'eos_dp_deactivate_plugin' );
//It loads plugin translation files
function eos_load_dp_plugin_textdomain(){
	load_plugin_textdomain( 'eos-dp', FALSE,EOS_DP_PLUGIN_DIR . '/languages/' );
}
add_action( 'admin_init', 'eos_load_dp_plugin_textdomain' );
//Filter function to read plugin translation files
function eos_dp_load_translation_file( $mofile, $domain ) {
	if ( 'eos-dp' === $domain ) {
		$loc = function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$mofile = EOS_DP_PLUGIN_DIR . '/languages/eos-dp-' . $loc . '.mo';
	}
	return $mofile;
}
if( is_admin() ){
	add_filter( 'load_textdomain_mofile', 'eos_dp_load_translation_file',99,2 ); //loads plugin translation files
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		require EOS_DP_PLUGIN_DIR. '/admin/eos-dp-ajax.php'; //file including all ajax requests functions
	}
	require EOS_DP_PLUGIN_DIR . '/inc/eos-dp-metaboxes.php'; //file including the needed functions for the metaboxes
	require EOS_DP_PLUGIN_DIR . '/admin/eos-dp-admin.php'; //file including the functions for back-end
	if( isset( $_GET['page'] ) && $_GET['page'] === 'eos_dp_menu' ){
		add_action( 'admin_enqueue_scripts', 'eos_dp_scripts',10 ); //we enqueue the scripts for back-end
	}
	add_action( 'admin_enqueue_scripts', 'eos_dp_style',10 ); //we enqueue the style for back-end
}
/**
 * Enqueue scripts for back-end
 */
function eos_dp_scripts() {
	wp_enqueue_script( 'eos-dp-backend',EOS_DP_PLUGIN_URL.'/admin/js/eos-dp-backend.js', array( 'jquery','jquery-ui-autocomplete' ), '' );
	wp_localize_script( 'eos-dp-backend','eos_dp_js',array( 'is_rtl' => is_rtl() ) );
}
/**
 * Enqueue style for back-end
 */
function eos_dp_style() {
	$action = false;
	if( function_exists( 'get_current_screen' ) ){
		$screen = get_current_screen();
		$action = isset( $screen->action ) ? $screen->action : false;
	}
	if( ( $action && $action === 'add' ) || ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] === 'edit' ) || ( isset( $_GET['page'] ) && $_GET['page'] === 'eos_dp_menu' ) ){
		wp_enqueue_style( 'eos-dp-admin-style',EOS_DP_PLUGIN_URL.'/admin/css/eos-dp-style.css',array(),EOS_DP_VERSION );
	}
}

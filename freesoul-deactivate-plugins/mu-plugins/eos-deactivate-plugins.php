<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$post_types = get_option( 'eos_post_types' );
if( !is_admin() && empty( $_POST ) && $post_types ){
	$home_page = false;
	$clean_uri = '';
	$arr = array();
	if( !isset( $_GET['page_id'] ) && !isset( $_GET['p'] ) ){
		$uri = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$uriArr = explode( '?',$uri );
		$uri = $clean_uri = $uriArr[0];
		$home_uri = str_replace( 'https://','',str_replace( 'http://','',home_url( '/' ) ) );
		if( $uri !== $home_uri ){
			$arr = array_filter( explode( '/',$uri ) );
			if( $arr[count( $arr ) - 1] !== '' ){
				$uri = $arr[count( $arr ) - 1];
			}
			$p = $uri ? get_page_by_path( esc_attr( $uri ),'OBJECT',$post_types ) : false;
			$eos_page_id = is_object( $p ) ? $p->ID : false;
		}
		else{
			$eos_page_id = get_option( 'page_on_front' );
			$home_page = true;
		}
	}
	else{
		$eos_page_id = isset( $_GET['page_id'] ) ? absint( $_GET['page_id'] ) : absint( $_GET['p'] );
	}
	$eos_page_id = absint( $eos_page_id ) !== 0 ? $eos_page_id : false;
	if( !$eos_page_id ){
		//we check if it's a child page
		if( isset( $arr[count( $arr ) - 2] ) ){
			$uri = $arr[count( $arr ) - 2].'/'.$arr[count( $arr ) - 1];
		}
		$p = $uri ? get_page_by_path( esc_attr( $uri ),'OBJECT',$post_types ) : false;
		$eos_page_id = is_object( $p ) ? $p->ID : false;		
	}
	$paths = '';
	if( $eos_page_id ){
		if( isset( $_GET['paths'] ) && isset( $_GET['eos_dp_preview'] ) ){
			$paths = explode( ';pn:',esc_attr( $_GET['paths'] ) );
		}
		else{
			$paths = explode( ',',get_post_meta( $eos_page_id,'_eos_deactive_plugins_key',true ) );
		}	
		global $eos_page_id;
	}
	else{
		$archives = get_option( 'eos_dp_archives' );
		$clean_uri = str_replace( '/','__',rtrim( $clean_uri,'/' ) );
		$key = sanitize_key( $clean_uri );
		if( isset( $_GET['paths'] ) && isset( $_GET['eos_dp_preview'] ) ){
			$paths = explode( ';pn:',esc_attr( $_GET['paths'] ) );
		}
		elseif( isset( $archives[$key] ) ){
			$paths = explode( ',',$archives[$key] );
		}
	}
	global $paths;
	if( !defined( 'EOS_DEACTIVE_PLUGINS' ) ) define( 'EOS_DEACTIVE_PLUGINS',true );
	add_filter( 'option_active_plugins', 'eos_option_active_plugins' );	
}
function eos_option_active_plugins( $plugins ){
	if( isset( $_GET['eos_dp_preview'] ) ){
		add_action( 'plugins_loaded','eos_check_dp_preview_nonce' );
	}
	global $paths;
	foreach( $plugins as $p => $const ){
		$const = str_replace( '-','_',strtoupper( str_replace( '.php','',basename( $const ) ) ) );
		if( !defined( 'EOS_'.$const.'_ACTIVE' ) ) define( 'EOS_'.$const.'_ACTIVE','true' );
	}	
	if( $paths === '' ) return $plugins;	
	$paths = $paths ? $paths : array();
	foreach( $paths as $path ){
		$k = array_search( $path, $plugins );
		if( false !== $k ){
			unset( $plugins[$k] );
		}
	}
	return $plugins;
}
function eos_check_dp_preview_nonce(){
	if( !wp_verify_nonce( $_GET['eos_dp_preview'],'eos_dp_preview' ) ){
		echo 'Sorry, you are not allowed to see this preview';
		exit;
	}
}	
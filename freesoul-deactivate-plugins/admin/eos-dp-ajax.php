<?php
/**
 *  File required by options.php that includes all the functions needed for admin ajax requests
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action("wp_ajax_eos_dp_save_settings", "eos_dp_save_settings");
//Saves categories order
function eos_dp_save_settings(){
	define( 'EOS_DP_SAVING_OPZIOND',true );
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	if ( 
		false === $nonce 
		|| ! wp_verify_nonce( $nonce, 'eos_dp_setts' ) //check for intentions
		|| !current_user_can( 'activate_plugins' ) //check for rights
	) {
	   echo 0;
	   die();
	   exit;
	}
	$post_types = array( 'page' => 'page' );
	$post_types_no_pages = get_post_types( array('publicly_queryable' => true) );
	if( $post_types_no_pages ){
		$post_types = array_merge( $post_types,$post_types_no_pages );
	}
	update_option( 'eos_post_types',array_keys( $post_types ) );
	if( isset( $_POST['eos_dp_setts'] ) && !empty( $_POST['eos_dp_setts'] ) ){
		foreach( $_POST['eos_dp_setts'] as $post_id => $opts ){
			$post_id = absint( str_replace( 'post_id_','',$post_id ) );
			if( $post_id > 0 ){
				update_post_meta( $post_id,'_eos_deactive_plugins_key',sanitize_text_field( $opts ) );
			}
		}
	}
	if( isset( $_POST['eos_dp_setts_archives'] ) && !empty( $_POST['eos_dp_setts_archives'] ) ){
		$archiveSetts = $_POST['eos_dp_setts_archives'];
		
		foreach( $archiveSetts as $k => $v ){
			unset( $archiveSetts[$k] );
			$kArr = explode( '//',$k );
			if( isset( $kArr[1] ) ){
				$k = rtrim( $kArr[1],'/' );
			}
			$k = str_replace( '/','__',$k );
			$archiveSetts[sanitize_key( $k )] = sanitize_text_field( $v );
			
		}
		update_option( 'eos_dp_archives',$archiveSetts );
	}
	echo 1;
	die();
	exit;
}
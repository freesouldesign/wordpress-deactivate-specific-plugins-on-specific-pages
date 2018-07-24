<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$writeAccess = false;
$access_type = get_filesystem_method();
if( $access_type === 'direct' ){
	/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
	$creds = request_filesystem_credentials( admin_url(), '', false, false, array() );
	/* initialize the API */
	if ( ! WP_Filesystem( $creds ) ) {
		/* any problems and we exit */
		return false;
	}	
	global $wp_filesystem;
	$writeAccess = true;
	if( empty( $wp_filesystem ) ){
		require_once ( ABSPATH . '/wp-admin/includes/file.php' );
		WP_Filesystem();
	}
	if( !$wp_filesystem->is_dir( WPMU_PLUGIN_DIR ) ){
		/* directory didn't exist, so let's create it */
		$wp_filesystem->mkdir( WPMU_PLUGIN_DIR );
	}
    $plugin_dir = EOS_DP_PLUGIN_DIR . '/mu-plugins/eos-deactivate-plugins.php';
    $destination = WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php';
    if ( !copy( $plugin_dir, $destination ) ) {
        echo __( 'Failed to create eos-deactivate-plugins.php mu-plugin','eos-framework' );
    }
	$post_types = get_post_types( array( 'publicly_queryable' => true) );
	if( $post_types ){
		$post_types['page'] = 'page';
		update_option( 'eos_post_types',array_keys( $post_types ) );
	}
	set_transient( 'freesoul-dp-notice-succ', true, 5 );
}	
else{
	set_transient( 'freesoul-dp-notice-fail', true, 5 ); /* don't have direct write access. Prompt user with our notice */
}
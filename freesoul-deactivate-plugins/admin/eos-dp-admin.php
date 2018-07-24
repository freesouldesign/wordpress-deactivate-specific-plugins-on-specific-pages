<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$plugin = EOS_DP_PLUGIN_BASE_NAME;
add_filter( "plugin_action_links_$plugin", 'eos_dp_plugin_add_settings_link' );
//It adds a settings link to the action links in the plugins page
function eos_dp_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="'.admin_url( 'plugins.php?page=eos_dp_menu' ).'">' . __( 'Settings','eos-dp' ). '</a>';
    array_push( $links, $settings_link );
  	return $links;
}

add_action( 'admin_init', function(){
	//It redirects to the plugin settings page on successfully plugin activation
	if( get_transient( 'freesoul-dp-notice-succ' ) ){
		delete_transient( 'freesoul-dp-notice-succ' );
		if( !get_transient( 'freesoul-dp-updating-mu' ) ){
			wp_safe_redirect( admin_url( 'plugins.php?page=eos_dp_menu' ) );
		}
	}
	$previous_version = get_option( 'eos_dp_version' );
	$version_compare = version_compare( $previous_version, EOS_DP_VERSION,'<' );
	if( $version_compare && EOS_DP_NEED_UPDATE_MU ){
		//if the plugin was updated and we need to update also the mu-plugin
		define( 'EOS_DP_DOING_MU_UPDATE',true );
		unlink( WPMU_PLUGIN_DIR.'/eos-deactivate-plugins.php' );
		require EOS_DP_PLUGIN_DIR.'/plugin-activation.php';
		update_option( 'eos_dp_version',EOS_DP_VERSION );
		set_transient( 'freesoul-dp-updating-mu',5 );
	}
} );
add_action( 'admin_notices', function(){
	//It creates the transient needed for displaing plugin notices after activation
	if( get_transient( 'freesoul-dp-notice-fail' ) ){
		delete_transient( 'freesoul-dp-notice-fail' );
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php _e( 'You have no direct write access, Freesoul Deactivate Plugins was not able to create the necessary mu-plugin and will not work.', 'eos-dp' ); ?></p>
	</div>
	<?php
	}
}, 100 );
//It adds the plugin setting page under plugins menu
function eos_dp_options_page(){
	add_plugins_page( __( 'Freesoul Deactivate Plugins','eos-dp' ),__( 'Freesoul Deactivate Plugins','eos-dp' ),'manage_options','eos_dp_menu','eos_dp_options_page_callback' );;
}
add_action( 'admin_menu','eos_dp_options_page' );
//Callback function for the plugin settings page
function eos_dp_options_page_callback(){
	if( !current_user_can( 'activate_plugins' ) ){
	?>
		<h2><?php _e( 'Sorry, you have not the right for this page','eos-dp' ); ?></h2>
		<?php
		return;
	}	
	wp_nonce_field( 'eos_dp_setts', 'eos_dp_setts' );
	$paths = array();
	$plugins = get_plugins();
	$active_plugins = get_option( 'active_plugins' );
	$plugin_names = array();
	$plugins_p = array();
	$featured_pages = array(
		get_option( 'page_for_posts' ),
		get_option( 'page_on_front' ),
	);
	$dpOpts = get_site_option( 'eos_dp_opts' );
	$front_id = get_option( 'page_for_posts' );
	$archiveSetts = get_option( 'eos_dp_archives' );
	$edit_title = __( 'Edit this page','eos-dp' );
	$preview_title = __( 'Preview with the active plugin you see now for this row','eos-dp' );
	$view_title = __( 'View with the plugin that are active for this row in according with the saved options','eos-dp' );
	?>
	<div class="eos-pre-nav"></div>
	<div id="eos-dp-setts-nav-wrp">
		<ul id="eos-dp-setts-nav">
			<li data-section="eos-dp-control-panel-section" class="hover eos-active eos-dp-setts-menu-item"><?php _e( 'Control Panel','eos-dp' ); ?></li>
			<?php do_action( 'eos_dp_tabs' ); ?>
		</ul>
	</div>
	<?php do_action( 'eos_dp_after_settings_nav' ); ?>
	<section id="eos-dp-control-panel-section" class="eos-dp-section">
		<h2><?php _e( 'Uncheck the plugins that you want to deactivate for each page','eos-dp' ); ?></h2>
		<div style="margin-bottom:32px"><?php _e( 'Stay a couple of seconds on the control settings with your mouse to read the help description','eos-dp' ); ?></div>
		<div class="eos-dp-sidebar close">
			<div id="eos-dp-sidebar-ctrl">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
				<span class="eos-hidden dashicons dashicons-arrow-right-alt2"></span>
			</div>
			<label for="eos-dp-go-to-posttype"><?php _e( 'Go to post type','eos-dp' ); ?></label>
			<div id="eos-dp-go-to-wrp">
				<input id="eos-dp-go-to-posttype" type="text" placeholder="<?php _e( 'Start typing ...','eos-dp' ); ?>">
				<span class="dashicons dashicons-sort"></span>
			</div>
		</div>		
		<div id="eos-dp-wrp">	
			<table id="eos-dp-setts">
				<tr id="eos-dp-table-head">
					<th style="background:transparent;border-style:none;text-align:initial;pointer-events:none">
						<div style="margin-bottom:12px">
							<span class="eos-dp-active-wrp"><input style="width:20px;height:20px" type="checkbox" /></span>
							<span class="eos-dp-legend-txt"><?php _e( 'Plugin active','eos-dp' ); ?></span>
						</div>
						<div>
							<span class="eos-dp-not-active-wrp"><input style="width:20px;height:20px" type="checkbox" checked/></span>
							<span class="eos-dp-legend-txt"><?php _e( 'Plugin not active','eos-dp' ); ?></span>
						</div>					
						<div style="margin-top:8px;margin-bottom:16px">
							<span style="margin:0;font-size:20px" title="<?php __( 'Restore last saved options','eos-dp' ); ?>" class="dashicons dashicons-image-rotate"></span><span class="eos-dp-legend-txt"><?php _e( 'Back to last saved settings','eos-dp' ); ?></span>
						</div>
					</th>
					<?php
					$n = 0;
					foreach( $active_plugins as $p ){
						if( isset( $plugins[$p] ) ){
							$plugin = $plugins[$p];
							if( isset( $plugin['TextDomain'] ) && $plugin['TextDomain'] !== 'eos-dp' ){
							?>
							<th class="eos-dp-name-th">
								<div>
									<div id="eos-dp-plugin-name-<?php echo $n + 1; ?>" class="eos-dp-plugin-name" title="<?php echo esc_attr( $plugin['Description'] ); ?>" data-path="<?php echo $p; ?>"><?php echo esc_attr( $plugin['Title'] ); ?></div>
									<div class="eos-dp-global-chk-col-wrp">
										<div class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate %s everywhere','eos-dp' ),esc_attr( $plugin['Title'] ) ); ?>" data-col="<?php echo $n + 1; ?>" class="eos-dp-global-chk-col" type="checkbox" /></div>
										<div class="eos-dp-reset-col" data-col="<?php echo $n + 1; ?>"><span title="<?php printf( __( 'Restore last saved options for %s everywhere','eos-dp' ),esc_attr( $plugin['Title'] ) ); ?>" class="dashicons dashicons-image-rotate"></span></div>
									</div>
								</div>
							</th>
							<?php
							$plugin_names[] = $plugin['Title'];
							$plugins_p[] = $p;
							++$n;
							}
						}
					}
					?>
				</tr>	
			<?php
			$post_types = array_merge( array( 'page' ),get_post_types( array( 'publicly_queryable' => true,'public' => true ) ) );
			foreach( $post_types as $post_type ){
				$posts = get_posts( array( 'post_type' => $post_type,'posts_per_page' => -1 ) );
				$labels = get_post_type_labels( get_post_type_object( $post_type ) );
				if( !in_array( $post_type,array( 'attachment' ) ) ){
					$labels_name = isset( $labels->name ) ? $labels->name : $post_type;
				?>
				<tr class="eos-dp-separator"></tr>
				<tr class="eos-dp-separator"></tr>
				<tr id="eos-dp-filters" class="eos-dp-no-border">
					<td colspan="<?php echo $n + 1; ?>">
						<table class="eos-dp-filters-table">
							<tr>
								<td><h2 id="eos-dp-<?php echo $post_type; ?>-title" class="eos-dp-post-name"><?php echo $labels_name; ?></h2></td>
							</tr>
							<tr>
							<?php
							$taxs = get_object_taxonomies( $post_type,'objects' );	
							foreach( $taxs as $tax ){
								if( is_object( $tax ) && isset( $tax->label ) ){
									?>
									<th class="eos-dp-filter">
										<?php echo $tax->label; ?>
									</th>
									<?php
								}
							}
							?>
							</tr>
							<tr>
							<?php
							foreach( $taxs as $tax ){
								if( is_object( $tax ) && isset( $tax->label ) ){
									?>
									<td class="eos-dp-filter">
										<select data-post_type="<?php echo $post_type; ?>" class="eos-dp-filter-terms">
											<option value="all"><?php _e( 'All','eos-dp' ); ?></option>
											<?php 
											$terms = get_terms( array( 'taxonomy' => $tax->name,'hide_empty' => true ) );
											foreach( $terms as $term ){
											?>
											<option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
											<?php
											}
											?>
										</select>
									</td>
									<?php
								}
							}						
							?>
							</tr>
						</table>
					</td>		
				</tr>
				<tr class="eos-dp-no-border">
					<td></td>
				</tr>
				<tr class="eos-dp-no-border">
					<td>
						<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),$labels_name ); ?>"data-post_type="<?php echo $post_type; ?>" class="eos-dp-global-chk-post_type" type="checkbox" /></span>
						<span class="eos-dp-reset-post_type" data-post_type="<?php echo $post_type; ?>"><span title="<?php printf( __( 'Reset last saved options for all plugins in %s','eos-dp' ),$labels_name ); ?>" class="dashicons dashicons-image-rotate"></span></span>							
					</td>
					<?php
					for( $r = 0;$r < $n;++$r ){
					?>	
						<td class="eos-dp-global-posts center">
							<div class="eos-dp-not-active-wrp"><input class="eos-dp-global-posts-chk" data-col="<?php echo $r + 1; ?>" data-post_type="<?php echo $post_type; ?>" title="<?php printf( __( 'Activate/deactivate %s in %s','eos-dp' ),esc_attr( $plugin_names[$r] ),$labels_name ); ?>" type="checkbox" /></div>
							<div><span class="eos-dp-global-posts-reset" data-col="<?php echo $r + 1; ?>" data-post_type="<?php echo $post_type; ?>"><span title="<?php printf( __( 'Restore last saved options for %s in %s','eos-dp' ),esc_attr( $plugin_names[$r] ),$labels_name ); ?>" class="dashicons dashicons-image-rotate"></span></span></div>
						</td>
						
					<?php
					}
					?>
				</tr>
				<?php 
				$archive_url = get_post_type_archive_link( $post_type );
				
				if( $post_type !== 'page' && $archive_url && ( $post_type !== 'post' || !get_option( 'page_for_posts') ) ): 
					$kArr = explode( '//',$archive_url );
					if( isset( $kArr[1] ) ){
						$k = $kArr[1];
					}
					$k = sanitize_key( str_replace( '/','__',rtrim( $k,'/' ) ) );
					$values = isset( $archiveSetts[$k] ) ? explode( ',',$archiveSetts[$k] ) : array_fill( ',',count( $active_plugins ) );
				?>
				<tr class="eos-dp-archive-row" data-post-type="<?php echo $post_type; ?>">
					<td class="eos-dp-post-name-wrp">
						<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),$post->post_title ); ?>" data-row="<?php echo $row; ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
						<span class="eos-dp-reset-row" data-row="<?php echo $row; ?>"><span title="<?php printf( __( 'Restore last saved optons in %s','eos-dp' ),$post->post_title ); ?>" class="dashicons dashicons-image-rotate"></span></span>
						<span class="eos-dp-title"><?php printf( __( '%s Archive','eos-dp' ),$labels_name ); ?></span>
						<div class="eos-dp-actions">
							<a class="eos-dp-view button" href="<?php echo $archive_url; ?>" target="_blank"><?php _e( 'View','eos-dp' ); ?></a>
							<a class="eos-dp-preview eos-dp-archive-preview button" href="<?php echo wp_nonce_url( $archive_url,'eos_dp_preview','eos_dp_preview' ); ?>" target="_blank"><?php _e( 'Preview','eos-dp' ); ?></a>
						</div>
					</td>				
					<?php
						for( $k = 0;$k < $n;++$k ){
						?>
						<td class="center<?php echo !in_array( $plugins_p[$k],$values ) ? ' eos-dp-active' : ''; ?>">
							<div class="eos-dp-td-chk-wrp eos-dp-td-archive-chk-wrp">
								<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.$post_type; ?>" data-checked="<?php echo in_array( $plugins_p[$k],$values ) ? 'checked' : 'not-checked'; ?>" type="checkbox"<?php echo in_array( $plugins_p[$k],$values ) ? ' checked' : ''; ?> title="<?php printf( __( '%s in the archive %s','eos-dp' ),esc_attr( $plugin_names[$k] ),esc_attr( $labels_name ) ); ?>" />
							</div>
						</td>
						<?php
						}
				
				
					
					?>
				</tr>
				<?php endif; ?>				
				<?php 
					if( $posts && !empty( $posts ) ){
						$row = 1;
						foreach( $posts as $post ){ 
							$extra_class = in_array( $post->ID,$featured_pages ) ? ' eos-featured-page' : '';
							$extra_class .= $post->ID == $featured_pages[0] ? ' eos-blog-page' : '';
							?>
							<tr class="eos-dp-post-row eos-dp-post-<?php echo $post_type.$extra_class; ?>" data-post-id="<?php echo $post->ID; ?>">
								<?php 
								if( isset( $post->post_title ) ){
									$classes = array( ' ' );
									$taxs = get_post_taxonomies( $post );
									foreach( $taxs as $tax ){
										$terms = get_the_terms( $post,$tax );
										if( is_array( $terms ) && !empty( $terms ) ){
											foreach( $terms as $term ){
												$classes[] = 'eos-dp-term-'.$term->term_id;
											}
										}
									}
									?>
									<td class="eos-dp-post-name-wrp<?php echo implode( ' ',$classes ) ?>">
										<span class="eos-dp-not-active-wrp"><input title="<?php printf( __( 'Activate/deactivate all plugins in %s','eos-dp' ),$post->post_title ); ?>" data-row="<?php echo $row; ?>" class="eos-dp-global-chk-row" type="checkbox" /></span>
										<span class="eos-dp-reset-row" data-row="<?php echo $row; ?>"><span title="<?php printf( __( 'Restore last saved optons in %s','eos-dp' ),$post->post_title ); ?>" class="dashicons dashicons-image-rotate"></span></span>
										<span class="eos-dp-title"><?php echo '' !== $post->post_title ? $post->post_title : sprintf( __( 'Untitled (post id:%s)','eos-dp' ),$post->ID ); ?></span>
										<div class="eos-dp-actions">
											<a class="eos-dp-edit button" href="<?php echo get_edit_post_link( $post->ID ); ?>" target="_blank"><?php _e( 'Edit','eos-dp' ); ?></a>
											<a class="eos-dp-view button" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank"><?php _e( 'View','eos-dp' ); ?></a>
											<a class="eos-dp-preview button" href="<?php echo wp_nonce_url( get_permalink( $post->ID ),'eos_dp_preview','eos_dp_preview' ); ?>" target="_blank"><?php _e( 'Preview','eos-dp' ); ?></a>
										</div>
									</td>
									<?php
									for( $k = 0;$k < $n;++$k ){
										$values_string = get_post_meta( $post->ID, '_eos_deactive_plugins_key',true );
										$values = explode( ',',$values_string );
									?>
									<td class="center<?php echo !in_array( $plugins_p[$k],$values ) ? ' eos-dp-active' : ''; ?>">
										<div class="eos-dp-td-chk-wrp">
											<input class="eos-dp-row-<?php echo $row; ?> eos-dp-col-<?php echo $k + 1; ?> eos-dp-col-<?php echo ( $k + 1 ).'-'.$post_type; ?>" data-checked="<?php echo in_array( $plugins_p[$k],$values ) ? 'checked' : 'not-checked'; ?>" type="checkbox"<?php echo in_array( $plugins_p[$k],$values ) ? ' checked' : ''; ?> title="<?php printf( __( '%s in the page %s','eos-dp' ),esc_attr( $plugin_names[$k] ),esc_attr( $post->post_title ) ); ?>" />
										</div>
									</td>
									<?php
									}
								} ?>
							</tr>
						<?php
							++$row;
						}
					}
				}
			}
			?>
			</table>		
		</div>
	</section>
	<section id="eos-dp-testing-section" class="eos-dp-section eos-hidden">
		<div id="eos-dp-php-testing">
			<span class="button eos-dp-php-test"><?php _e( 'Test plugins PHP performance locally','eos-dp' ); ?></span>
			<?php eos_dp_ajax_loader_img(); ?>
		</div>
		<div id="eos-dp-php-results" style="min-height:300px">		
			<pre></pre>
		</div>			
	
		
		<div id="eos-dp-gt-testing">
			<span data-post-id="<?php echo $front_id; ?>" class="button eos-dp-gt-test"><?php _e( 'Test plugins performance with GTmetrix','eos-dp' ); ?></span>
			<?php eos_dp_ajax_loader_img(); ?>
		</div>	
		<div id="eos-dp-apis-wrp">
			<table id="eos-dp-gtmetrix-apis">
				<tr id="eos-dp-gt-table-head">
					<th>
						<h2 class="left"><?php _e( 'GTmetrix Account Email','eos-dp' ); ?></h2>
					</th>
				</tr>
				<tr>
					<td>
						<input id="eos-dp-gt-user-email" type="email" style="min-width:400px" value="<?php echo isset( $dpOpts['gtmetrix_email'] ) ? esc_attr( $dpOpts['gtmetrix_email'] ) : ''; ?>" />
					</td>
				</tr>
				<tr class="eos-dp-separator"></tr>
				<tr id="eos-dp-gt-table-head">
					<th>
						<h2 class="left"><?php _e( 'GTmetrix API Key','eos-dp' ); ?></h2>
					</th>
				</tr>
				<tr>
					<td>
						<input id="eos-dp-gt-api-key" type="text" style="min-width:400px" value="<?php echo isset( $dpOpts['gtmetrix_api'] ) ? esc_attr( $dpOpts['gtmetrix_api'] ) : ''; ?>"/>
					</td>
				</tr>				
			</table>
		</div>		
		<div id="eos-dp-gt-results" style="min-height:600px">		
			<pre></pre>
		</div>		
	</section>	
	<?php
	do_action( 'eos_dp_after_setting_sections' );
	eos_dp_save_button();
}
//It displays the save button and related messages
function eos_dp_save_button(){
	$dir = is_rtl() ? 'left' : 'right';
	$antiDir = is_rtl() ? 'right' : 'left';		
	?>
	<div class="eos-dp-btn-wrp" style="margin-top:40px;">
		<input type="submit" name="submit" class="button button-primary submit-dp-opts" data-backup="false" value="<?php _e( 'Save all changes','eos-dp' ); ?>"  />
		<?php eos_dp_ajax_loader_img(); ?>
		<div style="margin-<?php echo $dir; ?>:30px">	
			<div class="eos-hidden eos-dp-opts-msg notice notice-success eos-dp-opts-msg_success msg_response" style="padding:10px;margin:10px;">
				<span><?php echo __( 'Options saved.','eos-dp' ); ?></span>
			</div>
			<div class="eos-dp-opts-msg_failed eos-dp-opts-msg notice notice-error eos-hidden msg_response" style="padding:10px;margin:10px;">
				<span><?php echo __( 'Something went wrong, maybe you need to refresh the page and try again, but you will lose all your changes','eos-dp' ); ?></span>
			</div>
			<div class="eos-dp-opts-msg_warning eos-dp-opts-msg notice notice-warning eos-hidden msg_response" style="padding:10px;margin:10px;">
				<span></span>
			</div>			
		</div>
	</div>
<?php
}
function eos_dp_ajax_loader_img(){
	?>
	<img alt="<?php _e( 'Ajax loader','eos-dp' ); ?>" class="ajax-loader-img eos-not-visible" width="30" height="30" src="<?php echo EOS_DP_PLUGIN_URL; ?>/img/ajax-loader.gif" />
	<?php
}
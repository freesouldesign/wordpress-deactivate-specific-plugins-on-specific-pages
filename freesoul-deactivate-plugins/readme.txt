=== Freesoul Deactivate Plugins ===
Contributors: giuse
Donate link: 
Tags: performance, debugging, debug
Requires at least: 4.6
Tested up to: 4.9.6
Stable tag: 1.1.4
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin lets you deactivate specific plugins for specific pages. Useful to reach good performance and for support in problem solving even when many plugins are active.

== Description ==

This is a WordPress plugin to deactivate specific plugins on specific pages and archives, to increase the performance of your WordPress site. 

You can also use it for support in problem solving even when many plugins are active.

It works for every pages, blog posts, custom posts that are publicly queryable and archives.

You will find the Settings Page submenu under the admin plugins menu. 

In the Settings Page you have a global control of the plugins that are active/not active in each pages and posts.

Moreover you will find a section for each page and post where you can select the plugins that you want to deactivate when that page or post is loaded in the front-end.

In the global settings you will be able to filter the posts based on their terms and categories, to quickly decide which plugin should be active for posts having specific terms and categories.

[vimeo https://player.vimeo.com/video/278470253]


For developers: if in your code you want to check if a plugin is globally active in your site, you can use the constant 'EOS_'.$const.'_ACTIVE' where $const is str_replace( '-','_',strtoupper( str_replace( '.php','',$plugin_file_name ) ) ).

$plugin_file name is the name of the main file of the plugin.

For example in case you have deactivated WooCommerce in a specific page, but you want that some code you want only when WooCommerce is active (e.g. code for displaying the cart link) runs in any case, you can check if WooCommerce is globally active in your site in this way:
if( class_exists( 'WooCommerce' ) || defined( 'EOS_WOOCOMMERCE_ACTIVE' ) ){
	//your code here
}

== Installation ==

1. Upload the entire `freesoul-deactivate-plugins` folder to the `/wp-content/plugins/` directory or install it using the usual installation button in the Plugins administration page.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. After successful activation you will be automatically redirected to the plugin global settings page.
4. All done. Good job!



== Changelog ==

= 1.1.4 =
* Modified: Global Settings Page, added sidebar and moved Saving button to the bottom in a fixed position
* Added: Possibility to deactivate plugins also for archives
* Added: Action hooks for incoming premium version

= 1.1.3 =
* Added: Page preview for testing purposes

= 1.1.2 =
* Added: Style changes in the Plugin Settings Page
* Added: Replaced 'edit_posts' with 'activate_plugins' as capability to activate/deactivate plugins

= 1.1.1 =
* Added: Taxonomies and terms filter in the settings page
* Added: Settings link to the plugin action links

= 1.1.0 =
* Fix: Solved loading admin stylesheet on new posts and pages editing screen

= 1.0.9 =
* Fix: PHP notices in customize preview
* Fix: PHP notices in debug log file

= 1.0.8 =
* Fix: solved mulfunction with custom post types
* Fix: solved mulfunction with child pages
* Added: mu-plugin update when it is required by the new version

= 1.0.7 =
* Fix: solved header already sent message on activation

= 1.0.6 =
* Fix: solved style issue on Firefox
* Fix: solved metaboxes options saving issue

= 1.0.5 =
* Improved translations in Italian and German
* Improved plugin general options
* Improved single page and post metaboxes settings

= 1.0.4 =
* Added redirection to the options page after plugin activation
* Improved plugin general options

= 1.0.3 =
* Added support for all custom posts that are publicly queryable
* Added plugin general options page and admin submenu under plugins menu
* Loading translation files in base of user locale and not any more in base of site locale
* Removing of Freesoul Deactivate Plugins from the deactivation options in post metaboxes

= 1.0.2 =
* Translated in Italian

= 1.0.1 =
* Translated in German

= 1.0 =
* Initial Release



== Screenshots ==

1. Global settings page (you find it under admin plugins menu)
2. Settings in each single page and post
<?php
/**
 * Help Screens
 */

add_action( 'admin_menu', 'register_plugin_page' );
function register_plugin_page() {

	$hook_suffix = add_submenu_page( 'plugins.php', 'SitePoint Plugin', 'SitePoint', 'manage_options', 'sp-config', 'sp_plugin_page' );

	add_action( "load-$hook_suffix", 'sp_help_tabs' );
}

function sp_plugin_page() {
	/* Code for the settings page will go here */
}

function sp_help_tabs() {
	$screen = get_current_screen();
	$screen->add_help_tab(
		array(
			'id'      => 'sp_overview',
			'title'   => 'Overview',
			'content' => '<p>Overview of your plugin or theme here</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'sp_faq',
			'title'   => 'FAQ',
			'content' => '<p>Frequently asked questions and their answers here</p>',
		)
	);

	$screen->add_help_tab(
		array(
			'id'      => 'sp_support',
			'title'   => 'Support',
			'content' => '<p>For support, shoot us a mail via me@w3guy.com</p>',
		)
	);

	$screen->set_help_sidebar( 'This is the content you will be adding to the sidebar.' );
}

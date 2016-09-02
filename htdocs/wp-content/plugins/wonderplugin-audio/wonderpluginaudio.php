<?php
/*
Plugin Name: WonderPlugin Audio Player
Plugin URI: http://www.wonderplugin.com
Description: WordPress Audio Player Plugin
Version: 2.4
Author: Magic Hills Pty Ltd
Author URI: http://www.wonderplugin.com
License: Copyright 2014 Magic Hills Pty Ltd, All Rights Reserved
*/

define('WONDERPLUGIN_AUDIO_VERSION', '2.4');
define('WONDERPLUGIN_AUDIO_URL', plugin_dir_url( __FILE__ ));
define('WONDERPLUGIN_AUDIO_PATH', plugin_dir_path( __FILE__ ));

require_once 'app/class-wonderplugin-audio-controller.php';

class WonderPlugin_Audio_Plugin {
	
	function __construct() {
	
		$this->init();
	}
	
	public function init() {
		
		// init controller
		$this->wonderplugin_audio_controller = new WonderPlugin_Audio_Controller();
		
		add_action( 'admin_menu', array($this, 'register_menu') );
		
		add_shortcode( 'wonderplugin_audio', array($this, 'shortcode_handler') );
		
		add_action( 'init', array($this, 'register_script') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );
		
		if ( is_admin() )
		{
			add_action( 'wp_ajax_wonderplugin_audio_save_config', array($this, 'wp_ajax_save_item') );
			add_action( 'admin_init', array($this, 'admin_init_hook') );
		}
	}
	
	function register_menu()
	{
		$menu = add_menu_page(
				__('WonderPlugin Audio Player', 'wonderplugin_audio'),
				__('WonderPlugin Audio Player', 'wonderplugin_audio'),
				'manage_options',
				'wonderplugin_audio_overview',
				array($this, 'show_overview'),
				WONDERPLUGIN_AUDIO_URL . 'images/logo-16.png' );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('Overview', 'wonderplugin_audio'),
				__('Overview', 'wonderplugin_audio'),
				'manage_options',
				'wonderplugin_audio_overview',
				array($this, 'show_overview' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('New Audio Player', 'wonderplugin_audio'),
				__('New Audio Player', 'wonderplugin_audio'),
				'manage_options',
				'wonderplugin_audio_add_new',
				array($this, 'add_new' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('Manage Audio Players', 'wonderplugin_audio'),
				__('Manage Audio Players', 'wonderplugin_audio'),
				'manage_options',
				'wonderplugin_audio_show_items',
				array($this, 'show_items' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				null,
				__('View Audio Player', 'wonderplugin_audio'),
				__('View Audio Player', 'wonderplugin_audio'),	
				'manage_options',	
				'wonderplugin_audio_show_item',	
				array($this, 'show_item' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				null,
				__('Edit Audio Player', 'wonderplugin_audio'),
				__('Edit Audio Player', 'wonderplugin_audio'),
				'manage_options',
				'wonderplugin_audio_edit_item',
				array($this, 'edit_item' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
	}
	
	function register_script()
	{		
		wp_register_script('wonderplugin-audio-template-script', WONDERPLUGIN_AUDIO_URL . 'app/wonderpluginaudiotemplate.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_script('wonderplugin-audio-skins-script', WONDERPLUGIN_AUDIO_URL . 'engine/wonderpluginaudioskins.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_script('wonderplugin-audio-script', WONDERPLUGIN_AUDIO_URL . 'engine/wonderpluginaudio.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_script('wonderplugin-audio-creator-script', WONDERPLUGIN_AUDIO_URL . 'app/wonderplugin-audio-creator.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_style('wonderplugin-audio-admin-style', WONDERPLUGIN_AUDIO_URL . 'wonderpluginaudio.css');
	}
	
	function enqueue_script()
	{
		wp_enqueue_script('wonderplugin-audio-skins-script');
		wp_enqueue_script('wonderplugin-audio-script');
	}
	
	function enqueue_admin_script($hook)
	{
		wp_enqueue_script('post');
		if (function_exists("wp_enqueue_media"))
		{
			wp_enqueue_media();
		}
		else
		{
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
		}
		wp_enqueue_script('wonderplugin-audio-template-script');
		wp_enqueue_script('wonderplugin-audio-skins-script');
		wp_enqueue_script('wonderplugin-audio-script');
		wp_enqueue_script('wonderplugin-audio-creator-script');
		wp_enqueue_style('wonderplugin-audio-admin-style');
	}
	
	function admin_init_hook()
	{
		if ( !current_user_can('manage_options') )
			return;
		
		// change text of history media uploader
		if (!function_exists("wp_enqueue_media"))
		{
			global $pagenow;
			
			if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
				add_filter( 'gettext', array($this, 'replace_thickbox_text' ), 1, 3 );
			}
		}
		
		// add meta boxes
		$this->wonderplugin_audio_controller->add_metaboxes();
	}
	
	function replace_thickbox_text($translated_text, $text, $domain) {
		
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'wonderplugin-audio' );
			if ( $referer != '' ) {
				return __('Insert into audio', 'wonderplugin_audio' );
			}
		}
		return $translated_text;
	}
	
	function show_overview() {
		
		$this->wonderplugin_audio_controller->show_overview();
	}
	
	function show_items() {
		
		$this->wonderplugin_audio_controller->show_items();
	}
	
	function add_new() {
		
		$this->wonderplugin_audio_controller->add_new();
	}
	
	function show_item() {
		
		$this->wonderplugin_audio_controller->show_item();
	}
	
	function edit_item() {
	
		$this->wonderplugin_audio_controller->edit_item();
	}
	
	function shortcode_handler($atts) {
		
		if ( !isset($atts['id']) )
			return __('Please specify a audio id', 'wonderplugin_audio');
		
		return $this->wonderplugin_audio_controller->generate_body_code( $atts['id'], false);
	}
	
	function wp_ajax_save_item() {

		if ( !current_user_can('manage_options') )
			return;
		
		header('Content-Type: application/json');
		echo json_encode($this->wonderplugin_audio_controller->save_item($_POST["item"]));
		die();
	}
	
}

/**
 * Init the plugin
 */
$wonderplugin_audio_plugin = new WonderPlugin_Audio_Plugin();

/**
 * Global php function
 * @param $id
 */
function wonderplugin_audio($id) {

	echo $wonderplugin_audio_plugin->wonderplugin_audio_controller->generate_body_code($id, false);
}

/**
 * Uninstallation
 */
function wonderplugin_audio_uninstall() {

	global $wpdb;
	$table_name = $wpdb->prefix . "wonderplugin_audio";
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}

if ( function_exists('register_uninstall_hook') )
{
	register_uninstall_hook( __FILE__, 'wonderplugin_audio_uninstall' );
}

define('WONDERPLUGIN_AUDIO_VERSION_TYPE', 'F');

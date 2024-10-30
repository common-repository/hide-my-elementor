<?php

/**
 * Plugin Name: Hide My Elementor
 * Plugin URI: https://wordpress.org/plugins/hide-my-elementor
 * Description: Hides Elementor from your website's source code.
 * Author: Hide My WP
 * Author URI: https://expresstech.io
 * Version: 1.0.1
 * Text Domain: hide-my-elementor
 */
if (!defined('ABSPATH')) {
	exit;
}

define('HideElementor_VERSION', '1.0.1');
define('HideElementor_ADDON_DIR', plugin_dir_path(__FILE__));
define('HideElementor_ADDON_URL', plugin_dir_url(__FILE__));
define('HideElementor_ADDON_INCLUDES_DIR', HideElementor_ADDON_DIR . 'includes');

/**
 * This class is the main class of the plugin
 *
 * When loaded, it loads the included plugin files and add functions to hooks or filters.
 *
 * @since 1.0
 */
class Hide_Elementor {

	/**
	 * Main Construct Function
	 *
	 * Call functions within class
	 *
	 * @since 1.0
	 * @uses Hide_Elementor::load_dependencies() Loads required filed
	 * @uses Hide_Elementor::add_hooks() Adds actions to hooks and filters
	 * @return void
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->add_hooks();
	}

	/**
	 * Load File Dependencies
	 *
	 * @since 1.0
	 * @return void
	 */
	public function load_dependencies() {
		include_once(HideElementor_ADDON_INCLUDES_DIR . '/functions.php');
	}

	/**
	 * Add Hooks
	 *
	 * Adds functions to hooks and filters
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_hooks() {
		add_action('init', array(&$this, 'install_DB'));
	}

	public function install_DB() {
		global $wpdb;
		$_version = get_option('hide_elementor_version');
		if (empty($_version) || $_version != HideElementor_VERSION) {
			update_option('hide_elementor_version', HideElementor_VERSION);
		}
	}

}

/**
 * Loads the plugin if Elementor is installed and activated
 *
 * @since 1.0
 */
function hide_elementor_load() {
	if (!did_action('elementor/loaded')) {
		add_action('admin_notices', 'hide_elementor_missing_elementor');
		return;
	}
	$HideElementor = new Hide_Elementor();
}

add_action('plugins_loaded', 'hide_elementor_load');

/**
 * Display notice if Elementor isn't installed or activated
 *
 * @since 1.0
 */
function hide_elementor_missing_elementor() {
	$plugin = 'elementor/elementor.php';
	if (_is_elementor_installed()) {
		if (!current_user_can('activate_plugins')) {
			return;
		}
		$activation_url = wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin);
		$message =  sprintf( esc_html__('%s is not working because you need to activate the %s plugin.', 'hide-my-elementor'), 'Hide My Elementor','Elementor' );
		$message .= sprintf('<p><a href="%s" class="button-primary">%s</a></p>', esc_url( $activation_url ), __('Activate Elementor Now', 'hide-my-elementor'));
	} else {
		if (!current_user_can('install_plugins')) {
			return;
		}
		$install_url = wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=elementor'), 'install-plugin_elementor');
		$message =  sprintf( esc_html__('%s is not working because you need to install the %s plugin.', 'hide-my-elementor'), 'Hide My Elementor','Elementor' );
		$message .= sprintf('<p><a href="%s" class="button-primary">%s</a></p>', esc_url( $install_url ), __('Install Elementor Now', 'hide-my-elementor'));
	}

	$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
	echo wp_kses_post( $html_message );
}

if (!function_exists('_is_elementor_installed')) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		return isset($installed_plugins[$file_path]);
	}

}
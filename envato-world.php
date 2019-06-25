<?php
/**
 * Plugin Name: envato-world
 * Description: Envato world plugin.
 * Plugin URI:  #
 * Version:     1.2.0
 * Author:      Rayhan
 * Author URI:  https://themeforest.net/user/xpeedstudio/portfolio
 * Text Domain: envato-world
 */

set_time_limit(1200);
// Plugin's main file path.
define( 'EW_FILE', __FILE__ );

// Plugin's directory.
define( 'EW_DIR', dirname( plugin_basename( EW_FILE ) ) );

// Plugin's directory path.
define( 'EW_PATH', untrailingslashit( plugin_dir_path( EW_FILE ) ) );
define( 'EW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// Stylesheets directory.
define( 'EW_CSS_DIR', 'assets/css' );
define( 'EW_JS_DIR', 'assets/js' );

// Image directory.
define( 'EW_IMG_DIR', 'assets/imgs' );

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


final class Envatoworld {

	/**
	 * Plugin Version
	 *
	 * @since 1.2.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.2.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.2.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
      
      
      
      // Load translation
      add_action( 'init', array( $this, 'i18n' ) );
      


		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function i18n() {
		 $plugin_dir = basename(dirname(__FILE__))."/languages/";
		 load_plugin_textdomain( 'envato-world',false,$plugin_dir );
	}

	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'plugin.php' );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'envato-world' ),
			'<strong>' . esc_html__( 'Envato World', 'envato-world' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'envato-world' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'envato-world' ),
			'<strong>' . esc_html__( 'envato-world', 'envato-world' ) . '</strong>',
			'<strong>' . esc_html__( 'Envato World', 'envato-world' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'envato-world' ),
			'<strong>' . esc_html__( 'Envato World', 'envato-world' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'envato-world' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

// Instantiate Envatoworld.
new Envatoworld();

//enqueue
require_once( EW_PATH . '/inc/enqueue.php');
// func
require_once( EW_PATH . '/inc/function.php');
//hooks
require_once( EW_PATH . '/inc/hooks.php');
//cron
require_once( EW_PATH . '/inc/cron/schedule.php');
//cpt
require_once( EW_PATH . '/inc/custom-post/option.php');
require_once( EW_PATH . '/inc/custom-post/cpt.php');
require_once( EW_PATH . '/inc/custom-post/metabox.php');
require_once( EW_PATH . '/inc/custom-post/shortcode.php');







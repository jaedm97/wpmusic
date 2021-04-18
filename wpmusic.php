<?php
/*
	Plugin Name: WP Music
	Plugin URI: https://github.com/jaedm97/wpmusic
	Description: Music plugin for WordPress
	Version: 1.0.0
	Text Domain: wpmusic
	Author: Jaed Mosharraf
	Author URI: https://github.com/jaedm97
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'WPMUSIC_PLUGIN_URL' ) || define( 'WPMUSIC_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'WPMUSIC_PLUGIN_DIR' ) || define( 'WPMUSIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'WPMUSIC_PLUGIN_FILE' ) || define( 'WPMUSIC_PLUGIN_FILE', plugin_basename( __FILE__ ) );

global $wpdb;

defined( 'WPMUSIC_META_TABLE' ) || define( 'WPMUSIC_META_TABLE', $wpdb->prefix . 'wpmusic_meta_box' );


if ( ! class_exists( 'WPMUSIC_Main' ) ) {
	/**
	 * Class WPMUSIC_Main
	 */
	class WPMUSIC_Main {


		/**
		 * wooOpenClose constructor.
		 */
		function __construct() {

			$this->define_scripts();
			$this->define_classes_functions();

			register_activation_hook( __FILE__, array( $this, 'activation' ) );

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}


		/**
		 * Load Textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'wpmusic', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Plugin activation hook
		 */
		function activation() {
			global $wpdb;

			$sql = "CREATE TABLE IF NOT EXISTS " . WPMUSIC_META_TABLE . " (
			id int(100) NOT NULL AUTO_INCREMENT,
			post_id int(100),
			meta_key VARCHAR(255) NOT NULL,
			meta_value VARCHAR(255) NOT NULL,
			datetime DATETIME NOT NULL,
			UNIQUE KEY id (id)
		) {$wpdb->get_charset_collate()};";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}


		/**
		 * Include Classes and Functions
		 */
		function define_classes_functions() {

			require_once WPMUSIC_PLUGIN_DIR . 'includes/classes/class-pb-settings.php';
			require_once WPMUSIC_PLUGIN_DIR . 'includes/functions.php';

			require_once WPMUSIC_PLUGIN_DIR . 'includes/classes/class-functions.php';
			require_once WPMUSIC_PLUGIN_DIR . 'includes/classes/class-hooks.php';
			require_once WPMUSIC_PLUGIN_DIR . 'includes/classes/class-meta-box.php';
		}


		/**
		 * Localize Scripts
		 *
		 * @return mixed|void
		 */
		function localize_scripts() {
			return apply_filters( 'wpmusic_localize_scripts', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			) );
		}


		/**
		 * Load Front Scripts
		 */
		function front_scripts() {

			wp_enqueue_script( 'wpmusic-front', plugins_url( '/assets/front/js/scripts.js', __FILE__ ), array( 'jquery' ), '', true );
			wp_localize_script( 'wpmusic-front', 'wpmusic', $this->localize_scripts() );

			wp_enqueue_style( 'wpmusic-front', WPMUSIC_PLUGIN_URL . 'assets/front/css/style.css' );
		}


		/**
		 * Load Admin Scripts
		 */
		function admin_scripts() {
			wp_enqueue_script( 'wpmusic-admin', plugins_url( '/assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ) );
			wp_localize_script( 'wpmusic-admin', 'wpmusic', $this->localize_scripts() );

			wp_enqueue_style( 'wpmusic-admin', WPMUSIC_PLUGIN_URL . 'assets/admin/css/style.css' );
		}


		/**
		 * Load Scripts
		 */
		function define_scripts() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
		}
	}

	new WPMUSIC_Main();
}

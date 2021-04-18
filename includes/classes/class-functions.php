<?php
/*
* @Author 		pluginbazar
* Copyright: 	2015 pluginbazar
*/

defined( 'ABSPATH' ) || exit;

class WPMUSIC_Functions {

	/**
	 * @var PB_Settings
	 */
	public $pb_settings = null;


	/**
	 * @var WPMUSIC_Meta_box
	 */
	public $meta_box = null;


	/**
	 * wpmusic constructor.
	 */
	public function __construct() {
	}


	/**
	 * Return option value
	 *
	 * @param string $option_key
	 * @param string $default_val
	 *
	 * @return mixed|string|void
	 */
	function get_option( $option_key = '', $default_val = '' ) {

		if ( empty( $option_key ) ) {
			return '';
		}

		$option_val = get_option( $option_key, $default_val );
		$option_val = empty( $option_val ) ? $default_val : $option_val;

		return apply_filters( 'wpmusic_filters_option_' . $option_key, $option_val );
	}


	/**
	 * Return settings page as Array
	 *
	 * @return mixed|void
	 */
	function get_settings_pages() {

		$pages['wpmusic_options'] = array(
			'page_nav'      => esc_html__( 'Options', 'wpmusic' ),
			'page_settings' => array(
				array(
					'title'       => esc_html__( 'General Settings', 'wpmusic' ),
					'description' => esc_html__( 'Edit general settings from this section.', 'wpmusic' ),
					'options'     => array(
						array(
							'id'          => 'wpmusic_currency',
							'title'       => esc_html__( 'Currency', 'wpmusic' ),
							'details'     => esc_html__( 'Set the currency you prefer, you can either set symbol or can set text.', 'wpmusic' ),
							'placeholder' => esc_html( '$' ),
							'type'        => 'text',
						),
						array(
							'id'          => 'wpmusic_pricing_format',
							'title'       => esc_html__( 'Pricing Format', 'wpmusic' ),
							'details'     => esc_html__( 'Customize pricing format. Default: %price% %currency%', 'wpmusic' ),
							'placeholder' => esc_html( '%price% %currency%' ),
							'type'        => 'text',
						),
					)
				),

				array(
					'title'       => esc_html__( 'Archive Page Settings', 'wpmusic' ),
					'description' => esc_html__( 'Edit archive page settings from this section.', 'wpmusic' ),
					'options'     => array(
						array(
							'id'      => 'wpmusic_music_archive',
							'title'   => esc_html__( 'Archive Page', 'wpmusic' ),
							'details' => esc_html__( 'Select a archive page for displaying all music.', 'wpmusic' ),
							'type'    => 'select',
							'args'    => 'PAGES',
						),
						array(
							'id'          => 'wpmusic_music_items_per_page',
							'title'       => esc_html__( 'Items per Page', 'wpmusic' ),
							'details'     => esc_html__( 'Select how many items you want to display per page. Default: 10', 'wpmusic' ),
							'placeholder' => esc_attr( 10 ),
							'type'        => 'number',
						),
					)
				),
			),
		);

		return apply_filters( 'wpmusic_settings_pages', $pages );
	}

	/**
	 * Return Plugin Path
	 *
	 * @return mixed|void
	 */
	function plugin_path() {
		return apply_filters( 'wpmusic_filters_plugin_path', untrailingslashit( WPMUSIC_PLUGIN_DIR ) );
	}


	/**
	 * PB_Settings Class
	 *
	 * @param array $args
	 *
	 * @return PB_Settings
	 */
	function PB_Settings( $args = array() ) {

		return new PB_Settings( $args );
	}


	/**
	 * Print notice to the admin bar
	 *
	 * @param string $message
	 * @param bool $is_success
	 * @param bool $is_dismissible
	 */
	function print_notice( $message = '', $is_success = true, $is_dismissible = true ) {

		if ( empty ( $message ) ) {
			return;
		}

		if ( is_bool( $is_success ) ) {
			$is_success = $is_success ? 'success' : 'error';
		}

		printf( '<div class="notice notice-%s %s">%s</div>', $is_success, $is_dismissible ? 'is-dismissible' : '', $message );
	}


	/**
	 * Return current URL with HTTP parameters
	 *
	 * @param $http_args
	 * @param $wp_request
	 *
	 * @return mixed|void
	 */
	function get_current_url( $http_args = array(), $wp_request = '' ) {

		global $wp;

		$current_url = empty( $wp_request ) ? site_url( $wp->request ) : $wp_request;
		$http_args   = array_merge( $_GET, $http_args );

		if ( ! empty( $http_args ) ) {
			$current_url .= '?' . http_build_query( $http_args );
		}

		return apply_filters( 'wpmusic_filters_current_url', $current_url );
	}


	/**
	 * Return Arguments Value
	 *
	 * @param string $key
	 * @param string $default
	 * @param array $args
	 *
	 * @return mixed|string
	 */
	function get_args_option( $key = '', $default = '', $args = array() ) {

		global $wpmusic_args;

		$args    = empty( $args ) ? $wpmusic_args : $args;
		$default = empty( $default ) && ! is_array( $default ) ? '' : $default;
		$default = empty( $default ) && is_array( $default ) ? array() : $default;
		$key     = empty( $key ) ? '' : $key;

		if ( isset( $args[ $key ] ) && ! empty( $args[ $key ] ) ) {
			return $args[ $key ];
		}

		return $default;
	}
}

global $wpmusic;
$wpmusic = new WPMUSIC_Functions();
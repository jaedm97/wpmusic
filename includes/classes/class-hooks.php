<?php
/**
 * Class Hooks
 *
 * @author Jaed Mosharraf
 */

if ( ! class_exists( 'WPMUSIC_Hooks' ) ) {
	/**
	 * Class WPMUSIC_Hooks
	 */
	class WPMUSIC_Hooks {

		/**
		 * WPMUSIC_Hooks constructor.
		 */
		function __construct() {

			add_action( 'init', array( $this, 'register_everything' ) );
			add_filter( 'the_content', array( $this, 'populate_music_shortcode' ) );
			add_action( 'wpmusic_before_music_archive', array( $this, 'before_music_archive' ) );
			add_action( 'wpmusic_after_music_archive', array( $this, 'after_music_archive' ) );
		}


		/**
		 * After music archive
		 */
		function after_music_archive() {

			global $wp_query, $_wp_query;

			// Restore the global $wp_query
			$wp_query = $_wp_query;

			// Resetting WP Query
			wp_reset_query();
		}


		/**
		 * Before music archive
		 */
		function before_music_archive() {

			global $wp_query, $_wp_query;

			$args = array(
				'post_type'      => 'music',
				'post_status'    => 'publish',
				'paged'          => ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1,
				'posts_per_page' => wpmusic()->get_option( 'wpmusic_music_items_per_page', 10 ),
			);

			$_wp_query = $wp_query; // Storing prev wp query
			$wp_query  = new WP_Query( $args ); // Assigning music query to global $wp_query
		}


		/**
		 * Populate content for music archive page
		 *
		 * @param $content
		 *
		 * @return mixed|string
		 */
		function populate_music_shortcode( $content ) {

			if ( get_the_ID() === (int) wpmusic()->get_option( 'wpmusic_music_archive' ) ) {
				$content = do_shortcode( '[music]' );
			}

			return $content;
		}


		/**
		 * Render shortcode [music]
		 *
		 * @param array $atts
		 *
		 * @return false|string
		 */
		function render_shortcode( $atts = array() ) {
			ob_start();
			wpmusic_get_template( 'archive-music.php', is_array( $atts ) ? $atts : array() );

			return ob_get_clean();
		}


		/**
		 * Register Post Types and Settings
		 */
		function register_everything() {

			// Register settings page
			wpmusic()->pb_settings = wpmusic()->PB_Settings( array(
				'add_in_menu'     => true,
				'menu_type'       => 'submenu',
				'menu_title'      => esc_html__( 'Settings', 'wpmusic' ),
				'page_title'      => esc_html__( 'Settings', 'wpmusic' ),
				'menu_page_title' => esc_html__( 'Music Settings', 'wpmusic' ),
				'capability'      => 'manage_options',
				'menu_slug'       => 'wpmusic',
				'parent_slug'     => 'edit.php?post_type=music',
				'pages'           => wpmusic()->get_settings_pages(),
			) );

			// Register Post Type - music
			wpmusic()->pb_settings->register_post_type( 'music', array(
				'singular'  => esc_html__( 'Music', 'wpmusic' ),
				'plural'    => esc_html__( 'All Music', 'wpmusic' ),
				'menu_icon' => 'dashicons-format-audio',
				'supports'  => array( 'title', 'editor', 'thumbnail' ),
			) );

			// Register hierarchical taxonomy genre
			wpmusic()->pb_settings->register_taxonomy( 'genre', 'music', array(
				'singular'     => esc_html__( 'Genre', 'wpmusic' ),
				'plural'       => esc_html__( 'All Genre', 'wpmusic' ),
				'hierarchical' => true,
			) );

			// Register non-hierarchical taxonomy tags
			wpmusic()->pb_settings->register_taxonomy( 'tags', 'music', array(
				'singular' => esc_html__( 'Tags', 'wpmusic' ),
				'plural'   => esc_html__( 'All Tags', 'wpmusic' ),
			) );

			// Register shortcode for displaying all music
			wpmusic()->pb_settings->register_shortcode( 'music', array( $this, 'render_shortcode' ) );
		}
	}

	new WPMUSIC_Hooks();
}
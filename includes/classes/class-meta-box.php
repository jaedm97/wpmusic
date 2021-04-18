<?php
/**
 * Class WPMUSIC_Meta_box
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPMUSIC_Meta_box' ) ) {
	/**
	 * Class WPMUSIC_Meta_box
	 */
	class WPMUSIC_Meta_box {

		/**
		 * Post types that this work in
		 */
		public $post_types = array( 'music' );


		/**
		 * @var wpdb
		 */
		public $wpdb = null;


		/**
		 * WPMUSIC_Meta_box constructor.
		 */
		function __construct() {

			global $wpdb;

			$this->wpdb = $wpdb;

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_data' ) );

			// Music column
			add_action( 'manage_music_posts_columns', array( $this, 'add_music_columns' ), 16, 1 );
			add_action( 'manage_music_posts_custom_column', array( $this, 'music_columns_content' ), 10, 2 );
		}


		/**
		 * Render custom column content
		 *
		 * @param $column_id
		 * @param $post_id
		 */
		function music_columns_content( $column_id, $post_id ) {

			switch ( $column_id ) {

				case 'composer' :
					echo esc_html( $this->get_meta( $post_id, '_composer', true ) );
					break;

				case 'publisher':
					echo esc_html( $this->get_meta( $post_id, '_publisher', true ) );
					break;

				case 'year':
					echo esc_html( $this->get_meta( $post_id, '_recording_year', true ) );
					break;

				case 'price':
					echo esc_html( wpmusic_price_formatted( $this->get_meta( $post_id, '_price', true ) ) );
					break;

				default:
					echo esc_html__( 'No data found!', 'wpmusic' );
					break;
			}
		}


		/**
		 * Add music columns
		 *
		 * @param $columns
		 *
		 * @return mixed
		 */
		function add_music_columns( $columns ) {

			$columns['composer']  = esc_html__( 'Composer', 'wpmusic' );
			$columns['publisher'] = esc_html__( 'Publisher', 'wpmusic' );
			$columns['year']      = esc_html__( 'Year', 'wpmusic' );
			$columns['price']     = esc_html__( 'Price', 'wpmusic' );

			if ( isset( $columns['date'] ) ) {
				unset( $columns['date'] );
			}

			return $columns;
		}


		/**
		 * Return music meta fields
		 *
		 * @return mixed|void
		 */
		function get_music_meta_fields() {
			$meta_fields = array(
				array(
					'id'          => '_composer',
					'title'       => esc_html__( 'Composer Name', 'wpmusic' ),
					'details'     => esc_html__( 'Composer name for this music', 'wpmusic' ),
					'placeholder' => esc_html__( 'Abhimann Roy', 'wpmusic' ),
					'type'        => 'text',
				),
				array(
					'id'          => '_publisher',
					'title'       => esc_html__( 'Publisher Name', 'wpmusic' ),
					'details'     => esc_html__( 'Publisher name for this music', 'wpmusic' ),
					'placeholder' => esc_html__( 'T Series', 'wpmusic' ),
					'type'        => 'text',
				),
				array(
					'id'          => '_recording_year',
					'title'       => esc_html__( 'Year of recording', 'wpmusic' ),
					'details'     => esc_html__( 'Publisher name for this music', 'wpmusic' ),
					'placeholder' => esc_html__( '2021', 'wpmusic' ),
					'type'        => 'number',
				),
				array(
					'id'          => '_contributors',
					'title'       => esc_html__( 'Additional Contributors', 'wpmusic' ),
					'details'     => esc_html__( 'Additional Contributors name for this music. If multiple separate with commas.', 'wpmusic' ),
					'placeholder' => esc_html__( 'Jason Hob, Shreya Ghoshal', 'wpmusic' ),
					'type'        => 'text',
				),
				array(
					'id'          => '_url',
					'title'       => esc_html__( 'Music URL', 'wpmusic' ),
					'details'     => esc_html__( 'Public URL for this music.', 'wpmusic' ),
					'placeholder' => esc_url( 'https://yoursite/music-url-here/' ),
					'type'        => 'text',
				),
				array(
					'id'          => '_price',
					'title'       => esc_html__( 'Price', 'wpmusic' ),
					'details'     => esc_html__( 'Add your music price here', 'wpmusic' ),
					'placeholder' => esc_attr( '99' ),
					'type'        => 'number',
				),
			);

			return apply_filters( 'wpmusic_music_meta_fields', $meta_fields );
		}


		/**
		 * Display meta data
		 *
		 * @param $post WP_Post
		 */
		function render_music_meta( $post ) {

			wp_nonce_field( 'wpmusic_nonce', 'wpmusic_nonce_val' );

			if ( ! empty( $meta_fields = $this->get_music_meta_fields() ) && is_array( $meta_fields ) ) {

				foreach ( $meta_fields as $index => $field ) {
					if ( ! empty( $meta_key = wpmusic()->get_args_option( 'id', '', $field ) ) ) {
						$meta_fields[ $index ]['value'] = $this->get_meta( $post->ID, $meta_key, true );
					}
				}

				wpmusic()->pb_settings->generate_fields( array( 'page_settings' => array( 'options' => $meta_fields ) ) );
			}
		}


		/**
		 * Add Meta boxes
		 *
		 * @param $post_type
		 */
		function add_meta_boxes( $post_type ) {

			if ( in_array( $post_type, $this->post_types ) ) {
				add_meta_box( 'wpmusic_metabox', esc_html__( 'Music Data Box', 'wpmusic' ), array( $this, 'render_music_meta' ), $post_type, 'normal', 'high' );
			}
		}


		/**
		 * Update meta data
		 *
		 * @param int $post_id
		 * @param string $meta_key
		 * @param string $meta_value
		 * @param string $prev_value
		 *
		 * @return bool|int
		 */
		function update_meta( $post_id = 0, $meta_key = '', $meta_value = '', $prev_value = '' ) {

			$post_id    = ! $post_id || empty( $post_id ) ? get_the_ID() : $post_id;
			$prev_value = empty( $prev_value ) ? $this->get_meta( $post_id, $meta_key, true ) : $prev_value;

			if ( empty( $meta_key ) || ! $post_id ) {
				return false;
			}

			// if no prev value add as new entry
			if ( empty( $prev_value ) ) {
				return $this->add_meta( $post_id, $meta_key, $meta_value );
			}

			// update meta data when there is prev value
			return $this->wpdb->update( WPMUSIC_META_TABLE,
				array( 'meta_value' => $meta_value ),
				array( 'post_id' => $post_id, 'meta_key' => $meta_key, 'meta_value' => $prev_value )
			);
		}


		/**
		 * Add post meta
		 *
		 * @param int $post_id
		 * @param string $meta_key
		 * @param string $meta_value
		 *
		 * @return bool|int
		 */
		function add_meta( $post_id = 0, $meta_key = '', $meta_value = '' ) {

			$post_id = ! $post_id || empty( $post_id ) ? get_the_ID() : $post_id;

			if ( empty( $meta_key ) || ! $post_id ) {
				return false;
			}

			// insert an entry in the meta table
			return $this->wpdb->insert( WPMUSIC_META_TABLE,
				array( 'post_id' => $post_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value, 'datetime' => current_time( 'mysql' ) )
			);
		}


		function get_meta( $post_id = 0, $meta_key = false, $single = false ) {

			$post_id    = ! $post_id || empty( $post_id ) ? get_the_ID() : $post_id;
			$meta_value = '';

			if ( empty( $meta_key ) || ! $post_id ) {
				return $meta_value;
			}

			// if retrieving singular data
			if ( $single ) {
				$row        = $this->wpdb->get_row( "SELECT * FROM " . WPMUSIC_META_TABLE . " WHERE post_id = $post_id AND meta_key = '$meta_key'", ARRAY_A );
				$meta_value = isset( $row['meta_value'] ) ? $row['meta_value'] : $meta_value;
			} // if retrieving multiple values as array
			else {
				$results    = $this->wpdb->get_results( "SELECT * FROM " . WPMUSIC_META_TABLE . " WHERE post_id = $post_id AND meta_key = '$meta_key'", ARRAY_A );
				$results    = array_map( function ( $row ) {
					if ( isset( $row['meta_value'] ) ) {
						return $row['meta_value'];
					}

					return '';
				}, $results );
				$meta_value = array_filter( $results );
			}

			return apply_filters( 'wpmusic_get_meta', $meta_value, $meta_key, $post_id );
		}


		/**
		 * Save post meta data
		 *
		 * @param $post_id
		 */
		function save_meta_data( $post_id ) {

			$posted_data = wp_unslash( $_POST );

			if ( wp_verify_nonce( wpmusic()->get_args_option( 'wpmusic_nonce_val', '', $posted_data ), 'wpmusic_nonce' ) ) {
				foreach ( $this->get_music_meta_fields() as $field ) {
					if ( ! empty( $meta_key = wpmusic()->get_args_option( 'id', '', $field ) ) ) {
						$this->update_meta( $post_id, $meta_key, wpmusic()->get_args_option( $meta_key, '', $posted_data ) );
					}
				}
			}
		}


	}

	wpmusic()->meta_box = new WPMUSIC_Meta_box();
}
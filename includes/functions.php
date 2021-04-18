<?php
/*
* @Author 		pluginbazar
* Copyright: 	2015 pluginbazar
*/

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpmusic_get_template_part' ) ) {
	/**
	 * Get Template Part
	 *
	 * @param $slug
	 * @param string $name
	 * @param array $args
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 */
	function wpmusic_get_template_part( $slug, $name = '', $args = array(), $main_template = false ) {

		$template   = '';
		$plugin_dir = WPMUSIC_PLUGIN_DIR;

		/**
		 * Locate template
		 */
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				"wpmusic/{$slug}-{$name}.php"
			) );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		// Search in WPMUSIC Pro
		if ( strpos( $backtrace_file, 'wpmusic-open-close-pro' ) !== false && defined( 'WPMUSICP_PLUGIN_DIR' ) ) {
			$plugin_dir = $main_template ? WPMUSIC_PLUGIN_DIR : WPMUSICP_PLUGIN_DIR;
		}


		/**
		 * Search for Template in Plugin
		 *
		 * @in Plugin
		 */
		if ( ! $template && $name && file_exists( untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php" ) ) {
			$template = untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php";
		}


		/**
		 * Search for Template in Theme
		 *
		 * @in Theme
		 */
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", "wpmusic/{$slug}.php" ) );
		}


		/**
		 * Allow 3rd party plugins to filter template file from their plugin.
		 *
		 * @filter wpmusic_filters_get_template_part
		 */
		$template = apply_filters( 'wpmusic_filters_get_template_part', $template, $slug, $name );


		if ( $template ) {
			load_template( $template, false );
		}
	}
}


if ( ! function_exists( 'wpmusic_get_template' ) ) {
	/**
	 * Get Template
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return WP_Error
	 */
	function wpmusic_get_template( $template_name, $args = array(), $template_path = '', $default_path = '', $main_template = false ) {

		if ( ! empty( $args = array_merge( $args, array( 'args' => $args ) ) ) && is_array( $args ) ) {
			extract( $args );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		$located = wpmusic_locate_template( $template_name, $template_path, $default_path, $backtrace_file, $main_template );


		if ( ! file_exists( $located ) ) {
			return new WP_Error( 'invalid_data', __( '%s does not exist.', 'wpmusic' ), '<code>' . $located . '</code>' );
		}

		$located = apply_filters( 'wpmusic_filters_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'wpmusic_before_template_part', $template_name, $template_path, $located, $args );

		include $located;

		do_action( 'wpmusic_after_template_part', $template_name, $template_path, $located, $args );
	}
}


if ( ! function_exists( 'wpmusic_locate_template' ) ) {
	/**
	 *  Locate template
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 * @param string $backtrace_file
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return mixed|void
	 */
	function wpmusic_locate_template( $template_name, $template_path = '', $default_path = '', $backtrace_file = '', $main_template = false ) {

		$plugin_dir = WPMUSIC_PLUGIN_DIR;

		/**
		 * Template path in Theme
		 */
		if ( ! $template_path ) {
			$template_path = 'wpmusic/';
		}

		// Check for WPMUSIC Pro
		if ( ! empty( $backtrace_file ) && strpos( $backtrace_file, 'wpmusic-open-close-pro' ) !== false && defined( 'WPMUSICP_PLUGIN_DIR' ) ) {
			$plugin_dir = $main_template ? WPMUSIC_PLUGIN_DIR : WPMUSICP_PLUGIN_DIR;
		}


		/**
		 * Template default path from Plugin
		 */
		if ( ! $default_path ) {
			$default_path = untrailingslashit( $plugin_dir ) . '/templates/';
		}

		/**
		 * Look within passed path within the theme - this is priority.
		 */
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		/**
		 * Get default template
		 */
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		/**
		 * Return what we found with allowing 3rd party to override
		 *
		 * @filter wpmusic_filters_locate_template
		 */
		return apply_filters( 'wpmusic_filters_locate_template', $template, $template_name, $template_path );
	}
}


if ( ! function_exists( 'wpmusic' ) ) {
	function wpmusic() {

		global $wpmusic;

		if ( empty( $wpmusic ) ) {
			$wpmusic = new WPMUSIC_Functions();
		}

		return $wpmusic;
	}
}


if ( ! function_exists( 'wpmusic_get_genres' ) ) {
	/**
	 * Return all genres of a music
	 *
	 * @param int $post_id
	 *
	 * @return mixed|void
	 */
	function wpmusic_get_genres( $post_id = 0 ) {

		$post_id = ! $post_id || empty( $post_id ) ? get_the_ID() : $post_id;
		$genres  = get_the_terms( $post_id, 'genre' );
		$genres  = array_map( function ( $genre ) {
			if ( $genre instanceof WP_Term ) {
				return $genre->name;
			}

			return '';
		}, $genres );

		return apply_filters( 'wpmusic_get_genres', implode( ', ', array_filter( $genres ) ), $post_id );
	}
}


if ( ! function_exists( 'wpmusic_get_tags' ) ) {
	/**
	 * Return all tags of a music
	 *
	 * @param int $post_id
	 *
	 * @return mixed|void
	 */
	function wpmusic_get_tags( $post_id = 0 ) {

		$post_id = ! $post_id || empty( $post_id ) ? get_the_ID() : $post_id;
		$genres  = get_the_terms( $post_id, 'tags' );
		$genres  = array_map( function ( $genre ) {
			if ( $genre instanceof WP_Term ) {
				return $genre->name;
			}

			return '';
		}, $genres );

		return apply_filters( 'wpmusic_get_tags', implode( ', ', array_filter( $genres ) ), $post_id );
	}
}


if ( ! function_exists( 'wpmusic_pagination' ) ) {
	/**
	 * Return Pagination HTML Content
	 *
	 * @param array $args
	 * @param bool $query_object
	 *
	 * @return array|string|void
	 */
	function wpmusic_pagination( $args = array(), $query_object = false ) {

		global $wp_query;

		// Storing current $wp_query
		$prev_query = $wp_query;

		// if query_object passed, then assign it to global $wp_query
		if ( $query_object ) {
			$wp_query = $query_object;
		}

		$paged          = max( 1, ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1 );
		$defaults       = array(
			'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'format'    => '?paged=%#%',
			'current'   => $paged,
			'total'     => $wp_query->max_num_pages,
			'prev_text' => esc_html__( 'Previous', 'wp-poll' ),
			'next_text' => esc_html__( 'Next', 'wp-poll' ),
		);
		$args           = apply_filters( 'wpmusic_pagination_args', array_merge( $defaults, $args ) );
		$paginate_links = paginate_links( $args );

		// Assigning saved query to $wp_query
		$wp_query = $prev_query;

		return apply_filters( 'wpmusic_pagination_links', $paginate_links );
	}
}


if ( ! function_exists( 'wpmusic_price_formatted' ) ) {
	/**
	 * Return formatted price
	 *
	 * @param int $price
	 *
	 * @return int|mixed|void
	 */
	function wpmusic_price_formatted( $price = 0 ) {

		$currency = wpmusic()->get_option( 'wpmusic_currency', '$' );
		$format   = wpmusic()->get_option( 'wpmusic_pricing_format', '%price%%currency%' );

		if ( strpos( $format, '%currency%' ) === false || strpos( $format, '%price%' ) === false ) {
			return $price;
		}

		$price_formatted = str_replace( '%currency%', $currency, $format );
		$price_formatted = str_replace( '%price%', $price, $price_formatted );

		return apply_filters( 'wpmusic_price_formatted', $price_formatted, $price );
	}
}
<?php
/**
 * Music archive
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

/**
 * Creating $wp_query for music query
 *
 * @see WPMUSIC_Hooks::before_music_archive()
 */
do_action( 'wpmusic_before_music_archive' );


if ( $wp_query->have_posts() ) :

	// Starting the music loop
	wpmusic_get_template( 'loop/start.php' );

	while ( $wp_query->have_posts() ) : $wp_query->the_post();

		wpmusic_get_template_part( 'content', 'music' );

	endwhile;

	// Finish the music loop
	wpmusic_get_template( 'loop/end.php' );

else:
	// No music found
	wpmusic_get_template( 'loop/no-items.php' );
endif;


// Loading pagination
wpmusic_get_template( 'loop/pagination.php' );


/**
 * Resetting $wp_query to original object
 *
 * @see WPMUSIC_Hooks::after_music_archive()
 */
do_action( 'wpmusic_after_music_archive' );


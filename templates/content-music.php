<?php
/**
 * Single music inside the loop
 */

defined( 'ABSPATH' ) || exit;


?>

<?php do_action( 'wpmusic_before_music_archive_single' ); ?>

    <div class="music-single">

		<?php
		/**
		 * Thumbnail of the music
		 */
		wpmusic_get_template_part( 'loop/item', 'thumb' ); ?>

		<?php
		/**
		 * Title of the music
		 */
		wpmusic_get_template_part( 'loop/item', 'title' ); ?>


		<?php
		/**
		 * Meta data of the music
		 */
		wpmusic_get_template_part( 'loop/item', 'meta' ); ?>


		<?php
		/**
		 * Short description of the music
		 */
		wpmusic_get_template_part( 'loop/item', 'details' ); ?>


		<?php
		/**
		 * Footer with tags and external button of the music
		 */
		wpmusic_get_template_part( 'loop/item', 'footer' ); ?>

    </div>

<?php do_action( 'wpmusic_after_music_archive_single' ); ?>
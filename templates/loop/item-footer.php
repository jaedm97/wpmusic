<?php
/**
 * Music Footer
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="music-footer">
	<?php if ( ! empty( $ext_url = wpmusic()->meta_box->get_meta( get_the_ID(), '_url', true ) ) ) : ?>

        <a href="<?php echo esc_url( $ext_url ); ?>" class="wpmusic-button music-external-url"><?php esc_html_e( 'Music external URL', 'wpmusic' ); ?></a>

	<?php endif; ?>
</div>

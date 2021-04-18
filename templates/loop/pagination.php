<?php
/**
 * Music archive pagination
 */

defined( 'ABSPATH' ) || exit;


global $wp_query;
?>

<div class="wpmusic-pagination paginate"><?php echo wp_kses_post( wpmusic_pagination() ); ?></div>



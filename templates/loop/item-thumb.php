<?php
/**
 * Music thumbnail
 */

defined( 'ABSPATH' ) || exit;
?>


<div class="thumb"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></div>

<?php
/**
 * Music meta data
 */

defined( 'ABSPATH' ) || exit;


wpmusic_get_genres();

?>

<div class="all-meta-data">

    <div class="general-meta-data">
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Composer', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic()->meta_box->get_meta( get_the_ID(), '_composer', true ) ); ?></div>
        </div>
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Publisher', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic()->meta_box->get_meta( get_the_ID(), '_publisher', true ) ); ?></div>
        </div>
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Year', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic()->meta_box->get_meta( get_the_ID(), '_recording_year', true ) ); ?></div>
        </div>
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Price', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic_price_formatted( wpmusic()->meta_box->get_meta( get_the_ID(), '_price', true ) ) ); ?></div>
        </div>
    </div>

    <div class="genre-tags-data">
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Genre', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic_get_genres() ); ?></div>
        </div>
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Contributors', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic()->meta_box->get_meta( get_the_ID(), '_contributors', true ) ); ?></div>
        </div>
        <div class="meta-data">
            <div class="data-label"><?php esc_html_e( 'Tags', 'wpmusic' ); ?></div>
            <div class="data-value"><?php echo esc_html( wpmusic_get_tags() ); ?></div>
        </div>
    </div>
</div>
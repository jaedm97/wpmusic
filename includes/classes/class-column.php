<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access

class class_qa_wpmusic_hour_column {

	public function __construct() {

		add_action( 'manage_wpmusic_hour_posts_columns', array( $this, 'add_columns' ), 16, 1 );
		add_action( 'manage_wpmusic_hour_posts_custom_column', array( $this, 'custom_columns_content' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
		add_filter( 'months_dropdown_results', array( $this, 'remove_date_filter' ), 10, 2 );
	}


	function remove_date_filter( $months, $post_type ) {
		if ( $post_type === 'wpmusic_hour' ) {
			return array();
		}

		return $months;
	}


	function custom_columns_content( $column, $post_id ) {

		if ( $column === 'shortcode' ) {
			printf( '<span class="wpmusic-shortcode hint--top" aria-label="%s">[schedule id="%s"]</span>',
				esc_html__( 'Click to Copy', 'wpmusic' ), $post_id
			);
		}

		if ( $column === 'is-default' && $post_id == wpmusic()->get_active_schedule_id() ) {
			printf( '<div class="wpmusic-schedule-default">%s</div>', esc_html__( 'Default Schedule', 'wpmusic' ) );
		}

		if ( $column === 'wpmusic-date' ) {
			printf( esc_html__( 'Created %s ago', 'wpmusic' ), human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) );
		}
	}


	/**
	 * Add columns to poll list page
	 *
	 * @return array
	 */
	function add_columns() {

		return array(
			'title'      => '',
			'shortcode'  => '',
			'is-default' => '',
			'wpmusic-date'   => '',
		);
	}


	public function remove_row_actions( $actions ) {
		global $post;

		if ( $post->post_type === 'wpmusic_hour' ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
		}

		return $actions;
	}


}

new class_qa_wpmusic_hour_column();
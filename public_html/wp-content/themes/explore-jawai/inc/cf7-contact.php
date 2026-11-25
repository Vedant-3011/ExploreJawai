<?php
/**
 * Contact Form 7 integration for /contact/ (editable in wp-admin).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return int
 */
function explore_jawai_get_cf7_form_id() {
	$form_id = (int) get_option( 'explore_jawai_cf7_form_id', 0 );
	if ( $form_id && 'wpcf7_contact_form' === get_post_type( $form_id ) && 'publish' === get_post_status( $form_id ) ) {
		return $form_id;
	}

	$posts = get_posts(
		array(
			'post_type'      => 'wpcf7_contact_form',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	return $posts ? (int) $posts[0]->ID : 0;
}

/**
 * @return string
 */
function explore_jawai_render_cf7_contact_form() {
	if ( ! function_exists( 'wpcf7_contact_form' ) ) {
		return '';
	}

	$form_id = explore_jawai_get_cf7_form_id();
	if ( $form_id < 1 ) {
		return '';
	}

	add_filter( 'wpcf7_autop', '__return_false', 20 );

	$html = do_shortcode(
		sprintf(
			'[contact-form-7 id="%d" html_class="ej-contact-form ej-form-grid" html_id="ej-contact-cf7"]',
			$form_id
		)
	);

	remove_filter( 'wpcf7_autop', '__return_false', 20 );

	if ( '' === trim( wp_strip_all_tags( $html ) ) ) {
		return '';
	}

	return '<div class="ej-contact-form-wrap ej-contact-form-wrap--cf7">' . $html . '</div>';
}

add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! function_exists( 'wpcf7_contact_form' ) ) {
			return;
		}
		if ( explore_jawai_get_cf7_form_id() > 0 ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'Explore Jawai: install the default Contact Form 7 enquiry form (run deploy/setup-contact-form-7.sh) or create a form in Contact → Contact Forms.', 'explore-jawai' );
		echo '</p></div>';
	}
);
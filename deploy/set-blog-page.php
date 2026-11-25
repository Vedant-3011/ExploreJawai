<?php
/**
 * Ensure Home + Blog pages and Reading settings (run via wp eval-file).
 *
 * @package ExploreJawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * @param string $slug Page slug.
 * @param string $title Page title.
 * @return int
 */
function explorejawai_ensure_page( $slug, $title ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing instanceof WP_Post && 'publish' === $existing->post_status ) {
		return (int) $existing->ID;
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => '',
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}

	return (int) $id;
}

$front_id = explorejawai_ensure_page( 'site-home', 'Home' );
$blog_id  = explorejawai_ensure_page( 'blog', 'Blog' );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $front_id );
update_option( 'page_for_posts', $blog_id );

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::success( "Reading settings: front={$front_id} (site-home), posts={$blog_id} (blog)" );
}
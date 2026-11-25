<?php
/**
 * WP-CLI eval-file: sync About page from deploy/content/about-explore-jawai.html
 *
 * Usage: ABOUT_PAGE_ID=12 CONTENT_FILE=/path/about-explore-jawai.html wp eval-file update-about-content.php
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}

$id   = (int) getenv( 'ABOUT_PAGE_ID' );
$file = getenv( 'CONTENT_FILE' );

if ( $id < 1 || ! is_string( $file ) || ! is_readable( $file ) ) {
	WP_CLI::error( 'Set ABOUT_PAGE_ID and CONTENT_FILE env vars.' );
}

$content = file_get_contents( $file );
if ( false === $content ) {
	WP_CLI::error( 'Could not read content file.' );
}

$result = wp_update_post(
	array(
		'ID'           => $id,
		'post_title'   => 'Explore Jawai',
		'post_name'    => 'about',
		'post_status'  => 'publish',
		'post_excerpt' => 'Discover Jawai granite landscape, wildlife, Jawai Bandh, birdlife and Rabari pastoral culture in Pali, Rajasthan.',
		'post_content' => $content,
	),
	true
);

if ( is_wp_error( $result ) ) {
	WP_CLI::error( $result->get_error_message() );
}

WP_CLI::success( "About page updated (ID {$id})." );
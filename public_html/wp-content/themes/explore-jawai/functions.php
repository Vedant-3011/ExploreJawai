<?php
/**
 * Explore Jawai — child theme of Twenty Twenty-Four.
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'explore-jawai-parent',
			get_template_directory_uri() . '/style.css',
			array(),
			wp_get_theme( 'twentytwentyfour' )->get( 'Version' )
		);
		wp_enqueue_style(
			'explore-jawai',
			get_stylesheet_uri(),
			array( 'explore-jawai-parent' ),
			'1.2.1'
		);
	}
);

add_action(
	'init',
	static function () {
		register_block_pattern_category(
			'explore-jawai',
			array(
				'label' => __( 'Explore Jawai', 'explore-jawai' ),
			)
		);
	}
);

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 120,
				'width'       => 120,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
	}
);

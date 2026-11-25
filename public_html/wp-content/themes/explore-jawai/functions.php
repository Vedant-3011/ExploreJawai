<?php
/**
 * Explore Jawai — child theme of Twenty Twenty-Four.
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_stylesheet_directory() . '/inc/subscribe.php';
require_once get_stylesheet_directory() . '/inc/contact-form.php';

/** Editorial design (matches local XAMPP preview). */
define( 'EXPLORE_JAWAI_EDITORIAL', true );

add_shortcode(
	'explore_jawai_editorial_cards',
	static function () {
		if ( ! defined( 'EXPLORE_JAWAI_EDITORIAL' ) || ! EXPLORE_JAWAI_EDITORIAL ) {
			return '';
		}
		ob_start();
		include get_stylesheet_directory() . '/inc/editorial-cards-markup.php';
		return (string) ob_get_clean();
	}
);

add_filter(
	'body_class',
	static function ( $classes ) {
		if ( defined( 'EXPLORE_JAWAI_EDITORIAL' ) && EXPLORE_JAWAI_EDITORIAL ) {
			$classes[] = 'ej-editorial';
		}
		return $classes;
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		$parent     = wp_get_theme( get_template() );
		$parent_ver = $parent->exists() ? $parent->get( 'Version' ) : '1.0';
		wp_enqueue_style(
			'explore-jawai-parent',
			get_template_directory_uri() . '/style.css',
			array(),
			$parent_ver
		);
		wp_enqueue_style(
			'explore-jawai',
			get_stylesheet_uri(),
			array( 'explore-jawai-parent' ),
			'2.1.0'
		);
		if ( defined( 'EXPLORE_JAWAI_EDITORIAL' ) && EXPLORE_JAWAI_EDITORIAL ) {
			wp_enqueue_style(
				'explore-jawai-fonts',
				'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap',
				array(),
				null
			);
			wp_enqueue_style(
				'explore-jawai-editorial',
				get_stylesheet_directory_uri() . '/assets/css/editorial.css',
				array( 'explore-jawai' ),
				'2.1.0'
			);
		}
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

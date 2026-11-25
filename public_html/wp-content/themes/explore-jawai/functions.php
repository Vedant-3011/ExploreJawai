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

/**
 * Collapse whitespace between tags so wpautop cannot inject stray paragraphs into shortcode HTML.
 *
 * @param string $html Markup from a theme partial.
 * @return string
 */
function explore_jawai_compact_markup( $html ) {
	$html = trim( (string) $html );
	if ( '' === $html ) {
		return '';
	}
	return (string) preg_replace( '/>\s+</', '><', $html );
}

/**
 * @param string $partial Partial under inc/.
 * @return string
 */
function explore_jawai_render_partial( $partial ) {
	if ( ! defined( 'EXPLORE_JAWAI_EDITORIAL' ) || ! EXPLORE_JAWAI_EDITORIAL ) {
		return '';
	}
	$path = get_stylesheet_directory() . '/inc/' . $partial;
	if ( ! is_readable( $path ) ) {
		return '';
	}
	ob_start();
	include $path;
	return explore_jawai_compact_markup( (string) ob_get_clean() );
}

add_shortcode( 'explore_jawai_editorial_cards', static fn () => explore_jawai_render_partial( 'editorial-cards-markup.php' ) );
add_shortcode( 'explore_jawai_home_split', static fn () => explore_jawai_render_partial( 'editorial-home-split.php' ) );
add_shortcode( 'explore_jawai_home_steps', static fn () => explore_jawai_render_partial( 'editorial-home-steps.php' ) );

/**
 * Undo wpautop damage inside editorial shortcodes (core/shortcode block).
 *
 * @param string $html Shortcode or block HTML.
 * @return string
 */
function explore_jawai_fix_autop_block_html( $html ) {
	$html = (string) $html;
	if ( '' === $html || ( strpos( $html, 'ej-home-' ) === false && strpos( $html, 'ej-blog-' ) === false && strpos( $html, 'ej-editorial-card' ) === false ) ) {
		return $html;
	}
	$html = preg_replace( '#</span></p>\s*<div>#', '</span><div>', $html );
	$html = preg_replace( '#<p>\s*(<(div|section|article|figure)\s)#i', '$1', $html );
	$html = preg_replace( '#</(div|section|article|figure)>\s*</p>#i', '</$1>', $html );
	$html = preg_replace( '#<p>\s*(<a class="ej-editorial-text-link")#', '$1', $html );
	$html = preg_replace( '#(</a>)</p>(\s*</div>)#', '$1$2', $html );
	$html = preg_replace( '#<img([^>]+)/>\s*<br\s*/?>#i', '<img$1 />', $html );
	return $html;
}

add_filter(
	'do_shortcode_tag',
	static function ( $output, $tag ) {
		static $tags = array(
			'explore_jawai_home_split',
			'explore_jawai_home_steps',
			'explore_jawai_home_journal',
			'explore_jawai_editorial_cards',
			'explore_jawai_editorial_blog',
		);
		if ( in_array( $tag, $tags, true ) ) {
			return explore_jawai_fix_autop_block_html( $output );
		}
		return $output;
	},
	20,
	2
);

add_filter(
	'render_block',
	static function ( $content, $block ) {
		if ( isset( $block['blockName'] ) && 'core/shortcode' === $block['blockName'] ) {
			return explore_jawai_fix_autop_block_html( $content );
		}
		return $content;
	},
	20,
	2
);
add_shortcode( 'explore_jawai_home_journal', static fn () => explore_jawai_render_partial( 'editorial-home-journal.php' ) );
add_shortcode( 'explore_jawai_editorial_blog', static fn () => explore_jawai_render_partial( 'editorial-blog-archive.php' ) );

add_filter(
	'body_class',
	static function ( $classes ) {
		if ( defined( 'EXPLORE_JAWAI_EDITORIAL' ) && EXPLORE_JAWAI_EDITORIAL ) {
			$classes[] = 'ej-editorial';
		}
		if ( is_home() && ! is_front_page() ) {
			$classes[] = 'ej-blog-index';
		}
		if ( is_singular( 'post' ) ) {
			$classes[] = 'ej-single-post-active';
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
			'2.1.8'
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
				'2.1.8'
			);
		}
		if ( is_home() && ! is_front_page() ) {
			wp_enqueue_script(
				'explore-jawai-blog-filters',
				get_stylesheet_directory_uri() . '/assets/js/editorial-blog-filters.js',
				array(),
				'2.1.8',
				true
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

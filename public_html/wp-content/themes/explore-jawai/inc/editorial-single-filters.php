<?php
/**
 * Single post — topic links (same labels as blog.html filters).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$topics = array(
	array(
		'label' => __( 'All stories', 'explore-jawai' ),
		'url'   => home_url( '/blog/' ),
	),
	array(
		'label' => __( 'Wildlife', 'explore-jawai' ),
		'url'   => home_url( '/category/wildlife/' ),
	),
	array(
		'label' => __( 'Nature', 'explore-jawai' ),
		'url'   => home_url( '/blog/' ),
	),
	array(
		'label' => __( 'Culture', 'explore-jawai' ),
		'url'   => home_url( '/category/culture/' ),
	),
	array(
		'label' => __( 'Travel tips', 'explore-jawai' ),
		'url'   => home_url( '/category/travel-tips/' ),
	),
	array(
		'label' => __( 'Photography', 'explore-jawai' ),
		'url'   => home_url( '/blog/' ),
	),
);

$current_terms = get_the_terms( get_the_ID(), 'category' );
$current_slug  = ( $current_terms && ! is_wp_error( $current_terms ) ) ? $current_terms[0]->slug : '';
?>
<nav class="ej-single-topic-filters ej-blog-filters" aria-label="<?php esc_attr_e( 'Browse journal topics', 'explore-jawai' ); ?>">
	<?php foreach ( $topics as $topic ) : ?>
		<?php
		$is_active = ( 'All stories' === $topic['label'] && ! $current_slug )
			|| ( str_contains( strtolower( $topic['url'] ), '/' . $current_slug . '/' ) );
		?>
		<a class="ej-blog-filter ej-blog-filter--link<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $topic['url'] ); ?>"><?php echo esc_html( $topic['label'] ); ?></a>
	<?php endforeach; ?>
</nav>
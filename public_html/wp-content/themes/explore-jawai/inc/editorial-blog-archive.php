<?php
/**
 * Blog archive — prototype blog.html (hero is in home.html template).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category name used on cards and filter buttons (prototype labels).
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function explore_jawai_blog_card_category( $post ) {
	$terms = get_the_terms( $post->ID, 'category' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return __( 'Journal', 'explore-jawai' );
	}
	$name = $terms[0]->name;
	$map = array(
		'Safari'         => 'Wildlife',
		'Uncategorized'  => 'Travel Tips',
	);
	return $map[ $name ] ?? $name;
}

$filter_labels = array(
	'all'          => __( 'All stories', 'explore-jawai' ),
	'Wildlife'     => __( 'Wildlife', 'explore-jawai' ),
	'Nature'       => __( 'Nature', 'explore-jawai' ),
	'Culture'      => __( 'Culture', 'explore-jawai' ),
	'Travel Tips'  => __( 'Travel tips', 'explore-jawai' ),
	'Photography'  => __( 'Photography', 'explore-jawai' ),
);

$query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<section class="ej-blog-archive-wrap alignfull">
	<div class="explore-jawai-shell">
		<div class="ej-home-section-head">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Read, learn, plan', 'explore-jawai' ); ?></p>
			<h2 class="ej-serif"><?php esc_html_e( 'Explore the journal.', 'explore-jawai' ); ?></h2>
			<p class="ej-home-section-intro"><?php esc_html_e( 'A selection of articles currently published on Explore Jawai. New stories can be added here as the blog grows.', 'explore-jawai' ); ?></p>
		</div>
		<div class="ej-blog-filters" role="toolbar" aria-label="<?php esc_attr_e( 'Filter articles by topic', 'explore-jawai' ); ?>">
			<?php foreach ( $filter_labels as $key => $label ) : ?>
				<button type="button" class="ej-blog-filter<?php echo 'all' === $key ? ' is-active' : ''; ?>" data-filter="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php if ( $query->have_posts() ) : ?>
			<div class="ej-home-blog-grid ej-blog-archive-grid">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$cat_label = explore_jawai_blog_card_category( get_post() );
					$thumb     = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
					?>
					<article class="ej-home-blog-card" data-category="<?php echo esc_attr( $cat_label ); ?>">
						<div class="ej-home-blog-thumb"<?php echo $thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ')"' : ''; ?>></div>
						<div class="ej-home-blog-copy">
							<p class="ej-editorial-eyebrow"><?php echo esc_html( $cat_label ); ?></p>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article ↗', 'explore-jawai' ); ?></a>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
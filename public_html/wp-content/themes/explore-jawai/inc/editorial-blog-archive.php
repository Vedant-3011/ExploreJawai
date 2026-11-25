<?php
/**
 * Blog archive grid (hero + sidebar live in templates).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category label on cards (matches prototype filter names).
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
	$map  = array(
		'Safari'        => 'Wildlife',
		'Uncategorized' => 'Travel Tips',
	);
	return $map[ $name ] ?? $name;
}

if ( is_home() || is_category() || is_tag() || is_author() || is_date() || is_search() ) {
	$query = $GLOBALS['wp_query'];
} else {
	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
?>
<div class="ej-blog-archive-main">
	<div class="ej-home-section-head">
		<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Read, learn, plan', 'explore-jawai' ); ?></p>
		<h2 class="ej-serif"><?php esc_html_e( 'Explore the journal.', 'explore-jawai' ); ?></h2>
		<p class="ej-home-section-intro"><?php esc_html_e( 'A selection of articles currently published on Explore Jawai. New stories can be added here as the blog grows.', 'explore-jawai' ); ?></p>
	</div>
	<?php if ( $query->have_posts() ) : ?>
		<div class="ej-home-blog-grid ej-blog-archive-grid">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$cat_label = explore_jawai_blog_card_category( get_post() );
				$thumb     = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
				?>
				<article class="ej-home-blog-card">
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
			?>
		</div>
		<?php if ( $query->max_num_pages > 1 ) : ?>
			<nav class="ej-blog-pagination" aria-label="<?php esc_attr_e( 'Journal pages', 'explore-jawai' ); ?>">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $query->max_num_pages,
							'current'   => $paged,
							'mid_size'  => 1,
							'prev_text' => '← ' . esc_html__( 'Previous', 'explore-jawai' ),
							'next_text' => esc_html__( 'Next', 'explore-jawai' ) . ' →',
							'type'      => 'list',
						)
					)
				);
				?>
			</nav>
		<?php endif; ?>
		<?php
		if ( ! ( is_home() || is_category() || is_tag() || is_author() || is_date() || is_search() ) ) {
			wp_reset_postdata();
		}
		?>
	<?php endif; ?>
</div>
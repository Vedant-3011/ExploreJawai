<?php
/**
 * Homepage — journal teaser grid (prototype index.html, live posts).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts = get_posts(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<section class="ej-home-journal-wrap alignfull">
	<div class="explore-jawai-shell">
		<div class="ej-home-section-head">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'From the journal', 'explore-jawai' ); ?></p>
			<h2 class="ej-serif"><?php esc_html_e( 'Stories to help you go deeper.', 'explore-jawai' ); ?></h2>
		</div>
		<?php if ( $posts ) : ?>
			<div class="ej-home-blog-grid">
				<?php
				foreach ( $posts as $post_item ) :
					setup_postdata( $post_item );
					$terms = get_the_terms( $post_item->ID, 'category' );
					$label = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : __( 'Journal', 'explore-jawai' );
					$thumb = get_the_post_thumbnail_url( $post_item, 'medium_large' );
					?>
					<article class="ej-home-blog-card">
						<div class="ej-home-blog-thumb"<?php echo $thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ')"' : ''; ?>></div>
						<div class="ej-home-blog-copy">
							<p class="ej-editorial-eyebrow"><?php echo esc_html( $label ); ?></p>
							<h3><a href="<?php echo esc_url( get_permalink( $post_item ) ); ?>"><?php echo esc_html( get_the_title( $post_item ) ); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_item ), 14 ) ); ?></p>
							<a href="<?php echo esc_url( get_permalink( $post_item ) ); ?>"><?php esc_html_e( 'Read the guide →', 'explore-jawai' ); ?></a>
						</div>
					</article>
					<?php
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
		<?php
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$journal_url   = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/blog/' );
		?>
		<p class="ej-home-text-link-wrap">
			<a class="ej-editorial-text-link" href="<?php echo esc_url( $journal_url ); ?>"><?php esc_html_e( 'Visit the travel journal →', 'explore-jawai' ); ?></a>
		</p>
	</div>
</section>
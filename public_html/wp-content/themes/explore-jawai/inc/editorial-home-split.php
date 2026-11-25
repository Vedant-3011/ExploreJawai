<?php
/**
 * Homepage — "Not just a safari" split section (prototype index.html).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_img = get_stylesheet_directory_uri() . '/assets/images/jawai-granite-hills-leopard-hero.webp';
?>
<section class="ej-home-split-wrap alignfull">
	<div class="explore-jawai-shell ej-home-split">
		<div class="ej-home-split__copy">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Not just a safari', 'explore-jawai' ); ?></p>
			<h2 class="ej-serif"><?php esc_html_e( 'Meet the many sides of Jawai.', 'explore-jawai' ); ?></h2>
			<p class="ej-home-section-intro"><?php esc_html_e( 'The first image many travellers associate with Jawai is a leopard among granite rocks. But the region is more than a wildlife encounter. Its character is shaped by rocky terrain, Jawai Bandh, birdlife, pastoral traditions and the everyday relationship between people and the land.', 'explore-jawai' ); ?></p>
			<div class="ej-home-features">
				<div class="ej-home-feature">
					<span class="ej-home-feature__num" aria-hidden="true">01</span>
					<div>
						<h3><?php esc_html_e( 'Wild landscapes', 'explore-jawai' ); ?></h3>
						<p><?php esc_html_e( 'Granite formations, open scrub and the changing light across the hills.', 'explore-jawai' ); ?></p>
					</div>
				</div>
				<div class="ej-home-feature">
					<span class="ej-home-feature__num" aria-hidden="true">02</span>
					<div>
						<h3><?php esc_html_e( 'Water and birdlife', 'explore-jawai' ); ?></h3>
						<p><?php esc_html_e( 'Discover the reservoir landscape and the birds that use its wetland habitats.', 'explore-jawai' ); ?></p>
					</div>
				</div>
				<div class="ej-home-feature">
					<span class="ej-home-feature__num" aria-hidden="true">03</span>
					<div>
						<h3><?php esc_html_e( 'Living culture', 'explore-jawai' ); ?></h3>
						<p><?php esc_html_e( 'Learn about local pastoral life through respectful, community-led experiences.', 'explore-jawai' ); ?></p>
					</div>
				</div>
			</div>
			<p class="ej-home-text-link-wrap">
				<a class="ej-editorial-text-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Discover the region in depth →', 'explore-jawai' ); ?></a>
			</p>
		</div>
		<figure class="ej-home-image-frame">
			<img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'A leopard on a granite outcrop above the Jawai reservoir landscape', 'explore-jawai' ); ?>" width="600" height="490" loading="lazy" decoding="async" />
			<span class="ej-home-image-caption"><?php esc_html_e( 'The granite country of Jawai', 'explore-jawai' ); ?></span>
		</figure>
	</div>
</section>
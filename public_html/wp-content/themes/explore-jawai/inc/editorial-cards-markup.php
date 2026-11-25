<?php
/**
 * Editorial experience cards markup (home).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ej-editorial-cards">
	<article class="ej-editorial-card">
		<div class="ej-editorial-card__visual ej-editorial-card__visual--safari" role="img" aria-label="<?php esc_attr_e( 'Leopards and rocky hills', 'explore-jawai' ); ?>"></div>
		<div class="ej-editorial-card__body">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Wildlife', 'explore-jawai' ); ?></p>
			<h3><?php esc_html_e( 'Leopards & the rocky hills', 'explore-jawai' ); ?></h3>
			<p><?php esc_html_e( 'Understand the habitat and approach wildlife viewing with patience and respect.', 'explore-jawai' ); ?></p>
			<a class="ej-editorial-text-link" href="<?php echo esc_url( home_url( '/about/#wildlife' ) ); ?>"><?php esc_html_e( 'Explore wildlife →', 'explore-jawai' ); ?></a>
		</div>
	</article>
	<article class="ej-editorial-card">
		<div class="ej-editorial-card__visual ej-editorial-card__visual--water" role="img" aria-label="<?php esc_attr_e( 'Jawai Bandh birdlife', 'explore-jawai' ); ?>"></div>
		<div class="ej-editorial-card__body">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Nature', 'explore-jawai' ); ?></p>
			<h3><?php esc_html_e( 'Jawai Bandh & birdlife', 'explore-jawai' ); ?></h3>
			<p><?php esc_html_e( 'Discover the reservoir, surrounding habitats and seasonal birdwatching possibilities.', 'explore-jawai' ); ?></p>
			<a class="ej-editorial-text-link" href="<?php echo esc_url( home_url( '/about/#dam' ) ); ?>"><?php esc_html_e( 'Explore the water landscape →', 'explore-jawai' ); ?></a>
		</div>
	</article>
	<article class="ej-editorial-card">
		<div class="ej-editorial-card__visual ej-editorial-card__visual--culture" role="img" aria-label="<?php esc_attr_e( 'Rabari village culture', 'explore-jawai' ); ?>"></div>
		<div class="ej-editorial-card__body">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Culture', 'explore-jawai' ); ?></p>
			<h3><?php esc_html_e( 'Villages & local life', 'explore-jawai' ); ?></h3>
			<p><?php esc_html_e( 'Approach local traditions with curiosity, consent and respect for everyday life.', 'explore-jawai' ); ?></p>
			<a class="ej-editorial-text-link" href="<?php echo esc_url( home_url( '/about/#culture' ) ); ?>"><?php esc_html_e( 'Discover local culture →', 'explore-jawai' ); ?></a>
		</div>
	</article>
</div>
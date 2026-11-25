<?php
/**
 * Homepage — planning steps section (prototype index.html).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ej-home-steps-wrap alignfull">
	<div class="explore-jawai-shell">
		<div class="ej-home-section-head">
			<p class="ej-editorial-eyebrow"><?php esc_html_e( 'Make it a thoughtful journey', 'explore-jawai' ); ?></p>
			<h2 class="ej-serif"><?php echo wp_kses_post( __( 'Plan less hurriedly.<br>Experience more deeply.', 'explore-jawai' ) ); ?></h2>
			<p class="ej-home-section-intro"><?php esc_html_e( 'Start with the essentials: when to visit, how to arrive, how much time to allow and how to travel responsibly.', 'explore-jawai' ); ?></p>
		</div>
		<div class="ej-home-steps">
			<div class="ej-home-step">
				<strong aria-hidden="true">01</strong>
				<h3><?php esc_html_e( 'Choose your season', 'explore-jawai' ); ?></h3>
				<p><?php esc_html_e( 'Consider weather, outdoor comfort and whether birdwatching is part of your plans.', 'explore-jawai' ); ?></p>
			</div>
			<div class="ej-home-step">
				<strong aria-hidden="true">02</strong>
				<h3><?php esc_html_e( 'Allow enough time', 'explore-jawai' ); ?></h3>
				<p><?php esc_html_e( 'A two-night stay can give first-time visitors room for a safari, the dam landscape and a slower cultural experience.', 'explore-jawai' ); ?></p>
			</div>
			<div class="ej-home-step">
				<strong aria-hidden="true">03</strong>
				<h3><?php esc_html_e( 'Travel with care', 'explore-jawai' ); ?></h3>
				<p><?php esc_html_e( 'Follow local guidance, respect wildlife distance and ask before photographing residents.', 'explore-jawai' ); ?></p>
			</div>
		</div>
	</div>
</section>
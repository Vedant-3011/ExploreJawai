<?php
/**
 * Contact page sidebar card (prototype contact.html).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<aside class="ej-contact-side-card">
	<h3 class="ej-serif"><?php esc_html_e( 'Before you enquire', 'explore-jawai' ); ?></h3>
	<p class="ej-contact-empty-note"><?php esc_html_e( 'For accurate planning, it helps to know your travel dates, group size, starting city and the experiences you are interested in.', 'explore-jawai' ); ?></p>
	<a class="ej-contact-side-link" href="<?php echo esc_url( home_url( '/how-to-reach-jawai-rajasthan/' ) ); ?>"><?php esc_html_e( 'How to reach Jawai', 'explore-jawai' ); ?></a>
	<a class="ej-contact-side-link" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Browse travel articles', 'explore-jawai' ); ?></a>
	<div class="ej-contact-callout">
		<p><strong><?php esc_html_e( 'Explore Jawai', 'explore-jawai' ); ?></strong> <?php esc_html_e( 'is a travel blog, not a booking desk. Use this form for travel questions and editorial feedback — for lodge reservations, contact your lodge or operator directly.', 'explore-jawai' ); ?></p>
	</div>
</aside>
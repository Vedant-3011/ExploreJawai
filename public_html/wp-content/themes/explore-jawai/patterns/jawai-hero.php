<?php
/**
 * Title: Jawai hero
 * Slug: explore-jawai/jawai-hero
 * Categories: explore-jawai
 * Inserter: yes
 *
 * @package Explore_Jawai
 */

$hero = esc_url( get_theme_file_uri( 'assets/images/jawai-hero.jpg' ) );
?>

<!-- wp:cover {"url":"<?php echo esc_url( $hero ); ?>","dimRatio":55,"overlayColor":"contrast","minHeight":75,"minHeightUnit":"vh","align":"full"} -->
<div class="wp-block-cover alignfull" style="min-height:75vh">
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-55 has-background-dim"></span>
	<img class="wp-block-cover__image-background" alt="<?php esc_attr_e( 'Leopard in golden light, Jawai region', 'explore-jawai' ); ?>" src="<?php echo esc_url( $hero ); ?>" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->
		<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size">Explore Jawai</h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
		<p class="has-text-align-center has-large-font-size">Leopard hills, Rabari villages, and safari stories from Rajasthan's granite country.</p>
		<!-- /wp:paragraph -->
	</div>
</div>
<!-- /wp:cover -->
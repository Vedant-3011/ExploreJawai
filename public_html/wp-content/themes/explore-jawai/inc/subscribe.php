<?php
/**
 * Footer email subscription (stores locally; notifies site admin).
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render footer subscribe form.
 *
 * @return string
 */
function explore_jawai_subscribe_form_html() {
	$status = '';
	if ( isset( $_GET['ej_subscribed'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '1' === $_GET['ej_subscribed'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$status = 'success';
		} elseif ( '0' === $_GET['ej_subscribed'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$status = 'error';
		}
	}

	ob_start();
	?>
	<div class="explore-jawai-subscribe">
		<h3 class="explore-jawai-footer-heading"><?php esc_html_e( 'Subscribe', 'explore-jawai' ); ?></h3>
		<p class="explore-jawai-subscribe-lead"><?php esc_html_e( 'New Jawai safari guides and field notes — one email when we publish, no spam.', 'explore-jawai' ); ?></p>
		<?php if ( 'success' === $status ) : ?>
			<p class="explore-jawai-subscribe-notice explore-jawai-subscribe-notice--success" role="status">
				<?php esc_html_e( 'Thanks — you are on the list. Watch your inbox for the next story from Jawai.', 'explore-jawai' ); ?>
			</p>
		<?php elseif ( 'error' === $status ) : ?>
			<p class="explore-jawai-subscribe-notice explore-jawai-subscribe-notice--error" role="alert">
				<?php esc_html_e( 'That email could not be saved. Try again or contact us.', 'explore-jawai' ); ?>
			</p>
		<?php endif; ?>
		<form class="explore-jawai-subscribe-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="explore_jawai_subscribe" />
			<?php wp_nonce_field( 'explore_jawai_subscribe', 'explore_jawai_subscribe_nonce' ); ?>
			<label class="screen-reader-text" for="explore-jawai-subscribe-email"><?php esc_html_e( 'Email address', 'explore-jawai' ); ?></label>
			<div class="explore-jawai-subscribe-row">
				<input
					id="explore-jawai-subscribe-email"
					class="explore-jawai-subscribe-input"
					type="email"
					name="subscriber_email"
					required
					autocomplete="email"
					placeholder="<?php echo esc_attr__( 'Your email', 'explore-jawai' ); ?>"
				/>
				<button type="submit" class="explore-jawai-subscribe-button wp-element-button">
					<?php esc_html_e( 'Subscribe', 'explore-jawai' ); ?>
				</button>
			</div>
		</form>
		<p class="explore-jawai-subscribe-fine"><?php esc_html_e( 'Jawai travel tips only. Unsubscribe anytime — email admin@explorejawai.com.', 'explore-jawai' ); ?></p>
	</div>
	<?php
	return (string) ob_get_clean();
}

add_shortcode(
	'explore_jawai_subscribe',
	static function () {
		return explore_jawai_subscribe_form_html();
	}
);

/**
 * Handle subscribe POST.
 */
function explore_jawai_handle_subscribe() {
	if ( ! isset( $_POST['explore_jawai_subscribe_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['explore_jawai_subscribe_nonce'] ) ), 'explore_jawai_subscribe' ) ) {
		explore_jawai_subscribe_redirect( false );
	}

	$email = isset( $_POST['subscriber_email'] ) ? sanitize_email( wp_unslash( $_POST['subscriber_email'] ) ) : '';
	if ( ! is_email( $email ) ) {
		explore_jawai_subscribe_redirect( false );
	}

	$list   = get_option( 'explore_jawai_subscribers', array() );
	$list   = is_array( $list ) ? $list : array();
	$exists = in_array( $email, $list, true );

	if ( ! $exists ) {
		$list[] = $email;
		update_option( 'explore_jawai_subscribers', $list, false );

		$admin = get_option( 'admin_email' );
		if ( $admin ) {
			wp_mail(
				$admin,
				sprintf( '[Explore Jawai] New subscriber: %s', $email ),
				sprintf( "New footer subscription:\n\n%s\n\nTotal subscribers: %d\n", $email, count( $list ) )
			);
		}
	}

	explore_jawai_subscribe_redirect( true );
}

/**
 * @param bool $ok Success flag.
 */
function explore_jawai_subscribe_redirect( $ok ) {
	$target = wp_get_referer();
	if ( ! $target || ! wp_validate_redirect( $target, home_url( '/' ) ) ) {
		$target = home_url( '/' );
	}
	$target = remove_query_arg( 'ej_subscribed', $target );
	$target = add_query_arg( 'ej_subscribed', $ok ? '1' : '0', $target );
	wp_safe_redirect( $target );
	exit;
}

add_action( 'admin_post_nopriv_explore_jawai_subscribe', 'explore_jawai_handle_subscribe' );
add_action( 'admin_post_explore_jawai_subscribe', 'explore_jawai_handle_subscribe' );
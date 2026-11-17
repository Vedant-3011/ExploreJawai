<?php
/**
 * Contact page form — emails site admin.
 *
 * @package Explore_Jawai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return string
 */
function explore_jawai_contact_form_html() {
	$status = '';
	if ( isset( $_GET['ej_contact'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '1' === $_GET['ej_contact'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$status = 'success';
		} elseif ( '0' === $_GET['ej_contact'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$status = 'error';
		}
	}

	ob_start();
	?>
	<div class="explore-jawai-contact-form-wrap">
		<?php if ( 'success' === $status ) : ?>
			<p class="explore-jawai-contact-notice explore-jawai-contact-notice--success" role="status">
				<?php esc_html_e( 'Thanks — your message was sent. We usually reply within 2–3 business days.', 'explore-jawai' ); ?>
			</p>
		<?php elseif ( 'error' === $status ) : ?>
			<p class="explore-jawai-contact-notice explore-jawai-contact-notice--error" role="alert">
				<?php esc_html_e( 'Something went wrong. Email us directly at admin@explorejawai.com.', 'explore-jawai' ); ?>
			</p>
		<?php endif; ?>
		<form class="explore-jawai-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="explore_jawai_contact" />
			<?php wp_nonce_field( 'explore_jawai_contact', 'explore_jawai_contact_nonce' ); ?>
			<p class="explore-jawai-contact-field">
				<label for="ej-contact-name"><?php esc_html_e( 'Your name', 'explore-jawai' ); ?></label>
				<input id="ej-contact-name" type="text" name="contact_name" required autocomplete="name" />
			</p>
			<p class="explore-jawai-contact-field">
				<label for="ej-contact-email"><?php esc_html_e( 'Email', 'explore-jawai' ); ?></label>
				<input id="ej-contact-email" type="email" name="contact_email" required autocomplete="email" />
			</p>
			<p class="explore-jawai-contact-field">
				<label for="ej-contact-subject"><?php esc_html_e( 'Subject', 'explore-jawai' ); ?></label>
				<input id="ej-contact-subject" type="text" name="contact_subject" required placeholder="<?php echo esc_attr__( 'Safari planning, correction, partnership…', 'explore-jawai' ); ?>" />
			</p>
			<p class="explore-jawai-contact-field">
				<label for="ej-contact-message"><?php esc_html_e( 'Message', 'explore-jawai' ); ?></label>
				<textarea id="ej-contact-message" name="contact_message" rows="5" required></textarea>
			</p>
			<p class="explore-jawai-contact-hp" aria-hidden="true">
				<label for="ej-contact-website">Website</label>
				<input id="ej-contact-website" type="text" name="contact_website" tabindex="-1" autocomplete="off" />
			</p>
			<p class="explore-jawai-contact-actions">
				<button type="submit" class="explore-jawai-contact-submit wp-element-button"><?php esc_html_e( 'Send message', 'explore-jawai' ); ?></button>
			</p>
		</form>
	</div>
	<?php
	return (string) ob_get_clean();
}

add_shortcode(
	'explore_jawai_contact',
	static function () {
		return explore_jawai_contact_form_html();
	}
);

/**
 * Handle contact form.
 */
function explore_jawai_handle_contact() {
	if ( ! isset( $_POST['explore_jawai_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['explore_jawai_contact_nonce'] ) ), 'explore_jawai_contact' ) ) {
		explore_jawai_contact_redirect( false );
	}

	if ( ! empty( $_POST['contact_website'] ) ) { // Honeypot.
		explore_jawai_contact_redirect( true );
	}

	$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$subject = isset( $_POST['contact_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_subject'] ) ) : '';
	$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $subject || '' === $message ) {
		explore_jawai_contact_redirect( false );
	}

	$admin = get_option( 'admin_email' );
	$mail_subject = sprintf( '[Explore Jawai] %s', $subject );
	$body         = sprintf(
		"Contact form — explorejawai.com\n\nName: %s\nEmail: %s\n\n%s\n",
		$name,
		$email,
		$message
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' );
	$sent    = $admin ? wp_mail( $admin, $mail_subject, $body, $headers ) : false;

	explore_jawai_contact_redirect( $sent );
}

/**
 * @param bool $ok Success.
 */
function explore_jawai_contact_redirect( $ok ) {
	$target = wp_get_referer();
	if ( ! $target || ! wp_validate_redirect( $target, home_url( '/contact/' ) ) ) {
		$target = home_url( '/contact/' );
	}
	$target = remove_query_arg( 'ej_contact', $target );
	$target = add_query_arg( 'ej_contact', $ok ? '1' : '0', $target );
	wp_safe_redirect( $target );
	exit;
}

add_action( 'admin_post_nopriv_explore_jawai_contact', 'explore_jawai_handle_contact' );
add_action( 'admin_post_explore_jawai_contact', 'explore_jawai_handle_contact' );
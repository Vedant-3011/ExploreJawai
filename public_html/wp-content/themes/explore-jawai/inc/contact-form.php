<?php
/**
 * Contact page form — emails site admin (prototype contact.html).
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
	<div class="ej-contact-form-wrap">
		<?php if ( 'success' === $status ) : ?>
			<p class="ej-contact-notice ej-contact-notice--success" role="status"><?php esc_html_e( 'Thanks — your enquiry was sent. We usually reply within 2–3 business days.', 'explore-jawai' ); ?></p>
		<?php elseif ( 'error' === $status ) : ?>
			<p class="ej-contact-notice ej-contact-notice--error" role="alert"><?php esc_html_e( 'Something went wrong. Email us directly at admin@explorejawai.com.', 'explore-jawai' ); ?></p>
		<?php endif; ?>
		<form class="ej-contact-form ej-form-grid" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="explore_jawai_contact" />
			<?php wp_nonce_field( 'explore_jawai_contact', 'explore_jawai_contact_nonce' ); ?>
			<div class="ej-form-field">
				<label for="ej-contact-name"><?php esc_html_e( 'Your name', 'explore-jawai' ); ?></label>
				<input id="ej-contact-name" type="text" name="contact_name" placeholder="<?php esc_attr_e( 'Name', 'explore-jawai' ); ?>" required autocomplete="name" />
			</div>
			<div class="ej-form-field">
				<label for="ej-contact-email"><?php esc_html_e( 'Email address', 'explore-jawai' ); ?></label>
				<input id="ej-contact-email" type="email" name="contact_email" placeholder="you@example.com" required autocomplete="email" />
			</div>
			<div class="ej-form-field">
				<label for="ej-contact-date"><?php esc_html_e( 'Approximate travel date', 'explore-jawai' ); ?></label>
				<input id="ej-contact-date" type="date" name="contact_travel_date" />
			</div>
			<div class="ej-form-field">
				<label for="ej-contact-travellers"><?php esc_html_e( 'Number of travellers', 'explore-jawai' ); ?></label>
				<select id="ej-contact-travellers" name="contact_travellers">
					<option value=""><?php esc_html_e( 'Select', 'explore-jawai' ); ?></option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3-4">3–4</option>
					<option value="5-8">5–8</option>
					<option value="9+">9+</option>
				</select>
			</div>
			<div class="ej-form-field ej-form-field--full">
				<label for="ej-contact-interest"><?php esc_html_e( 'What interests you most?', 'explore-jawai' ); ?></label>
				<select id="ej-contact-interest" name="contact_interest">
					<option value="Leopard and wildlife experiences"><?php esc_html_e( 'Leopard and wildlife experiences', 'explore-jawai' ); ?></option>
					<option value="Birdwatching and nature"><?php esc_html_e( 'Birdwatching and nature', 'explore-jawai' ); ?></option>
					<option value="Village and cultural experiences"><?php esc_html_e( 'Village and cultural experiences', 'explore-jawai' ); ?></option>
					<option value="Itinerary and travel planning"><?php esc_html_e( 'Itinerary and travel planning', 'explore-jawai' ); ?></option>
					<option value="General enquiry"><?php esc_html_e( 'General enquiry', 'explore-jawai' ); ?></option>
				</select>
			</div>
			<div class="ej-form-field ej-form-field--full">
				<label for="ej-contact-message"><?php esc_html_e( 'Your message', 'explore-jawai' ); ?></label>
				<textarea id="ej-contact-message" name="contact_message" rows="5" placeholder="<?php esc_attr_e( 'Tell us what you would like help with...', 'explore-jawai' ); ?>" required></textarea>
			</div>
			<div class="ej-form-field ej-form-field--full ej-form-field--actions">
				<p class="ej-contact-hp" aria-hidden="true">
					<label for="ej-contact-website">Website</label>
					<input id="ej-contact-website" type="text" name="contact_website" tabindex="-1" autocomplete="off" />
				</p>
				<button type="submit" class="ej-editorial-btn ej-contact-submit"><?php esc_html_e( 'Prepare enquiry ↗', 'explore-jawai' ); ?></button>
				<p class="ej-contact-form-note"><?php esc_html_e( 'Your details are sent to the site editor by email — not stored in this form for marketing.', 'explore-jawai' ); ?></p>
			</div>
		</form>
	</div>
	<?php
	return explore_jawai_compact_markup( (string) ob_get_clean() );
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

	$name        = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email       = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$message     = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';
	$interest    = isset( $_POST['contact_interest'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_interest'] ) ) : __( 'General enquiry', 'explore-jawai' );
	$travel_date = isset( $_POST['contact_travel_date'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_travel_date'] ) ) : '';
	$travellers  = isset( $_POST['contact_travellers'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_travellers'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $message ) {
		explore_jawai_contact_redirect( false );
	}

	$admin        = get_option( 'admin_email' );
	$mail_subject = sprintf( '[Explore Jawai] %s', $interest );
	$body         = sprintf(
		"Contact form — explorejawai.com\n\nName: %s\nEmail: %s\nTravel date: %s\nTravellers: %s\nInterest: %s\n\n%s\n",
		$name,
		$email,
		$travel_date ? $travel_date : '(not specified)',
		$travellers ? $travellers : '(not specified)',
		$interest,
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
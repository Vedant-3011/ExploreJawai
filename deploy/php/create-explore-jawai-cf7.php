<?php
/**
 * Seed Contact Form 7 "Explore Jawai Enquiry" (does not overwrite an existing form).
 *
 * @package Explore_Jawai
 */

if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::error( 'Contact Form 7 is not active.' );
	}
	return;
}

$option_key = 'explore_jawai_cf7_form_id';
$form_id    = (int) get_option( $option_key, 0 );

if ( $form_id > 0 && 'wpcf7_contact_form' === get_post_type( $form_id ) && 'publish' === get_post_status( $form_id ) ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::success( "Contact Form 7 already linked (ID {$form_id}). User edits in admin are kept." );
	}
	return;
}

$form_body = <<<'FORM'
<div class="ej-form-field">
<label>Your name</label>
[text* your-name autocomplete:name placeholder "Name"]
</div>
<div class="ej-form-field">
<label>Email address</label>
[email* your-email autocomplete:email placeholder "you@example.com"]
</div>
<div class="ej-form-field">
<label>Approximate travel date</label>
[date travel-date]
</div>
<div class="ej-form-field">
<label>Number of travellers</label>
[select travellers "Select" "1" "2" "3–4" "5–8" "9+"]
</div>
<div class="ej-form-field ej-form-field--full">
<label>What interests you most?</label>
[select your-interest default:1 "Leopard and wildlife experiences" "Birdwatching and nature" "Village and cultural experiences" "Itinerary and travel planning" "General enquiry"]
</div>
<div class="ej-form-field ej-form-field--full">
<label>Your message</label>
[textarea* your-message placeholder "Tell us what you would like help with..."]
</div>
<div class="ej-form-field ej-form-field--full ej-form-field--actions">
[submit class:ej-editorial-btn class:ej-contact-submit "Prepare enquiry ↗"]
<p class="ej-contact-form-note">Your details are sent to the site editor by email — not stored in this form for marketing.</p>
</div>
FORM;

$contact_form = WPCF7_ContactForm::get_template(
	array(
		'title' => 'Explore Jawai Enquiry',
	)
);

$properties = $contact_form->get_properties();
$properties['form'] = $form_body;

$admin_email                    = get_option( 'admin_email' );
$properties['mail']['active']   = true;
$properties['mail']['subject']  = '[Explore Jawai] [your-interest]';
/* Site From address — Reply-To uses visitor email (avoids SPF/send failures). */
$properties['mail']['sender']   = 'Explore Jawai <wordpress@explorejawai.com>';
$properties['mail']['recipient'] = $admin_email ? $admin_email : '[admin_email]';
$properties['mail']['body']     = "Contact form — explorejawai.com\n\nName: [your-name]\nEmail: [your-email]\nTravel date: [travel-date]\nTravellers: [travellers]\nInterest: [your-interest]\n\n[your-message]";
$properties['mail']['additional_headers'] = 'Reply-To: [your-email]';

$properties['messages']['mail_sent_ok'] = 'Thanks — your enquiry was sent. We usually reply within 2–3 business days.';
$properties['messages']['mail_sent_ng'] = 'Something went wrong. Email us directly at admin@explorejawai.com.';
$properties['messages']['validation_error'] = 'Please check the highlighted fields and try again.';
$properties['messages']['acceptance_missing'] = 'Please accept the terms before sending.';
$properties['messages']['spam'] = 'Your message could not be sent. Please try again later.';
$properties['messages']['invalid_required'] = 'This field is required.';
$properties['messages']['invalid_email'] = 'Please enter a valid email address.';

$contact_form->set_properties( $properties );
$saved_id = $contact_form->save();

if ( ! $saved_id ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::error( 'Could not save Contact Form 7.' );
	}
	return;
}

update_option( $option_key, (int) $saved_id, false );

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::success( "Created Contact Form 7 (ID {$saved_id}). Edit at wp-admin → Contact → Contact Forms." );
}
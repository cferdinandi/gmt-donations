<?php


	/**
	 * Send thank you email to donor
	 * @param  integer $id     The form ID
	 * @param  array   $status The donation data
	 */
	function gmt_donations_send_thank_you_email( $id, $status ) {

		// Variables
		$options = gmt_donations_get_theme_options();
		$details = get_post_meta( $donations['id'], 'gmt_donation_details', true );
		$in_honor = gmt_donations_create_message_in_honor( $details, $status, 'email' );
		$currencies = gmt_donations_settings_field_currency_choices();
		$site_name = get_bloginfo('name');
		$domain = gmt_donations_get_site_domain();
		$headers = 'From: ' . $site_name . ' <donotreply@' . $domain . '>' . "\r\n";

		// If recurring
		if ( $status['recurring'] && $details['recurring_send_email'] === 'on' && !empty( $details['recurring_email_subject'] ) && !empty( $details['recurring_email_text'] ) ) {

			// Variables
			$subject = str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['recurring_email_subject'] );
			$message = str_replace( '[in_honor]', $in_honor, str_replace( '[email]', $status['email'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['recurring_email_text'] ) ) );

			// Send email
			@wp_mail( get_option('admin_email'), $subject, $message, $headers );
			return;

		}

		// If big donor
		if ( $details['big_donor_send_email'] === 'on' && $details['big_donor_amount'] <= $status['amount'] && !empty( $details['big_donor_email_subject'] ) && !empty( $details['big_donor_email_text'] ) ) {

			// Variables
			$subject = str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['big_donor_email_subject'] );
			$message = str_replace( '[in_honor]', $in_honor, str_replace( '[email]', $status['email'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['big_donor_email_text'] ) ) );

			// Send email
			@wp_mail( get_option('admin_email'), $subject, $message, $headers );
			return;

		}

		// Standard donations
		if ( $details['standard_send_email'] === 'on' && !empty( $details['standard_email_subject'] ) && !empty( $details['standard_email_text'] ) ) {

			// Variables
			$subject = str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['standard_email_subject'] );
			$message = str_replace( '[in_honor]', $in_honor, str_replace( '[email]', $status['email'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['standard_email_text'] ) ) );

			// Send email
			$mail = @wp_mail( get_option('admin_email'), $subject, $message, $headers );

		}

	}
	add_action( 'gmt_donation_success', 'gmt_donations_send_thank_you_email', 10, 2 );



	/**
	 * Send email to "in honor of" honoree
	 * @param  integer $id     The form ID
	 * @param  array   $status The donation data
	 */
	function gmt_donations_send_in_honor_honoree_email( $id, $status ) {

		// Only run if honoree named and email address provided
		if ( empty( $status['in_honor_name'] ) || empty( $status['in_honor_email'] ) ) return;

		// Get settings
		$options = gmt_donations_get_theme_options();
		$details = get_post_meta( $donations['id'], 'gmt_donation_details', true );

		// Only run if honorees should receive emails
		if ( $details['send_in_honor_email'] === 'off' ) return;

		// Variables
		$currencies = gmt_donations_settings_field_currency_choices();
		$site_name = get_bloginfo('name');
		$domain = gmt_donations_get_site_domain();
		$headers = 'From: ' . $site_name . ' <donotreply@' . $domain . '>' . "\r\n";

		// Create email content
		$subject = str_replace( '[donor]', $donor, str_replace( '[name]', $status['in_honor_name'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['in_honor_email_subject'] ) ) );
		$message = str_replace( '[donor]', $donor, str_replace( '[name]', $status['in_honor_name'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['in_honor_email_message'] ) ) );

		// Send email
		@wp_mail( get_option('admin_email'), $subject, $message, $headers );

	}
	add_action( 'gmt_donation_success', 'gmt_donations_send_in_honor_honoree_email', 10, 2 );
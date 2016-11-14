<?php


	/**
	 * Get the donation form status message
	 * @return  array Status message
	 */
	function gmt_donation_form_get_status_message() {
		return array(
			'no_amount' => __( 'Please select a donation amount.', 'gmt_donations' ),
			'cancelled' => __( 'Your donation was cancelled.', 'gmt_donations' ),
			'success' => __( 'Thank you for your donation!', 'gmt_donations' ),
			'failed' => __( 'We were unable to process your donation. Please try again.', 'gmt_donations' ),
		);
	}



	/**
	 * Setup the PayPal API credentials
	 * @param  array $options The donations plugin settings
	 * @return array          The PayPal credentials
	 */
	function gmt_donations_get_paypal_credentials( $options ) {
		return array(
			'version' => $options['paypal_api_version'],
			'username' => $options['paypal_' . $options['api_mode'] . '_username'],
			'password' => $options['paypal_' . $options['api_mode'] . '_password'],
			'signature' => $options['paypal_' . $options['api_mode'] . '_signature'],
			'currency' => $options['currency'],
		);
	}



	function gmt_donations_create_goal_thermometer( $form ) {

		// Check if goal thermometer should be displayed
		if ( $form['details']['show_goal'] === 'off' || empty( $form['details']['goal_target'] ) ) return;

		// Variables
		$currencies = gmt_donations_settings_field_currency_choices();
		$total = get_post_meta( $form['id'], 'gmt_donations_total_donated', true );
		$progress = $total / $form['details']['goal_target'] * 100;
		$percent = ( $progress < 100 ? $progress : 100 );

		return
			'<strong>' . __( 'Goal', 'gmt_donations' ) . ':</strong> ' . esc_html( $currencies[$form['options']['currency']]['symbol'] ) . esc_html( number_format( $form['details']['goal_target'] ) ) .
			'<div class="gmt-donation-form-goal-thermometer ' . ( $percent > 24 ? ' ' : 'gmt-donation-form-goal-thermometer-small-percentage ' ) . ( $percent < 100 ? '' : 'gmt-donation-form-goal-thermometer-goal-met' ) . '" id="gmt-donation-form-goal-thermometer-' . $form['id'] . '">' .
			    '<div class="gmt-donation-form-goal-thermometer-progress" id="gmt-donation-form-goal-thermometer-progress-' . $form['id'] . '" style="width: ' . $percent . '%;">' .
			        ( $percent > 24 ? esc_html( $currencies[$form['options']['currency']]['symbol'] ) . esc_html( number_format( $total ) ) : '' ) .
			    '</div>' .
			    ( $percent > 24 ? '' : esc_html( $currencies[$form['options']['currency']]['symbol'] ) . esc_html( number_format( $total ) ) ) .
			'</div>';

	}



	/**
	 * Create donation form buttons
	 * @param  string $button Which button to create (`stripe` or `paypal`)
	 * @param  array  $form   The form data
	 * @return string         The button markup
	 */
	function gmt_donations_create_donation_form_buttons( $button, $form ) {

		// If the Stripe gateway is activated
		if ( $button === 'stripe' && $form['options']['gateways_stripe'] === 'on' ) {
			return
				'<button ' .
					'type="submit" ' .
					'class="gmt-donation-form-button gmt-donation-form-button-stripe" ' .
					'id="gmt-donation-form-button-stripe-' . esc_attr( $form['id'] ) . '" ' .
					'data-business-name="' . stripslashes( esc_attr( $form['options']['business_name'] ) ) . '" ' .
					'data-image="' . esc_attr( $form['options']['business_logo'] ) . '" ' .
					'data-description="' . get_the_excerpt( $form['id'] ) . '" ' .
					'disabled' .
				'>' .
					stripslashes( $form['options']['stripe_button_label'] ) .
				'</button> ';
		}

		// If the PayPal gateway is activated
		if ( $button === 'paypal' && $form['options']['gateways_paypal_express_checkout'] === 'on' ) {
			return
				'<button ' .
					'type="submit" ' .
					'class="gmt-donation-form-button gmt-donation-form-button-paypal" ' .
					'id="gmt-donation-form-button-paypal-' . esc_attr( $form['id'] ) . '" ' .
				'>' .
					stripslashes( $form['options']['paypal_button_label'] ) .
				'</button>';
		}

		return '';

	}



	/**
	 * Create donation form table rows
	 * @param  array  $form   The form data
	 * @return string         The form table markup
	 */
	function gmt_donations_create_donation_form_rows( $form ) {

		// Variables
		$currency = gmt_donations_settings_field_currency_choices();
		$amount_rows = '';

		// Create donation table
		foreach( $form['amounts'] as $key => $amount ) {
			$amount_rows .=
				'<tr>' .
					'<td>' .
						'<label for="gmt-donation-form-' . $amount['amount'] . '">' .
							'<input type="radio" name="gmt_donation_form[amount]" value="' . $amount['amount'] . '" class="gmt-donation-form-amount" id="gmt-donation-form-' . $amount['amount'] . '" ' . checked( $form['details']['default_amount'], $key, false ) . '> ' .
							esc_html( $currency[$form['options']['currency']]['symbol'] . $amount['amount'] ) .
						'</label>' .
					'</td>' .
					'<td><label for="gmt-donation-form-' . $amount['amount'] . '">' . stripslashes( $amount['description'] ) . '</label></td>' .
				'</tr>';
		}
		if ( $form['details']['show_other_amount'] === 'on' ) {
			$amount_rows .=
				'<tr>' .
					'<td>' .
						'<label for="gmt-donation-form-other-' . esc_attr( $form['id'] ) . '">' .
							__( 'Other', 'gmt_donations' ) . ':' .
						'</label>' .
					'</td>' .
					'<td>' .
						( $form['options']['hide_other_currency'] === 'on' ? '' : '<span class="gmt-donation-form-other-currency" id="gmt-donation-form-other-currency-' . esc_attr( $form['id'] ) . '">' . $currency[$form['options']['currency']]['symbol'] . '</span>' ) .
						'<input type="number" min="1" step="any" name="gmt_donation_form[other]" class="gmt-donation-form-other" id="gmt-donation-form-other-' . esc_attr( $form['id'] ) . '" value="">'.
					'</td>' .
				'</tr>';
		}

		return $amount_rows;

	}



	/**
	 * Create the donation form recurring donation checkbox
	 * @param  array  $form   The form data
	 * @return string         The checkbox markup
	 */
	function gmt_donations_create_donation_form_recurring( $form ) {

		// Check if recurring checkbox should be shown
		if ( $form['details']['show_recurring'] === 'off' ) return;

		return
			'<div class="gmt-donation-form-checkbox gmt-donation-form-recurring" id="gmt-donation-form-recurring-' . esc_attr( $form['id'] ) .'">' .
				'<label>' .
					'<input type="checkbox" name="gmt_donation_form[recurring]" class="gmt-donation-form-recurring-label" data-panel-label="' . __( 'Donate', 'gmt_donations' ) . '" data-panel-label-recurring="' . __( 'Donate Monthly', 'gmt_donations' ) . '"> ' .
					stripslashes( $form['options']['recurring_label'] ) .
				'</label>' .
			'</div>';
	}



	/**
	 * Create the donation form "in honor of" fields
	 * @param  array  $form   The form data
	 * @return string         The in honor of markup
	 */
	function gmt_donations_create_donation_form_in_honor( $form ) {

		// Check if recurring checkbox should be shown
		if ( $form['details']['show_in_honor'] === 'off' ) return;

		$in_honor_email_fields = '';
		if ( $form['details']['send_in_honor_email'] === 'on' ) {
			$in_honor_email_fields =
				'<div class="gmt-donation-form-in-honor-email">' .
					'<label>' . stripslashes( $form['options']['in_honor_label_email'] ) . '</label>' .
					'<input type="email" name="gmt_donation_form[in_honor_email]" id="gmt-donation-form-in-honor-email-' . esc_attr( $form['id'] ) . '">' .
				'</div>' .
				'<div class="gmt-donation-form-in-honor-donor">' .
					'<label>' . stripslashes( $form['options']['in_honor_label_donor'] ) . '</label>' .
					'<input type="email" name="gmt_donation_form[in_honor_donor]" id="gmt-donation-form-in-honor-donor-' . esc_attr( $form['id'] ) . '">' .
				'</div>';
		}


		return
			'<div class="gmt-donation-form-checkbox gmt-donation-form-in-honor" id="gmt-donation-form-in-honor-' . esc_attr( $form['id'] ) .'" style="display:none;visibility:hidden;">' .
				'<label>' .
					'<input type="checkbox" class="gmt-donation-form-in-honor-toggle" name="gmt_donation_form[in_honor]" data-in-honor="#gmt-donation-form-in-honor-content-' . esc_attr( $form['id'] ) . '"> ' .
					stripslashes( $form['options']['in_honor_label'] ) .
				'</label>' .
				'<div class="gmt-donation-form-in-honor-content" id="gmt-donation-form-in-honor-content-' . esc_attr( $form['id'] ) . '" style="display:none;visibility:hidden;">' .
					'<div class="gmt-donation-form-in-honor-message" id="gmt-donation-form-in-honor-message-' . esc_attr( $form['id'] ) . '">' . stripslashes( $form['options']['in_honor_message'] ) . '</div>' .
					'<div class="gmt-donation-form-in-honor-name">' .
						'<label>' . stripslashes( $form['options']['in_honor_label_name'] ) . '</label>' .
						'<input type="text" name="gmt_donation_form[in_honor_name]" id="gmt-donation-form-in-honor-name-' . esc_attr( $form['id'] ) . '">' .
					'</div>' .
					$in_honor_email_fields .
				'</div>' .
			'</div>';

	}


	/**
	 * Create the donations form
	 * @param  array $args  The form arguments and attributes
	 * @return string       The rendered form markup
	 */
	function gmt_donations_create_donation_form( $form ) {

		// Make sure values are provided
		if ( empty( $form['amounts'] ) || !is_array( $form['amounts'] ) ) return;

		// Messages
		$status_messages = gmt_donation_form_get_status_message();
		$get_status = !empty( $form['status'] ) && array_key_exists( $form['status'], $status_messages ) ? $status_messages[$form['status']] : null;

		// Create goal thermometer
		$thermometer = gmt_donations_create_goal_thermometer( $form );

		// Create buttons
		$button_stripe = gmt_donations_create_donation_form_buttons( 'stripe', $form );
		$button_paypal = gmt_donations_create_donation_form_buttons( 'paypal', $form );

		// Create form content
		$amount_rows = gmt_donations_create_donation_form_rows( $form );
		$recurring = gmt_donations_create_donation_form_recurring( $form );
		$in_honor = gmt_donations_create_donation_form_in_honor( $form );

		// Create donation table
		return
			( $form['options']['api_mode'] === 'test' ? '<div class="gmt-donation-form-test-mode">' . __( 'You are in test/sandbox mode.', 'gmt_donations' ) . '</div>' : '' ) .
			$thermometer .
			'<form class="gmt-donation-form" id="gmt-donation-form-' . esc_attr( $form['id'] ) . '" name="gmt-donation-form-' . esc_attr( $form['id'] ) . '" action="" method="post">' .

				'<table class="gmt-donation-form-table">' .
					'<tr>' .
						'<th>' . $form['details']['label_amount'] . '</th>' .
						'<th>' . $form['details']['label_description'] . '</th>' .
					'</tr>' .
					$amount_rows .
				'</table>' .

				$recurring .
				$in_honor .

				'<div class="gmt-donation-form-tarpit">' .
					'<label for="gmt_donation_form_email">' . __( 'Email', 'gmt_donations' ) . '</label>' .
					'<input type="email" id="gmt_donation_form_email" name="gmt_donation_form[email]" value="">' .
				'</div>' .

				'<div class="gmt-donation-form-alert" id="gmt-donation-form-alert-' . esc_attr( $form['id'] ) . '" data-error-no-amount="' . $status_messages['no_amount'] . '" tabindex="-1" style="outline: 0;' . ( empty( $get_status ) ? 'display: none; visibility: hidden;' : '' ) . '">' .
					( empty( $get_status ) ? '' : $get_status ) .
				'</div>' .

				'<div class="gmt-donation-form-actions" id="gmt-donation-form-actions-' . esc_attr( $form['id'] ) . '">' .
					$button_stripe .
					$button_paypal .
				'</div>' .

				wp_nonce_field( 'gmt_donations_form_nonce', 'gmt_donations_form_process', true, false ) .
				'<input type="hidden" name="gmt_donation_form[id]" value="' . esc_attr( $form['id'] ) . '">' .
				'<input type="hidden" id="gmt_donations_tarpit_time" name="gmt_donation_form[tarpit_time]" value="' . esc_attr( current_time( 'timestamp' ) ) . '">' .
				'<input type="hidden" data-gmt-donations-stripe-key="' . $form['options']['stripe_' . $form['options']['api_mode'] . '_publishable'] . '">' .

			'</form>';

	}



	/**
	 * Create [in_honor] placeholder content for messages
	 * @param  array  $details Donation form details
	 * @param  array  $status  Donation details
	 * @param  string $context Is the message for a `thank_you` message or `email`?
	 * @return string          The message
	 */
	function gmt_donations_create_message_in_honor( $details, $status, $context = 'thank_you' ) {

		// Bail if no honoree
		if ( empty( $status['in_honor_name'] ) ) return '';

		// If no honoree email is provided
		if ( empty( $status['in_honor_email'] ) ) {
			return str_replace( '[name]', $status['in_honor_name'], $details['in_honor_' . $context . '_no_email'] );
		}

		// If an honoree email is provided
		$donor = empty( $status['in_honor_donor'] ) ? __( 'anonymous donor' ) : $status['in_honor_donor'];
		return str_replace( '[donor]', $donor, str_replace( '[email]', $status['in_honor_email'], str_replace( '[name]', $status['in_honor_name'], $details['in_honor_' . $context . '_email'] ) ) );

	}



	/**
	 * Create a thank-you message
	 * @param  array $details The form details
	 * @param  array $status  The donation details
	 * @return string         Thank-you message markup
	 */
	function gmt_donations_create_thank_you_message( $details, $status ) {

		// Get the currency
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();
		$in_honor = gmt_donations_create_message_in_honor( $details, $status );

		// If recurring
		if ( $status['recurring'] && $details['recurring_unique_message'] === 'on' && !empty( $details['recurring_thank_you_text'] ) ) {
			$title = empty( $details['recurring_thank_you_title'] ) ? '' : '<h2>' . str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['recurring_thank_you_title'] ) . '</h2>';
			$text = str_replace( '[in_honor]', $in_honor, str_replace( '[email]', $status['email'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['recurring_thank_you_text'] ) ) );
			return $title . $text;
		}

		// If big donor
		if ( $details['big_donor_unique_message'] === 'on' && $details['big_donor_amount'] <= $status['amount'] && !empty( $details['big_donor_thank_you_text'] ) ) {
			$title = empty( $details['big_donor_thank_you_title'] ) ? '' : '<h2>' . str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['big_donor_thank_you_title'] ) . '</h2>';
			$text = str_replace( '[in_honor]', $in_honor, str_replace( '[email]', $status['email'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['big_donor_thank_you_text'] ) ) );
			return $title . $text;
		}

		// Standard donations
		$title = empty( $details['standard_thank_you_title'] ) ? '' : '<h2>' . str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['standard_thank_you_title'] ) . '</h2>';
		$text = str_replace( '[in_honor]', $in_honor, str_replace( '[email]', $status['email'], str_replace( '[amount]', $currencies[$options['currency']]['symbol'] . $status['amount'], $details['standard_thank_you_text'] ) ) );
		return $title . $text;

	}



	/**
	 * Donation form shortcode
	 * @return string Shortcode markup
	 */
	function gmt_donations_form( $atts ) {

		// Get shortcode atts
		$donations = shortcode_atts( array(
			'id' => null,
		), $atts );

		// Prevent this content from caching
		define('DONOTCACHEPAGE', TRUE);

		// Make sure ID is provided
		if ( empty( $donations['id'] ) ) return;

		// Variables
		$options = gmt_donations_get_theme_options();
		$amounts = get_post_meta( $donations['id'], 'gmt_donation_amounts', true );
		$details_saved = get_post_meta( $donations['id'], 'gmt_donation_details', true );
		$details_defaults = gmt_donations_metabox_details_defaults();
		$details = wp_parse_args( $details_saved, $details_defaults );
		$status = gmt_donations_get_session( 'gmt_donations_status', true );

		// Make sure post is a donation form
		if ( get_post_type( $donations['id'] ) !== 'gmt_donation_forms' ) return;

		// Display donation thank-you message
		if ( $status['status'] === 'success' ) {
			return wpautop( stripslashes( gmt_donations_create_thank_you_message( $details, $status ) ), true );
		}

		// Generate form
		return gmt_donations_create_donation_form(array(
			'id' => $donations['id'],
			'options' => $options,
			'amounts' => $amounts,
			'details' => $details,
			'status' => $status['status'],
		));

	}
	add_shortcode( 'donation_form', 'gmt_donations_form' );



	/**
	 * Create a new subscription plan in Stripe
	 * @param  string $key      Stripe API key
	 * @param  string $amount   Amount of money for recurring plan
	 * @param  string $currency Currency to use for the subscription plan
	 */
	function gmt_donations_create_stripe_plan( $key, $amount, $currency ) {
		$currencies = gmt_donations_settings_field_currency_choices();
		try {
			\Stripe\Stripe::setApiKey( $key );
			$plan = \Stripe\Plan::create(array(
				'amount' => $amount * 100,
				'interval' => 'month',
				'name' => __( 'Monthly Donation' ,'gmt_donations' ) . ': ' . $currencies[$currency]['symbol'] . $amount,
				'currency' => $currency,
				'id' => 'donation_' . $amount,
			));
		} catch (Exception $e) {
			return $e;
		}
		return 'donation_' . $amount;
	}



	/**
	 * Get the Stripe subscription plan based on donation amount
	 * @param  string $key      Stripe API key
	 * @param  string $amount   Amount of money for recurring plan
	 * @param  string $currency Currency to use for the subscription plan
	 */
	function gmt_donations_get_stripe_plan( $key, $amount, $currency ) {

		// Get existing plans
		try {
			\Stripe\Stripe::setApiKey( $key );
			$plans = \Stripe\Plan::all();
		} catch (Exception $e) {
			$plans = null;
		}

		// Convert $plans to array
		$plans = $plans->__toArray(true);

		// Check that data was returned
		if ( !array_key_exists( 'data', $plans ) ) return 'failed';

		// If no plans exist, create the needed plan
		if ( empty( $plans['data'] ) ) {
			return gmt_donations_create_stripe_plan( $key, $amount, $currency );
		}

		// If plan already exists, return it's ID
		foreach( $plans['data'] as $plan ) {
			if ( $plan['id'] === 'donation_' . $amount ) {
				return 'donation_' . $amount;
			}
		}

		// Otherwise, create a new plan
		return gmt_donations_create_stripe_plan( $key, $amount, $currency );

	};



	/**
	 * Get the donor's email from the Stripe token
	 */
	function gmt_donations_get_email_from_stripe( $token, $status ) {

		// If checkout failed, return null
		if ( $status === 'failed' ) return null;

		// Variables
		$options = gmt_donations_get_theme_options();

		// Get email from token
		try {
			\Stripe\Stripe::setApiKey( $options['stripe_' . $options['api_mode'] . '_secret']);
			$stripeinfo = \Stripe\Token::retrieve( $token );
			$email = $stripeinfo->email;
		} catch (Exception $e) {
			return null;
		}
		return $email;

	}


	/**
	 * Process Stripe Donation
	 */
	function gmt_donations_process_stripe( $token, $amount, $recurring ) {

		// Variables
		$options = gmt_donations_get_theme_options();

		// Setup monthly donation
		if ( $recurring ) {
			$plan = gmt_donations_get_stripe_plan( $options['stripe_' . $options['api_mode'] . '_secret'], $amount, $options['currency'] );
			if ( $plan === 'failed' ) return 'failed';
			try {
				\Stripe\Stripe::setApiKey( $options['stripe_' . $options['api_mode'] . '_secret'] );
				$customer = \Stripe\Customer::create(array(
						'card' => $token,
						'plan' => $plan,
					)
				);
			} catch (Exception $e) {
				return 'failed';
			}
			return 'success';
		}

		// Charge card for one-time donation
		try {
			\Stripe\Stripe::setApiKey( $options['stripe_' . $options['api_mode'] . '_secret'] );
			$charge = \Stripe\Charge::create(array(
					'amount' => $amount * 100,
					'currency' => $options['currency'],
					'card' => $token,
				)
			);
		} catch (Exception $e) {
			return 'failed';
		}
		return 'success';

	}



	function gmt_donations_process_paypal() {

		// Get PayPal token
		$paypal = gmt_donations_get_session( 'gmt_donations_paypal_data', true );

		// Check to see if token exists and buyer has approved transaction
		if ( empty( $paypal ) || empty( $paypal['token'] ) || !isset( $_GET['PayerID'] ) ) return;

		// Variables
		$options = gmt_donations_get_theme_options();
		$credentials = gmt_donations_get_paypal_credentials( $options );
		$referer = gmt_donations_clean_url( 'PayerID', gmt_donations_clean_url( 'token', $paypal['referer'] ) );
		$error = gmt_donations_clean_url( 'PayerID', gmt_donations_clean_url( 'token', $paypal['error'] ) );

		// Get checkout data from PayPal
		$getCheckoutArgs = array(
			'METHOD' => 'GetExpressCheckoutDetails',
			'TOKEN' => $paypal['token'],
		);
		$checkoutResponse = gmt_paypal_call_api( $getCheckoutArgs, $credentials, $options['api_mode'] );

		// Make sure response didn't fail
		if ( is_wp_error( $checkoutResponse ) || $checkoutResponse['ACK'] !== 'Success' || !array_key_exists( 'PAYERID', $checkoutResponse ) ) {
			gmt_donations_set_session( 'gmt_donations_status', array( 'status' => 'failed' ) );
			wp_safe_redirect( $error );
			exit;
		}

		if ( $paypal['recurring'] ) {
			// Monthly donations
			$doCheckoutArgs = array(
				'METHOD' => 'CreateRecurringPaymentsProfile',
				'TOKEN' => $paypal['token'],
				'PAYERID' => $checkoutResponse['PAYERID'],
				'PROFILESTARTDATE' => date( 'Y-m-d', ( current_time( 'timestamp', true ) + (25 * 3600) ) ) . 'T' . date( 'H:i:s', ( current_time( 'timestamp', true ) + (25 * 3600) ) ) . 'Z',
				'DESC' => __( 'Monthly Donation', 'gmt_donations' ),
				'BILLINGPERIOD' => 'Month',
				'BILLINGFREQUENCY' => 1,
				'AMT' => $checkoutResponse['PAYMENTREQUEST_0_AMT'],
				'CURRENCYCODE' => strtoupper( $options['currency'] ),
				'MAXFAILEDPAYMENTS' => 3,
			);
		} else {
			// One-time donation
			$doCheckoutArgs = array(
				'METHOD' => 'DoExpressCheckoutPayment',
				'TOKEN' => $paypal['token'],
				'PAYERID' => $checkoutResponse['PAYERID'],
				'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE',
				'PAYMENTREQUEST_0_AMT' => $checkoutResponse['PAYMENTREQUEST_0_AMT'],
				'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper( $options['currency'] ),
			);
		}
		$doCheckoutResponse = gmt_paypal_call_api( $doCheckoutArgs, $credentials, $options['api_mode'] );

		// Verify that donation was completed
		if ( is_wp_error( $doCheckoutResponse ) || $doCheckoutResponse['ACK'] !== 'Success' ) {
			gmt_donations_set_session( 'gmt_donations_status', array( 'status' => 'failed' ) );
			wp_safe_redirect( $error );
			exit;
		}

		// If donation was successful, display success message
		$status = array(
			'type' => 'paypal',
			'status' => 'success',
			'amount' => $checkoutResponse['AMT'],
			'recurring' => $paypal['recurring'],
			'in_honor_name' => $paypal['in_honor_name'],
			'in_honor_email' => $paypal['in_honor_email'],
			'in_honor_donor' => $paypal['in_honor_donor'],
			'email' => $checkoutResponse['EMAIL'],
		);
		gmt_donations_set_session( 'gmt_donations_status', $status );

		// Emit action hook
		do_action( 'gmt_donation_success', $paypal['id'], $status );

		wp_safe_redirect( $referer );
		exit;

	}
	add_action( 'init', 'gmt_donations_process_paypal' );



	/**
	 * Process PayPal Donation
	 */
	function gmt_donations_get_paypal_authorization( $paypal ) {

		// Variables
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();
		$credentials = gmt_donations_get_paypal_credentials( $options );

		// Clean URLs
		$referer = gmt_donations_clean_url( 'PayerID', gmt_donations_clean_url( 'token', $paypal['referer'] ) );
		$error = gmt_donations_clean_url( 'PayerID', gmt_donations_clean_url( 'token', $paypal['error'] ) );

		// Request token
		$setCheckoutArgs = array(
			'METHOD' => 'SetExpressCheckout',
			'RETURNURL' => esc_url_raw( $referer ),
			'CANCELURL' => esc_url_raw( $referer ),
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Authorization',
			'PAYMENTREQUEST_0_AMT' => number_format( $paypal['amount'], 2 ),
			'PAYMENTREQUEST_0_DESC' => 'Donation: ' . html_entity_decode( $currencies[$options['currency']]['symbol'] ) . number_format( $paypal['amount'], 2 ),
			'PAYMENTREQUEST_0_CURRENCYCODE' => strtoupper( $options['currency'] ),
			'SOLUTIONTYPE' => 'Sole',
			'NOSHIPPING' => 1,
			'ALLOWNOTE' => 0,
			'PAGESTYLE' => $options['paypal_page_style'],
		);
		if ( $paypal['recurring'] ) {
			$setCheckoutArgs['L_BILLINGTYPE0'] = 'RecurringPayments';
			$setCheckoutArgs['L_BILLINGAGREEMENTDESCRIPTION0'] = __( 'Monthly Donation', 'gmt_donations' );
		}
		$setCheckoutResponse = gmt_paypal_call_api( $setCheckoutArgs, $credentials, $options['api_mode'] );

		// If response is not successful, display an error message
		if ( is_wp_error( $setCheckoutResponse ) || $setCheckoutResponse['ACK'] !== 'Success' ) {
			gmt_donations_set_session( 'gmt_donations_status', array( 'status' => 'failed' ) );
			wp_safe_redirect( esc_url_raw( $error ), 302 );
			exit;
		}

		// Store token for 24 minutes
		$paypal['token'] = $setCheckoutResponse['TOKEN'];
		gmt_donations_set_session( 'gmt_donations_paypal_data', $paypal );

		// If response successful, send to PayPal for authorization.
		$getAuthorizationArgs = array(
			'token' => $setCheckoutResponse['TOKEN'],
			'useraction' => 'commit',
		);
		gmt_paypal_send_to_express_checkout( $getAuthorizationArgs, $options['api_mode'] );

	}



	/**
	 * Process donation form
	 */
	function gmt_donations_process_form() {

		// Check that form was submitted
		if ( !isset( $_POST['gmt_donations_form_process'] ) ) return;

		// Verify data came from proper screen
		if ( !wp_verify_nonce( $_POST['gmt_donations_form_process'], 'gmt_donations_form_nonce' ) ) {
			die( 'Security check' );
		}

		// Make sure donation form fields are provided
		if ( !isset( $_POST['gmt_donation_form'] ) ) return;

		// Variables
		$referer = gmt_donations_get_url();
		$error = $referer . '#gmt-donation-form-alert-' . $_POST['gmt_donation_form']['id'];

		// Make sure a donation amount is provided
		if ( empty( $_POST['gmt_donation_form']['other'] ) && !isset( $_POST['gmt_donation_form']['amount'] ) ) {
			gmt_donations_set_session( 'gmt_donations_status', array( 'status' => 'no_amount' ) );
			wp_safe_redirect( $error, 302 );
			exit;
		}

		// Verify honeypots
		if ( !empty( $_POST['gmt_donation_form']['email'] ) || !isset( $_POST['gmt_donation_form']['tarpit_time'] ) || current_time( 'timestamp' ) - $_POST['gmt_donation_form']['tarpit_time'] < 1 ) {
			gmt_donations_set_session( 'gmt_donations_status', array( 'status' => 'failed' ) );
			wp_safe_redirect( $referer, 302 );
			exit;
		}

		// Get the donation info
		$amount = !empty( $_POST['gmt_donation_form']['other'] ) ? $_POST['gmt_donation_form']['other'] : $_POST['gmt_donation_form']['amount'];
		$recurring = isset( $_POST['gmt_donation_form']['recurring'] ) ? true : false;
		$in_honor = isset( $_POST['gmt_donation_form']['in_honor'] ) ? true : false;

		// Charge card
		if ( isset( $_POST['stripe_token'] ) ) {
			$process = gmt_donations_process_stripe( $_POST['stripe_token'], $amount, $recurring );
			$status = array(
				'type' => 'stripe',
				'status' => $process,
				'amount' => wp_filter_nohtml_kses( $amount ),
				'recurring' => $recurring,
				'in_honor_name' => ( $in_honor ? wp_filter_nohtml_kses( $_POST['gmt_donation_form']['in_honor_name'] ) : null ),
				'in_honor_email' => ( $in_honor ? wp_filter_nohtml_kses( $_POST['gmt_donation_form']['in_honor_email'] ) : null ),
				'in_honor_donor' => ( $in_honor ? wp_filter_nohtml_kses( $_POST['gmt_donation_form']['in_honor_donor'] ) : null ),
				'email' => gmt_donations_get_email_from_stripe( $_POST['stripe_token'], $process ),
			);
			gmt_donations_set_session( 'gmt_donations_status', $status );
		} else {
			$paypal = array(
				'id' => $_POST['gmt_donation_form']['id'],
				'amount' => wp_filter_nohtml_kses( $amount ),
				'recurring' => $recurring,
				'in_honor_name' => ( $in_honor ? wp_filter_nohtml_kses( $_POST['gmt_donation_form']['in_honor_name'] ) : null ),
				'in_honor_email' => ( $in_honor ? wp_filter_nohtml_kses( $_POST['gmt_donation_form']['in_honor_email'] ) : null ),
				'in_honor_donor' => ( $in_honor ? wp_filter_nohtml_kses( $_POST['gmt_donation_form']['in_honor_donor'] ) : null ),
				'referer' => $referer,
				'error' => $error,
			);
			$process = gmt_donations_get_paypal_authorization( $paypal );
		}

		// If payment is successful, emit action hook
		if ( !empty( $status ) && array_key_exists( 'status', $status ) && $status['status'] === 'success' ) {
			do_action( 'gmt_donation_success', $_POST['gmt_donation_form']['id'], $status );
		}

		// Redirect user
		wp_safe_redirect( $referer, 302 );
		exit;

	}
	add_action( 'init', 'gmt_donations_process_form' );



	/**
	 * Load scripts conditionally
	 */
	function gmt_donations_load_front_end_scripts() {

		// Variables
		global $post;
		$options = gmt_donations_get_theme_options();
		if ( !is_a( $post, 'WP_Post' ) || !has_shortcode( $post->post_content, 'donation_form') ) return;

		// If Stripe is active, load stripe scripts
		if ( $options['gateways_stripe'] === 'on' ) {
			wp_enqueue_script( 'stripe-checkout', 'https://checkout.stripe.com/checkout.js', null, false, true );
			wp_enqueue_script( 'gmt-donations-stripe', plugins_url( '../includes/js/gmt-stripe.js' , __FILE__ ), array( 'stripe-checkout' ), false, true );
		}

		// Load donation form handler
		wp_enqueue_script( 'gmt-donation-forms', plugins_url( '../includes/js/gmt-donations.js' , __FILE__ ), null, false, true );

	}
	add_action( 'wp_enqueue_scripts', 'gmt_donations_load_front_end_scripts' );



	/**
	 * Load styles conditionally
	 */
	function gmt_donations_load_front_end_styles() {

		// Only run if [donation_form] shortcode is used
		global $post;
		if ( !is_a( $post, 'WP_Post' ) || !has_shortcode( $post->post_content, 'donation_form') ) return;
		?>
			<style type="text/css">.gmt-donation-form-tarpit{display:none;visibility:hidden;}</style>
		<?php

		// Add additional form styles
		$options = gmt_donations_get_theme_options();
		if ( $options['disable_css'] === 'on' ) return;
		?>
			<style type="text/css">
				.gmt-donation-form-other-currency{display:inline-block;float:left;margin-right:0.5em;}
				.gmt-donation-form-other{width:88%;}
				.gmt-donation-form-goal-thermometer{height:1.5625em;margin-bottom:1.5625em;overflow:hidden;background-color:#e5e5e5;text-align:center;}
				.gmt-donation-form-goal-thermometer-small-percentage{text-align:left;}
				.gmt-donation-form-goal-thermometer-progress{float:left;width:0;height:100%;color:#ffffff;background-color:#880e14;box-sizing:border-box;}
				.gmt-donation-form-goal-thermometer-small-percentage .gmt-donation-form-goal-thermometer-progress{margin-right:0.5em;}
				.gmt-donation-form-goal-thermometer-goal-met .gmt-donation-form-goal-thermometer-progress{background-color:#377f31;}
				.gmt-donation-form-alert,.gmt-donation-form-test-mode{color:#43070a;background-color:#e7cfd0;border:.0725em solid #d5abad;margin-bottom:1.5625em;padding:.25em .5em;border-radius:.0725em}
				.gmt-donation-form-in-honor-content{background-color:#f7f7f7;padding:1em;}
				.gmt-donation-form-in-honor-message{font-style:italic;}
			</style>
		<?php
	}
	add_action( 'wp_head', 'gmt_donations_load_front_end_styles' );
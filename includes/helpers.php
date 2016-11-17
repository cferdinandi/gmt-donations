<?php

	/**
	 * WP Session Helpers
	 */

	// Set session data
	function gmt_donations_set_session( $name, $value, $sanitize = null ) {

		// Start session
		$wp_session = WP_Session::get_instance();

		// Sanitize data
		if ( $sanitize === 'post' ) {
			$value = wp_filter_post_kses( $value );
		} elseif ( $sanitize === 'nohtml' ) {
			$value = wp_filter_nohtml_kses( $value );
		}

		// Store session value
		$wp_session[$name] = $value;

	}

	// Get session data
	function gmt_donations_get_session( $name, $unset = false ) {

		// Start session
		$wp_session = WP_Session::get_instance();

		// Store session value
		$value = $wp_session[$name];

		// If value is array, transform it
		if ( is_object( $value ) ) {
			$value->toArray();
		}

		// Unset session value
		if ( $unset ) {
			unset( $wp_session[$name] );
		}

		return $value;

	}

	// Unset session data
	function gmt_donations_unset_session( $name ) {
		$wp_session = WP_Session::get_instance();
		unset( $wp_session[$name] );
	}



	/**
	 * URL Helpers
	 * Get, sanitize, and process URLs.
	 */

	// Get and sanitize the current URL
	function gmt_donations_get_url() {
		$url  = @( $_SERVER['HTTPS'] != 'on' ) ? 'http://' . $_SERVER['SERVER_NAME'] :  'https://' . $_SERVER['SERVER_NAME'];
		$url .= ( $_SERVER['SERVER_PORT'] !== 80 ) ? ":" . $_SERVER['SERVER_PORT'] : '';
		$url .= $_SERVER['REQUEST_URI'];
		return $url;
	}

	// Get the site domain and remove the www.
	function gmt_donations_get_site_domain() {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		return $sitename;
	}

	// Prepare URL for status string
	function gmt_donations_prepare_url( $url ) {

		// If URL has a '?', add an '&'.
		// Otherwise, add a '?'.
		$url_status = strpos($url, '?');
		if ( $url_status === false ) {
			$concate = '?';
		}
		else {
			$concate = '&';
		}

		return $url . $concate;

	}


	// Remove a $_GET variable from the URL
	function gmt_donations_clean_url( $variable, $url ) {
		$new_url = preg_replace('/(?:&|(\?))' . $variable . '=[^&]*(?(1)&|)?/i', '$1', $url);
		$last_char = substr( $new_url, -1 );
		if ( $last_char == '?' ) {
			$new_url = substr($new_url, 0, -1);
		}
		return $new_url;
	}



	/**
	 * Make sure the API is activated and setup
	 */
	function gmt_donations_api_is_activated( $api = null ) {

		// Sanity check
		if ( empty( $api ) ) return false;

		// Get the settings
		$options = gmt_donations_get_theme_options();

		// Check Stripe
		if ( $api === 'stripe' ) {
			$activated = empty( $options['stripe_' . $options['api_mode'] . '_secret'] ) || empty( $options['stripe_' . $options['api_mode'] . '_publishable'] ) ? false : true;
		}

		// Check PayPal
		if ( $api === 'paypal' ) {
			$activated = empty( $options['paypal_' . $options['api_mode'] . '_username'] ) || empty( $options['paypal_' . $options['api_mode'] . '_password'] ) || empty( $options['paypal_' . $options['api_mode'] . '_signature'] ) ? false : true;
		}

		return $activated;
	}



	//
	// Reporting
	//

	/**
	 * Get total amount of money donated
	 * @param  object $donations All donations to total up
	 * @return integer           Total donated
	 */
	function gmt_donations_get_total_donated( $donations ) {
		$total = 0;
		foreach( $donations as $donation ) {
			$amount = get_post_meta( $donation->ID, 'gmt_donations_amount', true );
			$total += $amount;
		}
		return $total;
	}



	/**
	 * Get donations or donors by date
	 * @param  array   $donations  Donations
	 * @param  integer $start      Start date as a timestamp
	 * @param  integer $end        End date as a timestampe
	 * @return array               Donations that fall beteween the dates
	 */
	function gmt_donations_get_donations_by_date( $donations, $start, $end ) {
		$donations_by_date = array();
		foreach( $donations as $key => $donation ) {
			$donation_time = strtotime( $donation->post_date_gmt );
			if ( $donation_time >= $start && $donation_time <= $end ) {
				$donations_by_date[] = $donation;
			}
		}
		return $donations_by_date;
	}
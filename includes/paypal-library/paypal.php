<?php

	/**
	 * GMT Paypal
	 * A Library to help make working with the PayPal API a tad easier.
	 *
	 * This was forked from the PayPal API framework
	 * @link https://wordpress.org/plugins/paypal-framework/
	 *
	 * @license GPL
	 */


	/**
	 * Get the PayPal endpoint based on live or sandbox mode
	 * @return string The API endpoint
	 */
	function gmt_paypal_get_endpoint() {
		return array(
			'test' => 'https://api-3t.sandbox.paypal.com/nvp',
			'live' => 'https://api-3t.paypal.com/nvp',
		);
	}



	function gmt_paypal_get_checkout_url() {
		return array(
			'test' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
			'live' => 'https://www.paypal.com/cgi-bin/webscr',
		);
	}



	/**
	 * This function creates a name value pair (nvp) string from a given array, object, or string.  It also makes sure that all "names" in the nvp are all caps (which PayPal requires) and that anything that's not specified uses the defaults
	 * @param array|object|string $req Request to format
	 * @return string NVP string
	 */
	function gmt_paypal_prep_request( $req, $credentials ) {
		$defaults = array(
			'VERSION'		=> $credentials['version'],
			'PWD'			=> $credentials['password'],
			'USER'			=> $credentials['username'],
			'SIGNATURE'		=> $credentials['signature'],
			'CURRENCYCODE'	=> strtoupper( $credentials['currency'] ),
		);
		return wp_parse_args( $req, $defaults );
	}



	/**
	 * Function to perform the API call to PayPal using API signature
	 * @param string|array $args Parameters needed for call
	 * @return array On success return associtive array containing the response from the server.
	 */
	function gmt_paypal_call_api( $args, $credentials, $mode ) {

		// Get the API endpoints
		$endpoint = gmt_paypal_get_endpoint();

		// Setup request parameters
		$params = array(
			'httpversion' => '1.1',
			'body'		=> gmt_paypal_prep_request( $args, $credentials ),
			'sslverify' => apply_filters( 'paypal_framework_sslverify', false ),
			'timeout' 	=> 30,
		);

		// Send the request
		$resp = wp_remote_post( $endpoint[$mode], $params );

		// If the response was valid, decode it and return it.  Otherwise return a WP_Error
		if ( !is_wp_error( $resp ) && $resp['response']['code'] >= 200 && $resp['response']['code'] < 300 ) {
			return wp_parse_args( $resp['body'] );
		} else {
			if ( !is_wp_error($resp) ) {
				$resp = new WP_Error('http_request_failed', $resp['response']['message'], $resp['response']);
			}
			return $resp;
		}

	}



	/**
	 * Direct the user to Express Checkout
	 * @param string|array $args Parameters needed for call.  *token is REQUIRED*
	 */
	function gmt_paypal_send_to_express_checkout( $args, $mode ) {

		// Variables
		$url = gmt_paypal_get_checkout_url();
		$args['cmd'] = '_express-checkout';
		$nvp_string = build_query( $args );

		// Redirect user to PayPal
		wp_redirect( $url[$mode] . '?' . $nvp_string );
		exit;

	}
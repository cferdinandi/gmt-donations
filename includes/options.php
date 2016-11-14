<?php

/**
 * Theme Options v1.1.0
 * Adjust theme settings from the admin dashboard.
 * Find and replace `gmt_donations` with your own namepspacing.
 *
 * Created by Michael Fields.
 * https://gist.github.com/mfields/4678999
 *
 * Forked by Chris Ferdinandi
 * http://gomakethings.com
 *
 * Free to use under the MIT License.
 * http://gomakethings.com/mit/
 */


	/**
	 * Theme Options Fields
	 * Each option field requires its own uniquely named function. Select options and radio buttons also require an additional uniquely named function with an array of option choices.
	 */

	//
	// General Options
	//

	function gmt_donations_settings_field_api_mode_choices() {
		$radio_buttons = array(
			'test' => array(
				'value' => 'test',
				'label' => __( 'Test/Sandbox', 'gmt_donations' )
			),
			'live' => array(
				'value' => 'live',
				'label' => __( 'Live', 'gmt_donations' )
			),
		);

		return apply_filters( 'gmt_donations_settings_field_api_mode_choices', $radio_buttons );
	}

	function gmt_donations_settings_field_api_mode() {
		$options = gmt_donations_get_theme_options();

		foreach ( gmt_donations_settings_field_api_mode_choices() as $button ) {
		?>
		<div class="layout">
			<label class="description">
				<input type="radio" name="gmt_donations_theme_options[api_mode]" value="<?php echo esc_attr( $button['value'] ); ?>" <?php checked( $options['api_mode'], $button['value'] ); ?> />
				<?php echo $button['label']; ?>
			</label>
		</div>
		<?php
		}
	}

	function gmt_donations_settings_field_currency_choices() {
		$select_options = array(
			'aud' => array(
				'value'  => 'aud',
				'label'  => __( 'Australian Dollar', 'gmt_donations' ),
				'symbol' => '&#36;',
			),
			'cad' => array(
				'value'  => 'cad',
				'label'  => __( 'Canadian Dollar', 'gmt_donations' ),
				'symbol' => '&#36;',
			),
			'czk' => array(
				'value'  => 'czk',
				'label'  => __( 'Czech Koruna', 'gmt_donations' ),
				'symbol' => 'Kč',
			),
			'dkk' => array(
				'value'  => 'dkk',
				'label'  => __( 'Danish Krone', 'gmt_donations' ),
				'symbol' => 'kr.',
			),
			'eur' => array(
				'value'  => 'eur',
				'label'  => __( 'Euro', 'gmt_donations' ),
				'symbol' => '&euro;',
			),
			'hkd' => array(
				'value'  => 'hkd',
				'label'  => __( 'Hong Kong Dollar', 'gmt_donations' ),
				'symbol' => 'HK&#36;',
			),
			'huf' => array(
				'value'  => 'huf',
				'label'  => __( 'Hungarian Forint', 'gmt_donations' ),
				'symbol' => '‎Ft',
			),
			'ils' => array(
				'value'  => 'ils',
				'label'  => __( 'Israeli New Sheqel', 'gmt_donations' ),
				'symbol' => '&#8362;',
			),
			'jpy' => array(
				'value'  => 'jpy',
				'label'  => __( 'Japanese Yen', 'gmt_donations' ),
				'symbol' => '&yen;',
			),
			'mxn' => array(
				'value'  => 'mxn',
				'label'  => __( 'Mexican Peso', 'gmt_donations' ),
				'symbol' => 'Mex&#36;',
			),
			'nok' => array(
				'value'  => 'nok',
				'label'  => __( 'Norwegian Krone', 'gmt_donations' ),
				'symbol' => 'kr',
			),
			'nzd' => array(
				'value'  => 'nzd',
				'label'  => __( 'New Zealand Dollar', 'gmt_donations' ),
				'symbol' => '&#36;',
			),
			'pln' => array(
				'value'  => 'pln',
				'label'  => __( 'Polish Zloty', 'gmt_donations' ),
				'symbol' => 'zł',
			),
			'gbp' => array(
				'value'  => 'gbp',
				'label'  => __( 'British Pound', 'gmt_donations' ),
				'symbol' => '&pound;',
			),
			'sgd' => array(
				'value'  => 'sgd',
				'label'  => __( 'Singapore Dollar', 'gmt_donations' ),
				'symbol' => '&#36;',
			),
			'sek' => array(
				'value'  => 'sek',
				'label'  => __( 'Swedish Krona', 'gmt_donations' ),
				'symbol' => 'kr',
			),
			'chf' => array(
				'value'  => 'chf',
				'label'  => __( 'Swiss Franc', 'gmt_donations' ),
				'symbol' => 'Fr.',
			),
			'usd' => array(
				'value'  => 'usd',
				'label'  => __( 'US Dollar', 'gmt_donations' ),
				'symbol' => '&#36;',
			),
		);

		return apply_filters( 'gmt_donations_settings_field_currency_choices', $select_options );
	}

	function gmt_donations_settings_field_currency() {
		$options = gmt_donations_get_theme_options();
		?>
		<select name="gmt_donations_theme_options[currency]" id="gmt_donations_theme_options_currency">
			<?php foreach( gmt_donations_settings_field_currency_choices() as $option ) : ?>
				<option value="<?php echo esc_attr( $option['value'] ); ?>" <?php selected( $option['value'], $options['currency'] ); ?>><?php echo esc_attr( $option['label'] . ' - ' . $option['symbol'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<label class="description" for="gmt_donations_theme_options_currency"><?php _e( 'Which currency to use for your donations', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_gateways() {
		$options = gmt_donations_get_theme_options();
		?>
		<div class="layout">
			<label>
				<input type="checkbox" name="gmt_donations_theme_options[gateways_stripe]" id="gateways_stripe" <?php checked( 'on', $options['gateways_stripe'] ); ?> />
				<?php _e( 'Stripe', 'gmt_donations' ); ?>
			</label>
			<br>
			<label>
				<input type="checkbox" name="gmt_donations_theme_options[gateways_paypal_express_checkout]" id="gateways_paypal_express_checkout" <?php checked( 'on', $options['gateways_paypal_express_checkout'] ); ?> />
				<?php _e( 'PayPal Express Checkout', 'gmt_donations' ); ?>
			</label>
		</div>
		<?php
	}


	//
	// Business Details
	//

	function gmt_donations_settings_field_business_name() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[business_name]" class="regular-text" id="business_name" value="<?php echo stripslashes( esc_attr( $options['business_name'] ) ); ?>">
		<label class="description" for="business_name"><?php _e( 'The name of your business (to be displayed in Stripe and PayPal', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_business_logo() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[business_logo]" class="regular-text" id="business_logo" value="<?php echo esc_attr( $options['business_logo'] ); ?>"> <button class="button" data-stripe-image="#business_logo"><?php _e( 'Upload', 'gmt_stripe' ); ?></button>
		<label class="description" for="business_logo"><?php _e( 'Your business logo (to be displayed in Stripe and PayPal', 'gmt_donations' ); ?></label>
		<?php
	}


	//
	// Stripe API
	//

	function gmt_donations_settings_field_stripe_api_test() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[stripe_test_secret]" class="regular-text" id="stripe_test_secret" value="<?php echo esc_attr( $options['stripe_test_secret'] ); ?>">
		<label class="description" for="stripe_test_secret"><?php _e( 'Your Stripe test secret key', 'gmt_donations' ); ?></label>
		<br>
		<input type="text" name="gmt_donations_theme_options[stripe_test_publishable]" class="regular-text" id="stripe_test_publishable" value="<?php echo esc_attr( $options['stripe_test_publishable'] ); ?>">
		<label class="description" for="stripe_test_publishable"><?php _e( 'Your Stripe test publishable key', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_stripe_api_live() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[stripe_live_secret]" class="regular-text" id="stripe_live_secret" value="<?php echo esc_attr( $options['stripe_live_secret'] ); ?>">
		<label class="description" for="stripe_live_secret"><?php _e( 'Your Stripe live secret key', 'gmt_donations' ); ?></label>
		<br>
		<input type="text" name="gmt_donations_theme_options[stripe_live_publishable]" class="regular-text" id="stripe_live_publishable" value="<?php echo esc_attr( $options['stripe_live_publishable'] ); ?>">
		<label class="description" for="stripe_live_publishable"><?php _e( 'Your Stripe live publishable key', 'gmt_donations' ); ?></label>
		<?php
	}


	//
	// PayPal API
	//

	function gmt_donations_settings_field_paypal_api_test() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[paypal_test_username]" class="regular-text" id="paypal_test_username" value="<?php echo esc_attr( $options['paypal_test_username'] ); ?>">
		<label class="description" for="paypal_test_username"><?php _e( 'Your PayPal sandbox username', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[paypal_test_password]" class="regular-text" id="paypal_test_password" value="<?php echo esc_attr( $options['paypal_test_password'] ); ?>">
		<label class="description" for="paypal_test_password"><?php _e( 'Your PayPal sandbox password', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[paypal_test_signature]" class="regular-text" id="paypal_test_signature" value="<?php echo esc_attr( $options['paypal_test_signature'] ); ?>">
		<label class="description" for="paypal_test_signature"><?php _e( 'Your PayPal sandbox signature', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_paypal_api_live() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[paypal_live_username]" class="regular-text" id="paypal_live_username" value="<?php echo esc_attr( $options['paypal_live_username'] ); ?>">
		<label class="description" for="paypal_live_username"><?php _e( 'Your PayPal live username', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[paypal_live_password]" class="regular-text" id="paypal_live_password" value="<?php echo esc_attr( $options['paypal_live_password'] ); ?>">
		<label class="description" for="paypal_live_password"><?php _e( 'Your PayPal live password', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[paypal_live_signature]" class="regular-text" id="paypal_live_signature" value="<?php echo esc_attr( $options['paypal_live_signature'] ); ?>">
		<label class="description" for="paypal_live_signature"><?php _e( 'Your PayPal live signature', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_paypal_api_version() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[paypal_api_version]" class="small-text" id="paypal_api_version" value="<?php echo esc_attr( $options['paypal_api_version'] ); ?>">
		<label class="description" for="paypal_api_version"><?php _e( 'Which version of the PayPal API do you want to use?', 'gmt_donations' ); ?></label>
		<?php
	}


	//
	// Labels
	//

	function gmt_donations_settings_field_recurring_label() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[recurring_label]" class="regular-text" id="recurring_label" value="<?php echo stripslashes( esc_attr( $options['recurring_label'] ) ); ?>">
		<label class="description" for="recurring_label"><?php _e( 'Label for the recurring donation checkbox', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_in_honor_labels() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[in_honor_label]" class="regular-text" id="in_honor_label" value="<?php echo stripslashes( esc_attr( $options['in_honor_label'] ) ); ?>">
		<label class="description" for="in_honor_label"><?php _e( 'Label for the "in honor of" checkbox', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[in_honor_label_name]" class="regular-text" id="in_honor_label_name" value="<?php echo stripslashes( esc_attr( $options['in_honor_label_name'] ) ); ?>">
		<label class="description" for="in_honor_label_name"><?php _e( 'Label for the "in honor of" honoree name', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[in_honor_label_email]" class="regular-text" id="in_honor_label_email" value="<?php echo stripslashes( esc_attr( $options['in_honor_label_email'] ) ); ?>">
		<label class="description" for="in_honor_label_email"><?php _e( 'Label for the "in honor of" honoree email', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[in_honor_label_donor]" class="regular-text" id="in_honor_label_donor" value="<?php echo stripslashes( esc_attr( $options['in_honor_label_donor'] ) ); ?>">
		<label class="description" for="in_honor_label_donor"><?php _e( 'Label for the "in honor of" donor name', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_in_honor_message() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[in_honor_message]" class="large-text" id="in_honor_message" value="<?php echo stripslashes( esc_attr( $options['in_honor_message'] ) ); ?>">
		<label class="description" for="in_honor_message"><?php _e( 'Message above the "in honor of" fields', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_button_labels() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[stripe_button_label]" class="regular-text" id="stripe_button_label" value="<?php echo stripslashes( esc_attr( $options['stripe_button_label'] ) ); ?>">
		<label class="description" for="stripe_button_label"><?php _e( 'Label for the Stripe donate by credit card button', 'gmt_donations' ); ?></label>
		<br>

		<input type="text" name="gmt_donations_theme_options[paypal_button_label]" class="regular-text" id="paypal_button_label" value="<?php echo stripslashes( esc_attr( $options['paypal_button_label'] ) ); ?>">
		<label class="description" for="paypal_button_label"><?php _e( 'Label for the donate by PayPal button', 'gmt_donations' ); ?></label>
		<?php
	}


	//
	// Styles
	//

	function gmt_donations_settings_field_styles_paypal_checkout_style() {
		$options = gmt_donations_get_theme_options();
		?>
		<input type="text" name="gmt_donations_theme_options[paypal_page_style]" class="regular-text" id="paypal_page_style" value="<?php echo esc_attr( $options['paypal_page_style'] ); ?>">
		<label class="description" for="paypal_page_style"><?php _e( 'PayPal custom checkout page style (optional)', 'gmt_donations' ); ?></label>
		<?php
	}

	function gmt_donations_settings_field_styles_hide_other_currency() {
		$options = gmt_donations_get_theme_options();
		?>
		<label>
			<input type="checkbox" name="gmt_donations_theme_options[hide_other_currency]" id="hide_other_currency" value="on" <?php checked( $options['hide_other_currency'], 'on' ); ?>>
			<?php _e( 'Hide the currency icon beside the "other amount" field', 'gmt_donations' ); ?>
		</label>
		<?php
	}

	function gmt_donations_settings_field_styles_disable_css() {
		$options = gmt_donations_get_theme_options();
		?>
		<label>
			<input type="checkbox" name="gmt_donations_theme_options[disable_css]" id="disable_css" value="on" <?php checked( $options['disable_css'], 'on' ); ?>>
			<?php _e( 'Disable donation form styles (useful if you want to style the form yourself)', 'gmt_donations' ); ?>
		</label>
		<?php
	}



	/**
	 * Theme Option Defaults & Sanitization
	 * Each option field requires a default value under gmt_donations_get_theme_options(), and an if statement under gmt_donations_theme_options_validate();
	 */

	// Get the current options from the database.
	// If none are specified, use these defaults.
	function gmt_donations_get_theme_options() {
		$saved = (array) get_option( 'gmt_donations_theme_options' );
		$defaults = array(

			// General options
			'api_mode' => 'test',
			'currency' => 'usd',
			'gateways_stripe' => 'off',
			'gateways_paypal_express_checkout' => 'off',

			// Business Details
			'business_name' => '',
			'business_logo' => '',

			// Stripe API
			'stripe_test_secret' => '',
			'stripe_test_publishable' => '',
			'stripe_live_secret' => '',
			'stripe_live_publishable' => '',

			// PayPal API
			'paypal_test_username' => '',
			'paypal_test_password' => '',
			'paypal_test_signature' => '',
			'paypal_live_username' => '',
			'paypal_live_password' => '',
			'paypal_live_signature' => '',
			'paypal_api_version' => '204',

			// Labels
			'recurring_label' => __( 'Make this a recurring monthly donation', 'gmt_donations' ),
			'in_honor_label' => __( 'Donate in honor of someone', 'gmt_donations' ),
			'in_honor_label_name' => __( 'Honoree\'s full name', 'gmt_donations' ),
			'in_honor_label_email' => __( 'Honoree\'s email address (optional)', 'gmt_donations' ),
			'in_honor_label_donor' => __( 'Your name (optional)', 'gmt_donations' ),
			'in_honor_message' => __( 'We\'ll send them a short note letting them know you made a donation in their honor.', 'gmt_donations' ),
			'stripe_button_label' => __( 'Donate by Credit Card', 'gmt_donations' ),
			'paypal_button_label' => __( 'Donate with PayPal', 'gmt_donations' ),

			// Styles
			'paypal_page_style' => '',
			'hide_other_currency' => 'off',
			'disable_css' => 'off',

		);

		$defaults = apply_filters( 'gmt_donations_default_theme_options', $defaults );

		$options = wp_parse_args( $saved, $defaults );
		$options = array_intersect_key( $options, $defaults );

		return $options;
	}

	// Sanitize and validate updated theme options
	function gmt_donations_theme_options_validate( $input ) {
		$output = array();

		// General options

		if ( isset( $input['api_mode'] ) && array_key_exists( $input['api_mode'], gmt_donations_settings_field_api_mode_choices() ) )
			$output['api_mode'] = $input['api_mode'];

		if ( isset( $input['currency'] ) && array_key_exists( $input['currency'], gmt_donations_settings_field_currency_choices() ) )
			$output['currency'] = $input['currency'];

		if ( isset( $input['gateways_stripe'] ) )
			$output['gateways_stripe'] = 'on';

		if ( isset( $input['gateways_paypal_express_checkout'] ) )
			$output['gateways_paypal_express_checkout'] = 'on';


		// Business Details
		//
		if ( isset( $input['business_name'] ) && ! empty( $input['business_name'] ) )
			$output['business_name'] = wp_filter_nohtml_kses( $input['business_name'] );

		if ( isset( $input['business_logo'] ) && ! empty( $input['business_logo'] ) )
			$output['business_logo'] = wp_filter_nohtml_kses( $input['business_logo'] );


		// Stripe API

		if ( isset( $input['stripe_test_secret'] ) && ! empty( $input['stripe_test_secret'] ) )
			$output['stripe_test_secret'] = wp_filter_nohtml_kses( $input['stripe_test_secret'] );

		if ( isset( $input['stripe_test_publishable'] ) && ! empty( $input['stripe_test_publishable'] ) )
			$output['stripe_test_publishable'] = wp_filter_nohtml_kses( $input['stripe_test_publishable'] );

		if ( isset( $input['stripe_live_secret'] ) && ! empty( $input['stripe_live_secret'] ) )
			$output['stripe_live_secret'] = wp_filter_nohtml_kses( $input['stripe_live_secret'] );

		if ( isset( $input['stripe_live_publishable'] ) && ! empty( $input['stripe_live_publishable'] ) )
			$output['stripe_live_publishable'] = wp_filter_nohtml_kses( $input['stripe_live_publishable'] );


		// PayPal API

		if ( isset( $input['paypal_test_username'] ) && ! empty( $input['paypal_test_username'] ) )
			$output['paypal_test_username'] = wp_filter_nohtml_kses( $input['paypal_test_username'] );

		if ( isset( $input['paypal_test_password'] ) && ! empty( $input['paypal_test_password'] ) )
			$output['paypal_test_password'] = wp_filter_nohtml_kses( $input['paypal_test_password'] );

		if ( isset( $input['paypal_test_signature'] ) && ! empty( $input['paypal_test_signature'] ) )
			$output['paypal_test_signature'] = wp_filter_nohtml_kses( $input['paypal_test_signature'] );

		if ( isset( $input['paypal_live_username'] ) && ! empty( $input['paypal_live_username'] ) )
			$output['paypal_live_username'] = wp_filter_nohtml_kses( $input['paypal_live_username'] );

		if ( isset( $input['paypal_live_password'] ) && ! empty( $input['paypal_live_password'] ) )
			$output['paypal_live_password'] = wp_filter_nohtml_kses( $input['paypal_live_password'] );

		if ( isset( $input['paypal_live_signature'] ) && ! empty( $input['paypal_live_signature'] ) )
			$output['paypal_live_signature'] = wp_filter_nohtml_kses( $input['paypal_live_signature'] );

		if ( isset( $input['paypal_api_version'] ) && ! empty( $input['paypal_api_version'] ) )
			$output['paypal_api_version'] = wp_filter_nohtml_kses( $input['paypal_api_version'] );


		// Labels

		if ( isset( $input['recurring_label'] ) && ! empty( $input['recurring_label'] ) )
			$output['recurring_label'] = wp_filter_post_kses( $input['recurring_label'] );

		if ( isset( $input['in_honor_label'] ) && ! empty( $input['in_honor_label'] ) )
			$output['in_honor_label'] = wp_filter_post_kses( $input['in_honor_label'] );

		if ( isset( $input['in_honor_label_name'] ) && ! empty( $input['in_honor_label_name'] ) )
			$output['in_honor_label_name'] = wp_filter_post_kses( $input['in_honor_label_name'] );

		if ( isset( $input['in_honor_label_email'] ) && ! empty( $input['in_honor_label_email'] ) )
			$output['in_honor_label_email'] = wp_filter_post_kses( $input['in_honor_label_email'] );

		if ( isset( $input['in_honor_label_donor'] ) && ! empty( $input['in_honor_label_donor'] ) )
			$output['in_honor_label_donor'] = wp_filter_post_kses( $input['in_honor_label_donor'] );

		if ( isset( $input['in_honor_message'] ) && ! empty( $input['in_honor_message'] ) )
			$output['in_honor_message'] = wp_filter_post_kses( $input['in_honor_message'] );

		if ( isset( $input['stripe_button_label'] ) && ! empty( $input['stripe_button_label'] ) )
			$output['stripe_button_label'] = wp_filter_post_kses( $input['stripe_button_label'] );

		if ( isset( $input['paypal_button_label'] ) && ! empty( $input['paypal_button_label'] ) )
			$output['paypal_button_label'] = wp_filter_post_kses( $input['paypal_button_label'] );


		// Styles

		if ( isset( $input['paypal_page_style'] ) && ! empty( $input['paypal_page_style'] ) )
			$output['paypal_page_style'] = wp_filter_post_kses( $input['paypal_page_style'] );

		if ( isset( $input['hide_other_currency'] ) )
			$output['hide_other_currency'] = 'on';

		if ( isset( $input['disable_css'] ) )
			$output['disable_css'] = 'on';

		return apply_filters( 'gmt_donations_theme_options_validate', $output, $input );
	}



	/**
	 * Theme Options Menu
	 * Each option field requires its own add_settings_field function.
	 */

	// Create theme options menu
	// The content that's rendered on the menu page.
	function gmt_donations_theme_options_render_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Donations Settings', 'gmt_donations' ); ?></h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'gmt_donations_options' );
					do_settings_sections( 'gmt_donations_options' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	// Register the theme options page and its fields
	function gmt_donations_theme_options_init() {

		// Register a setting and its sanitization callback
		// register_setting( $option_group, $option_name, $sanitize_callback );
		// $option_group - A settings group name.
		// $option_name - The name of an option to sanitize and save.
		// $sanitize_callback - A callback function that sanitizes the option's value.
		register_setting( 'gmt_donations_options', 'gmt_donations_theme_options', 'gmt_donations_theme_options_validate' );


		// Register our settings field group
		// add_settings_section( $id, $title, $callback, $page );
		// $id - Unique identifier for the settings section
		// $title - Section title
		// $callback - // Section callback (we don't want anything)
		// $page - // Menu slug, used to uniquely identify the page. See gmt_donations_theme_options_add_page().
		add_settings_section( 'general', null,  '__return_false', 'gmt_donations_options' );
		add_settings_section( 'business', __( 'Business Details', 'gmt_donations' ),  '__return_false', 'gmt_donations_options' );
		add_settings_section( 'stripe', 'Stripe',  '__return_false', 'gmt_donations_options' );
		add_settings_section( 'paypal', 'PayPal Express Checkout',  '__return_false', 'gmt_donations_options' );
		add_settings_section( 'labels', __( 'Labels', 'gmt_donations' ),  '__return_false', 'gmt_donations_options' );
		add_settings_section( 'styles', __( 'Styles', 'gmt_donations' ),  '__return_false', 'gmt_donations_options' );


		// Register our individual settings fields
		// add_settings_field( $id, $title, $callback, $page, $section );
		// $id - Unique identifier for the field.
		// $title - Setting field title.
		// $callback - Function that creates the field (from the Theme Option Fields section).
		// $page - The menu page on which to display this field.
		// $section - The section of the settings page in which to show the field.
		add_settings_field( 'api_mode', __( 'API Mode', 'gmt_donations' ), 'gmt_donations_settings_field_api_mode', 'gmt_donations_options', 'general' );
		add_settings_field( 'currency', __( 'Currency', 'gmt_donations' ), 'gmt_donations_settings_field_currency', 'gmt_donations_options', 'general' );
		add_settings_field( 'gateways', __( 'Donation Gateways', 'gmt_donations' ), 'gmt_donations_settings_field_gateways', 'gmt_donations_options', 'general' );

		add_settings_field( 'business_name', __( 'Business Name', 'gmt_donations' ), 'gmt_donations_settings_field_business_name', 'gmt_donations_options', 'business' );
		add_settings_field( 'business_Logo', __( 'Business Logo', 'gmt_donations' ), 'gmt_donations_settings_field_business_logo', 'gmt_donations_options', 'business' );

		add_settings_field( 'stripe_test', __( 'Stripe Test', 'gmt_donations' ), 'gmt_donations_settings_field_stripe_api_test', 'gmt_donations_options', 'stripe' );
		add_settings_field( 'stripe_live', __( 'Stripe Live', 'gmt_donations' ), 'gmt_donations_settings_field_stripe_api_live', 'gmt_donations_options', 'stripe' );

		add_settings_field( 'paypal_test', __( 'PayPal Sandbox', 'gmt_donations' ), 'gmt_donations_settings_field_paypal_api_test', 'gmt_donations_options', 'paypal' );
		add_settings_field( 'paypal_live', __( 'PayPal Live', 'gmt_donations' ), 'gmt_donations_settings_field_paypal_api_live', 'gmt_donations_options', 'paypal' );
		// add_settings_field( 'paypal_api_version', __( 'API Version', 'gmt_donations' ), 'gmt_donations_settings_field_paypal_api_version', 'gmt_donations_options', 'paypal' );

		add_settings_field( 'recurring_label', __( 'Recurring Label', 'gmt_donations' ), 'gmt_donations_settings_field_recurring_label', 'gmt_donations_options', 'labels' );
		add_settings_field( 'in_honor_labels', __( 'In Honor Labels', 'gmt_donations' ), 'gmt_donations_settings_field_in_honor_labels', 'gmt_donations_options', 'labels' );
		add_settings_field( 'in_honor_message', __( 'In Honor Message', 'gmt_donations' ), 'gmt_donations_settings_field_in_honor_message', 'gmt_donations_options', 'labels' );
		add_settings_field( 'button_labels', __( 'Button Labels', 'gmt_donations' ), 'gmt_donations_settings_field_button_labels', 'gmt_donations_options', 'labels' );

		add_settings_field( 'paypal_checkout', __( 'PayPal Checkout', 'gmt_donations' ), 'gmt_donations_settings_field_styles_paypal_checkout_style', 'gmt_donations_options', 'styles' );
		add_settings_field( 'hide_other_currency', __( 'Hide Other Currency', 'gmt_donations' ), 'gmt_donations_settings_field_styles_hide_other_currency', 'gmt_donations_options', 'styles' );
		add_settings_field( 'disable_css', __( 'Disable CSS', 'gmt_donations' ), 'gmt_donations_settings_field_styles_disable_css', 'gmt_donations_options', 'styles' );

	}
	add_action( 'admin_init', 'gmt_donations_theme_options_init' );

	// Add the theme options page to the admin menu
	// Use add_theme_page() to add under Appearance tab (default).
	// Use add_menu_page() to add as it's own tab.
	// Use add_submenu_page() to add to another tab.
	function gmt_donations_theme_options_add_page() {

		// add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		// $page_title - Name of page
		// $menu_title - Label in menu
		// $capability - Capability required
		// $menu_slug - Used to uniquely identify the page
		// $function - Function that renders the options page
		// $theme_page = add_theme_page( __( 'Theme Options', 'gmt_donations' ), __( 'Theme Options', 'gmt_donations' ), 'edit_theme_options', 'gmt_donations_options', 'gmt_donations_theme_options_render_page' );

		// $theme_page = add_menu_page( __( 'Theme Options', 'gmt_donations' ), __( 'Theme Options', 'gmt_donations' ), 'edit_theme_options', 'gmt_donations_options', 'gmt_donations_theme_options_render_page' );
		$theme_page = add_submenu_page( 'edit.php?post_type=gmt_donation_forms', __( 'Settings', 'gmt_donations' ), __( 'Settings', 'gmt_donations' ), 'edit_theme_options', 'gmt_donations_options', 'gmt_donations_theme_options_render_page' );
	}
	add_action( 'admin_menu', 'gmt_donations_theme_options_add_page' );



	// Restrict access to the theme options page to admins
	function gmt_donations_option_page_capability( $capability ) {
		return 'edit_theme_options';
	}
	add_filter( 'option_page_capability_gmt_donations_options', 'gmt_donations_option_page_capability' );



	/**
	 * Load the media uploader
	 */
	function gmt_donations_settings_load_admin_scripts( $hook ) {

		global $typenow;
		if ( !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'gmt_donation_forms' || !isset( $_GET['page'] ) || $_GET['page'] !== 'gmt_donations_options' ) return;

		wp_enqueue_media();

		// Registers and enqueues the required javascript.
		wp_register_script( 'meta-box-image', plugins_url( '../includes/js/gmt-donations-settings.js' , __FILE__ ), array( 'jquery' ) );
		wp_localize_script( 'meta-box-image', 'meta_image',
			array(
				'title' => __( 'Choose or Upload Business Logo', 'gmt_stripe' ),
				'button' => __( 'Use this image', 'gmt_stripe' ),
			)
		);
		wp_enqueue_script( 'meta-box-image' );

	}
	add_action( 'admin_enqueue_scripts', 'gmt_donations_settings_load_admin_scripts', 10, 1 );
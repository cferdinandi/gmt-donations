<?php

/**
 * Plugin Name: GMT Donations
 * Plugin URI: https://github.com/cferdinandi/gmt-donations/
 * GitHub Plugin URI: https://github.com/cferdinandi/gmt-donations/
 * Description: Create powerful donation forms that integrate with Stripe and PayPal Express Checkout. Adjust settings under <a href="edit.php?post_type=gmt_donation_forms&page=gmt_donations_options">Donations &rarr; Settings</a>.
 * Version: 1.2.2
 * Author: Chris Ferdinandi
 * Author URI: http://gomakethings.com
 * License: GPLv3
 */

// Includes
require_once( plugin_dir_path( __FILE__ ) . 'includes/wp-session-manager/wp-session-manager.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/paypal-library/paypal.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/stripe-library/init.php' );

// Helper methods
require_once( plugin_dir_path( __FILE__ ) . 'includes/helpers.php' );

// Options
require_once( plugin_dir_path( __FILE__ ) . 'includes/options.php' );

// Custom Post Type
require_once( plugin_dir_path( __FILE__ ) . 'includes/cpt.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/metabox.php' );

// Reporting
require_once( plugin_dir_path( __FILE__ ) . 'includes/reporting.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/dashboard.php' );

// Donation form shortcodes and process
require_once( plugin_dir_path( __FILE__ ) . 'includes/donation-forms.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/emails.php' );


/**
 * Display activation notice if API credentials aren't provided
 */
function gmt_donations_configure_settings_admin_notice() {

	if ( gmt_donations_api_is_activated( 'stripe' ) || gmt_donations_api_is_activated( 'paypal' ) ) return;

	?>

		<div class="notice notice-error"><p><strong>GMT Donations:</strong> <?php printf( __( 'You need to %sprovide API credentials%s before you can begin collecting donations.', 'gmt_donations' ), '<a href="edit.php?post_type=gmt_donation_forms&page=gmt_donations_options">', '</a>' ); ?></p></div>

	<?php
}
add_action( 'admin_notices', 'gmt_donations_configure_settings_admin_notice' );


/**
 * Flush rewrite rules on activation and deactivation
 */
function gmt_donations_flush_rewrites() {
	gmt_donations_add_custom_post_type_forms();
	gmt_donations_add_custom_post_type_donations();
	gmt_donations_add_custom_post_type_donors();
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'gmt_donations_flush_rewrites' );
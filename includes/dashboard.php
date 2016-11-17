<?php

	/**
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'wp_dashboard_setup' action below.
	 */
	function gmt_donations_add_dashboard_widget() {
		wp_add_dashboard_widget(
			'gmt_donations_dashboard_widget', // Widget slug.
			'Donations Summary', // Title.
			'gmt_donations_display_dashboard_widget' // Display function.
		);
	}
	add_action( 'wp_dashboard_setup', 'gmt_donations_add_dashboard_widget' );



	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 */
	function gmt_donations_display_dashboard_widget() {

		// Options
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();

		// Dates
		$month_start = strtotime(date('Y-m-01'));
		$month_end = strtotime(date('Y-m-t 23:59:59'));
		$last_month_start = strtotime(date('Y-m-d', mktime(0, 0, 0, date('m')-1, 1)));
		$last_month_end = strtotime(date('Y-m-d', mktime(0, 0, 0, date('m'), 0)));
		$year_start = strtotime(date('Y-01-01'));
		$year_end = strtotime(date('Y-12-31 23:59:59'));

		// Donations
		$donations = get_posts(array(
			'numberposts' => -1,
			'post_status' => 'publish',
			'post_type' => 'gmt_donations',
		));
		$donations_this_month = gmt_donations_get_donations_by_date( $donations, $month_start, $month_end );
		$donations_last_month = gmt_donations_get_donations_by_date( $donations, $last_month_start, $last_month_end );
		$donations_this_year = gmt_donations_get_donations_by_date( $donations, $year_start, $year_end );

		// Donors
		$donors = get_posts(array(
			'numberposts' => -1,
			'post_status' => 'publish',
			'post_type' => 'gmt_donors',
		));
		$donors_this_month = gmt_donations_get_donations_by_date( $donors, $month_start, $month_end );
		$donors_last_month = gmt_donations_get_donations_by_date( $donors, $last_month_start, $last_month_end );
		$donors_this_year = gmt_donations_get_donations_by_date( $donors, $year_start, $year_end );

		?>
		<div class="main gmt-donations-dashboard-container">
			<div class="gmt-donations-dashboard-row">
				<div class="gmt-donations-dashboard-grid">
					<p><strong><?php _e( 'This Month', 'gmt_donations' ) ?></strong></p>
					<ul>
						<li>
							<a href="edit.php?s&post_status=publish&post_type=gmt_donors&action=-1&m=<?php echo date( 'Ym' ); ?>&filter_action=Filter&paged=1&action2=-1"><?php echo count( $donors_this_month ) . ' ' . __( 'Donors', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?s&post_status=publish&post_type=gmt_donations&action=-1&m=<?php echo date( 'Ym' ); ?>&filter_action=Filter&paged=1&action2=-1"><?php echo count( $donations_this_month ) . ' ' . __( 'Donations', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?s&post_status=publish&post_type=gmt_donations&action=-1&m=<?php echo date( 'Ym' ); ?>&filter_action=Filter&paged=1&action2=-1"><?php echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( gmt_donations_get_total_donated( $donations_this_month ), 2 ) ); ?></a>
						</li>
					</ul>
					<br>

					<p><strong><?php _e( 'Last Month', 'gmt_donations' ) ?></strong></p>
					<ul>
						<li>
							<a href="edit.php?s&post_status=publish&post_type=gmt_donors&action=-1&m=<?php echo date( 'Ym', strtotime( 'last month' ) ); ?>&filter_action=Filter&paged=1&action2=-1"><?php echo count( $donors_last_month ) . ' ' . __( 'Donors', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?s&post_status=publish&post_type=gmt_donations&action=-1&m=<?php echo date( 'Ym', strtotime( 'last month' ) ); ?>&filter_action=Filter&paged=1&action2=-1"><?php echo count( $donations_last_month ) . ' ' . __( 'Donations', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?s&post_status=publish&post_type=gmt_donations&action=-1&m=<?php echo date( 'Ym', strtotime( 'last month' ) ); ?>&filter_action=Filter&paged=1&action2=-1"><?php echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( gmt_donations_get_total_donated( $donations_last_month ), 2 ) ); ?></a>
						</li>
					</ul>
				</div>
				<div class="gmt-donations-dashboard-grid">
					<p><strong><?php _e( 'This Year', 'gmt_donations' ) ?></strong></p>
					<ul>
						<li>
							<a href="edit.php?post_type=gmt_donors"><?php echo count( $donors_this_year ) . ' ' . __( 'Donors', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?post_type=gmt_donations"><?php echo count( $donations_this_year ) . ' ' . __( 'Donations', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?post_type=gmt_donations"><?php echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( gmt_donations_get_total_donated( $donations_this_year ), 2 ) ); ?></a>
						</li>
					</ul>
					<br>

					<p><strong><?php _e( 'Total', 'gmt_donations' ) ?></strong></p>
					<ul>
						<li>
							<a href="edit.php?post_type=gmt_donors"><?php echo count( $donors ) . ' ' . __( 'Donors', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?post_type=gmt_donations"><?php echo count( $donations ) . ' ' . __( 'Donations', 'gmt_donations' ); ?></a>
						</li>
						<li>
							<a href="edit.php?post_type=gmt_donations"><?php echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( gmt_donations_get_total_donated( $donations ), 2 ) ); ?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="gmt-donations-dashboard-sub">

			<p><strong><?php _e( 'Recent Donations', 'gmt_donations' ); ?></strong></p>
			<ul>
				<?php foreach( $donations as $key => $donation ) {
					if ( $key === 5 ) break;
					$form = get_post( get_post_meta( $donation->ID, 'gmt_donations_form', true ) );
					$donor = get_post( get_post_meta( $donation->ID, 'gmt_donations_donor', true ) );
					echo '<li><a href="post.php?post=' . $donation->ID . '&action=edit">' . esc_html( $currencies[$options['currency']]['symbol'] . number_format( get_post_meta( $donation->ID, 'gmt_donations_amount', true ), 2 ) ) . '</a> ' . __( 'from', 'gmt_donations' ) . ' <a href="post.php?post=' . $donor->ID . '&action=edit">' . $donor->post_title . '</a> ' . __( 'on', 'gmt_donations' ) . ' ' . get_the_date( 'M j, Y', $donation->ID ) . ' ' . __( 'to', 'gmt_donations' ) . ' <a href="post.php?post=' . $form->ID . '&action=edit">' . $form->post_title . '</a></li>';
				} ?>
			</ul>
		</div>
		<?php
	}




	function dashboard_widget_display_enqueues( $hook ) {
		if( 'index.php' != $hook )  return;
		wp_enqueue_style( 'gmt-donations-dashboard-widget-styles', plugins_url( 'css/gmt-donations-dashboard.css', __FILE__ ) );
	}
	add_action( 'admin_enqueue_scripts', 'dashboard_widget_display_enqueues' );
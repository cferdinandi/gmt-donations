<?php


	/**
	 * Set form totals when forms are first created
	 */
	function gmt_donation_forms_set_totals_defaults( $post ) {
		if ( get_post_type( $post->ID ) !== 'gmt_donation_forms' ) return;
		update_post_meta( $post->ID, 'gmt_donations_count_donated', 0 );
		update_post_meta( $post->ID, 'gmt_donations_total_donated', 0 );
	}
	add_action( 'new_to_publish', 'gmt_donation_forms_set_totals_defaults' );
	add_action( 'auto-draft_to_publish', 'gmt_donation_forms_set_totals_defaults' );



	/**
	 * Create donation and donor records in the database
	 * @param  integer $id     The form ID
	 * @param  array   $status The donation data
	 */
	function gmt_donations_create_donation_records( $id, $status ) {

		// Check if donor already exists
		$donor = get_page_by_title( $status['email'], 'OBJECT', 'gmt_donors' );
		$donor_exists = empty( $donor ) ? false : true;
		$recurring_amount = $status['recurring'] ? $status['amount'] : 0;

		// If not, create donor record
		if ( empty( $donor ) ) {
			$donor = wp_insert_post(array(
				'post_content'   => '',
				'post_title'     => $status['email'],
				'post_status'    => 'publish',
				'post_type'      => 'gmt_donors',
			));
		} else {
			$donor = $donor->ID;
		}

		// Create donation record
		$donation = wp_insert_post(array(
			'post_content'   => '',
			'post_title'     => current_time( 'timestamp' ) . '_' . wp_generate_password( 24, false ),
			'post_status'    => 'publish',
			'post_type'      => 'gmt_donations',
		));

		// Add donation details
		if ( intval( $donation !== 0 ) ) {
			wp_update_post( array( 'ID' => $donation, 'post_title' => $donation ) );
			update_post_meta( $donation, 'gmt_donations_amount', wp_filter_nohtml_kses( $status['amount'] ) );
			update_post_meta( $donation, 'gmt_donations_form', wp_filter_nohtml_kses( $id ) );
			update_post_meta( $donation, 'gmt_donations_donor', $donor );
			update_post_meta( $donation, 'gmt_donations_source', wp_filter_nohtml_kses( $status['type'] ) );
			update_post_meta( $donation, 'gmt_donations_recurring', wp_filter_nohtml_kses( $recurring_amount ) );
			update_post_meta( $donation, 'gmt_donations_in_honor', wp_filter_nohtml_kses( $status['in_honor_name'] ) );
			update_post_meta( $donation, 'gmt_donations_in_honor_email', wp_filter_nohtml_kses( sanitize_email( $status['in_honor_email'] ) ) );
			update_post_meta( $donation, 'gmt_donations_in_honor_donor', wp_filter_nohtml_kses( $status['in_honor_donor'] ) );
		}

		// Add donor details
		if ( intval( $donor ) !== 0 ) {

			// Get all donations by donor
			$donations_by_donor = get_posts(array(
				'numberposts' => -1,
				'post_status' => 'publish',
				'post_type' => 'gmt_donations',
				'meta_key' => 'gmt_donations_donor',
				'meta_value' => $donor,
			));

			// Add donor metadata
			update_post_meta( $donor, 'gmt_donations_email', wp_filter_nohtml_kses( $status['email'] ) );
			update_post_meta( $donor, 'gmt_donations_count_donated', count( $donations_by_donor ) );
			update_post_meta( $donor, 'gmt_donations_total_donated', wp_filter_nohtml_kses( gmt_donations_get_total_donated( $donations_by_donor ) ) );
			update_post_meta( $donor, 'gmt_donations_recurring', wp_filter_nohtml_kses( $recurring_amount ) );
			update_post_meta( $donor, 'gmt_donations_recurring_id', ( $status['recurring'] ? $donation : 0 ) );

		}

		// Update form details
		$donations_by_form = get_posts(array(
			'numberposts' => -1,
			'post_status' => 'publish',
			'post_type' => 'gmt_donations',
			'meta_key' => 'gmt_donations_form',
			'meta_value' => $id,
		));
		update_post_meta( $id, 'gmt_donations_count_donated', count( $donations_by_form ) );
		update_post_meta( $id, 'gmt_donations_total_donated', gmt_donations_get_total_donated( $donations_by_form ) );

	}
	add_action( 'gmt_donation_success', 'gmt_donations_create_donation_records', 10, 2 );



	/**
	 * Update total donation amounts for Donors and Forms when a donation is deleted or undeleted
	 * @param  integer $donation  The Donation ID
	 */
	function gmt_donations_update_donation_records( $donation ) {

		// Only run for donation post types
		if ( get_post_type( $donation ) !== 'gmt_donations' ) return;

		// Variables
		$donor = get_post_meta( $donation, 'gmt_donations_donor', true );
		$form = get_post_meta( $donation, 'gmt_donations_form', true );
		$recurring = get_post_meta( $donation, 'gmt_donations_recurring_id' );

		// Get all donations by donor
		$donations_by_donor = get_posts(array(
			'numberposts' => -1,
			'post_status' => 'publish',
			'post_type' => 'gmt_donations',
			'meta_key' => 'gmt_donations_donor',
			'meta_value' => $donor,
		));

		// Get all donations for the form
		$donations_by_form = get_posts(array(
			'numberposts' => -1,
			'post_status' => 'publish',
			'post_type' => 'gmt_donations',
			'meta_key' => 'gmt_donations_form',
			'meta_value' => $form,
		));

		// Update donor details
		update_post_meta( $donor, 'gmt_donations_count_donated', count( $donations_by_donor ) );
		update_post_meta( $donor, 'gmt_donations_total_donated', wp_filter_nohtml_kses( gmt_donations_get_total_donated( $donations_by_donor ) ) );
		if ( !empty( $recurring ) && intval( $recurring ) === intval( $donor ) ) {
			update_post_meta( $donor, 'gmt_donations_recurring', 0 );
			update_post_meta( $donor, 'gmt_donations_recurring_id', 0 );
		}

		// Update form details
		update_post_meta( $form, 'gmt_donations_count_donated', count( $donations_by_form ) );
		update_post_meta( $form, 'gmt_donations_total_donated', gmt_donations_get_total_donated( $donations_by_form ) );

	}
	add_action( 'wp_trash_post', 'gmt_donations_update_donation_records' );
	add_action( 'wp_untrash_post', 'gmt_donations_update_donation_records' );



	/**
	 * Hide "View" link for Donations and Donors
	 */
	function gmt_donations_remove_row_actions( $actions, $post ) {
		if ( !in_array( get_post_type(), array( 'gmt_donations', 'gmt_donors' ) ) ) return $actions;
		$actions['edit'] = '<a href="post.php?post=' . $post->ID . '&action=edit">' . __( 'View', 'gmt_donations' ) . '</a>';
		unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}
	add_filter( 'post_row_actions', 'gmt_donations_remove_row_actions', 10, 2 );



	/**
	 * Add custom column to donation forms table
	 * @param  array $columns Existing columns
	 * @return array          Updated array with new columns
	 */
	function gmt_donation_forms_admin_table_columns( $columns ) {
		$new = array();
		foreach( $columns as $key => $title ) {
			if ( $key == 'date' ) {
				$new['count_donated'] = __( 'Donations', 'gmt_donations' );
				$new['total_donated'] = __( 'Total Donated', 'gmt_donations' );
				$new['description'] = __( 'Description', 'gmt_donations' );
			}
			$new[$key] = $title;
		}
		return $new;
	}
	add_filter( 'manage_edit-gmt_donation_forms_columns', 'gmt_donation_forms_admin_table_columns' );



	/**
	 * Add custom column content to donation forms table
	 * @param  string $name The column name
	 * @return string       The column content
	 */
	function gmt_donation_forms_admin_table_columns_content( $column, $post_id ) {

		// Only run on donation forms
		if ( get_post_type( $post_id ) !== 'gmt_donation_forms' ) return;

		// Add custom column content
		if ( $column === 'count_donated' ) {
			$count = get_post_meta( $post_id, 'gmt_donations_count_donated', true );
			echo esc_html( empty( $count ) ? 0 : $count );
		}

		if ( $column === 'total_donated' ) {
			$options = gmt_donations_get_theme_options();
			$currencies = gmt_donations_settings_field_currency_choices();
			$total = get_post_meta( $post_id, 'gmt_donations_total_donated', true );
			echo $currencies[$options['currency']]['symbol'] . esc_html( number_format( ( empty( $total ) ? 0 : $total ), 2 ) );
		}

		if ( $column === 'description' ) {
			echo get_the_excerpt( $post_id );
		}

	}
	add_action( 'manage_posts_custom_column', 'gmt_donation_forms_admin_table_columns_content', 10, 2 );



	/**
	 * Register donations fields as sortable
	 * @param  array $columns Existing columns
	 * @return array          Updated array with new columns
	 */
	function gmt_donations_form_admin_table_sortable_columns( $sortable ) {
		$sortable['count_donated'] = 'count_donated';
		$sortable['total_donated'] = 'total_donated';
		return $sortable;
	}
	add_filter( 'manage_edit-gmt_donation_forms_sortable_columns', 'gmt_donations_form_admin_table_sortable_columns' );



	/**
	 * Sort donation form fields
	 * @param  object $query The query
	 */
	function gmt_donation_forms_admin_table_sortable_columns_sorting( $query ) {

		if ( !is_admin() || empty( $query->get( 'orderby' ) ) || !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'gmt_donation_forms' ) return;

		$orderby = $query->get( 'orderby' );

		if ( $orderby === 'count_donated' ) {
			$query->set( 'meta_key', 'gmt_donations_count_donated' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'total_donated' ) {
			$query->set( 'meta_key', 'gmt_donations_total_donated' );
			$query->set( 'orderby', 'meta_value_num' );
		}

	}
	add_action( 'pre_get_posts', 'gmt_donation_forms_admin_table_sortable_columns_sorting' );



	/**
	 * Add custom column to donations table
	 * @param  array $columns Existing columns
	 * @return array          Updated array with new columns
	 */
	function gmt_donations_admin_table_columns( $columns ) {
		$new = array();
		foreach( $columns as $key => $title ) {
			if ( $key === 'date' ) {
				$new['amount'] = __( 'Amount', 'gmt_donations' );
				$new['recurring'] = __( 'Recurring', 'gmt_donations' );
				$new['in_honor'] = __( 'In Honor', 'gmt_donations' );
				$new['donor'] = __( 'Donor', 'gmt_donations' );
				$new['form'] = __( 'Form', 'gmt_donations' );
				$new['source'] = __( 'Source', 'gmt_donations' );
			}
			$new[$key] = $title;
		}
		return $new;
	}
	add_filter( 'manage_edit-gmt_donations_columns', 'gmt_donations_admin_table_columns' );



	/**
	 * Add custom column content to donations table
	 * @param  string $name The column name
	 * @return string       The column content
	 */
	function gmt_donations_admin_table_columns_content( $column, $post_id ) {

		// Only run on donations
		if ( get_post_type( $post_id ) !== 'gmt_donations' ) return;

		// Add custom column content
		if ( $column === 'amount' ) {
			$options = gmt_donations_get_theme_options();
			$currencies = gmt_donations_settings_field_currency_choices();
			echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( get_post_meta( $post_id, 'gmt_donations_amount', true ), 2 ) );
		}

		if ( $column === 'recurring' ) {
			echo ( get_post_meta( $post_id, 'gmt_donations_recurring', true ) ? _e( 'Yes', 'gmt_donations' ) : _e( 'No', 'gmt_donations' ) );
		}

		if ( $column === 'in_honor' ) {
			echo ( empty( get_post_meta( $post_id, 'gmt_donations_in_honor', true ) ) ? _e( 'No', 'gmt_donations' ) : _e( 'Yes', 'gmt_donations' ) );
		}

		if ( $column === 'donor' ) {
			$donor = get_post( get_post_meta( $post_id, 'gmt_donations_donor', true ) );
			if ( empty( $donor ) ) {
				echo '<em>' . __( 'Deleted' ) . '</em>';
			} else {
				echo '<a href="post.php?post=' . $donor->ID . '&action=edit">' . $donor->post_title . '</a>';
			}
		}

		if ( $column === 'form' ) {
			$form_id = get_post_meta( $post_id, 'gmt_donations_form', true );
			$form = get_post( $form_id );
			if ( intval( $form_id ) === -1 ) {
				$invoice_id = get_post_meta( $post_id, 'gmt_donation_invoice_id', true );
				echo '<a href="post.php?post=' . $invoice_id . '&action=edit">' . __( 'Invoice', 'gmt_donations' ) . ' ' . $invoice_id . '</a>';
			} elseif ( empty( $form ) ) {
				echo '<em>' . __( 'Deleted' ) . '</em>';
			} else {
				echo '<a href="post.php?post=' . $form->ID . '&action=edit">' . $form->post_title . '</a>';
			}
		}


		if ( $column === 'source' ) {
			echo esc_html( ucfirst( get_post_meta( $post_id, 'gmt_donations_source', true ) ) );
		}


	}
	add_action( 'manage_posts_custom_column', 'gmt_donations_admin_table_columns_content', 10, 2 );



	/**
	 * Register donations fields as sortable
	 * @param  array $columns Existing columns
	 * @return array          Updated array with new columns
	 */
	function gmt_donations_admin_table_sortable_columns( $sortable ) {
		$sortable['amount'] = 'amount';
		$sortable['recurring'] = 'recurring';
		$sortable['in_honor'] = 'in_honor';
		$sortable['donor'] = 'donor';
		$sortable['form'] = 'form';
		$sortable['source'] = 'source';
		return $sortable;
	}
	add_filter( 'manage_edit-gmt_donations_sortable_columns', 'gmt_donations_admin_table_sortable_columns' );



	/**
	 * Sort donations fields
	 * @param  object $query The query
	 */
	function gmt_donations_admin_table_sortable_columns_sorting( $query ) {

		if ( !is_admin() || empty( $query->get( 'orderby' ) ) || !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'gmt_donations' ) return;

		$orderby = $query->get( 'orderby' );

		if ( isset( $_GET['form'] ) || isset( $_GET['donor'] ) ) {
			$meta_query = array(
				array(
					'key'     => 'gmt_donations_' . ( isset( $_GET['donor'] ) ? 'donor' : 'form' ),
					'value'   => ( isset( $_GET['donor'] ) ? $_GET['donor'] : $_GET['form'] ),
				),
				array(
					'key'     => 'gmt_donations_' . $orderby,
					'orderby' => ( in_array( $orderby, array( 'source', 'in_honor' ) ) ? 'meta_value' : 'meta_value_num' ),
				),
			);
			$query->set( 'meta_query', $meta_query );
			return $query;
		}

		if ( $orderby === 'amount' ) {
			$query->set( 'meta_key', 'gmt_donations_amount' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'recurring' ) {
			$query->set( 'meta_key', 'gmt_donations_recurring' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'in_honor' ) {
			$query->set( 'meta_key', 'gmt_donations_in_honor' );
			$query->set( 'orderby', 'meta_value' );
		}

		if ( $orderby === 'donor' ) {
			$query->set( 'meta_key', 'gmt_donations_donor' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'form' ) {
			$query->set( 'meta_key', 'gmt_donations_form' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'source' ) {
			$query->set( 'meta_key', 'gmt_donations_source' );
			$query->set( 'orderby', 'meta_value' );
		}

	}
	add_action( 'pre_get_posts', 'gmt_donations_admin_table_sortable_columns_sorting' );



	/**
	 * Filter donations fields by donor
	 * @param  object $query The query
	 */
	function gmt_donations_admin_table_filter_by_donor( $query ) {

		if ( !is_admin() || !empty( $query->get( 'orderby' ) ) || !isset( $_GET['donor'] ) || !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'gmt_donations' ) return;

		$query->set( 'meta_key', 'gmt_donations_donor' );
		$query->set( 'meta_value', $_GET['donor'] );

	}
	add_action( 'pre_get_posts', 'gmt_donations_admin_table_filter_by_donor' );



	/**
	 * Filter donations fields by form
	 * @param  object $query The query
	 */
	function gmt_donations_admin_table_filter_by_form( $query ) {

		if ( !is_admin() || !empty( $query->get( 'orderby' ) ) || !isset( $_GET['form'] ) || !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'gmt_donations' ) return;

		$query->set( 'meta_key', 'gmt_donations_form' );
		$query->set( 'meta_value', $_GET['form'] );

	}
	add_action( 'pre_get_posts', 'gmt_donations_admin_table_filter_by_form' );



	/**
	 * Add custom column to donor table
	 * @param  array $columns Existing columns
	 * @return array          Updated array with new columns
	 */
	function gmt_donors_admin_table_columns( $columns ) {
		$new = array();
		foreach( $columns as $key => $title ) {
			if ( $key === 'date' ) {
				$new['count'] = __( 'Donations', 'gmt_donations' );
				$new['total'] = __( 'Total Donated', 'gmt_donations' );
				$new['recurring'] = __( 'Recurring', 'gmt_donations' );
			}
			if ( $key === 'title' ) {
				$new[$key] = __( 'Email', 'gmt_donations' );
				continue;
			}
			$new[$key] = $title;
		}
		return $new;
	}
	add_filter( 'manage_edit-gmt_donors_columns', 'gmt_donors_admin_table_columns' );



	/**
	 * Add custom column content to donations table
	 * @param  string $name The column name
	 * @return string       The column content
	 */
	function gmt_donors_admin_table_columns_content( $column, $post_id ) {

		// Only run on donations
		if ( get_post_type( $post_id ) !== 'gmt_donors' ) return;

		// Add custom column content
		if ( $column === 'count' ) {
			echo '<a href="edit.php?post_type=gmt_donations&donor=' . $post_id . '">' . esc_html( get_post_meta( $post_id, 'gmt_donations_count_donated', true ) ) . '</a>';
		}

		if ( $column === 'total' ) {
			$options = gmt_donations_get_theme_options();
			$currencies = gmt_donations_settings_field_currency_choices();
			echo '<a href="edit.php?post_type=gmt_donations&donor=' . $post_id . '">' . esc_html( $currencies[$options['currency']]['symbol'] . number_format( get_post_meta( $post_id, 'gmt_donations_total_donated', true ), 2 ) ) . '</a>';
		}

		if ( $column === 'recurring' ) {
			$recurring = get_post_meta( $post_id, 'gmt_donations_recurring', true );
			echo empty( $recurring ) ? __( 'None', 'gmt_donations' ) : '<a href="post.php?post=' . get_post_meta( $post_id, 'gmt_donations_recurring_id', true ) . '&action=edit">' . $currencies[$options['currency']]['symbol'] . esc_html( number_format( $recurring, 2 ) ) . '</a>';
		}


	}
	add_action( 'manage_posts_custom_column', 'gmt_donors_admin_table_columns_content', 10, 2 );



	/**
	 * Register donor fields as sortable
	 * @param  array $columns Existing columns
	 * @return array          Updated array with new columns
	 */
	function gmt_donors_admin_table_sortable_columns( $sortable ) {
		$sortable['count'] = 'count';
		$sortable['total'] = 'total';
		$sortable['recurring'] = 'recurring';
		return $sortable;
	}
	add_filter( 'manage_edit-gmt_donors_sortable_columns', 'gmt_donors_admin_table_sortable_columns' );



	/**
	 * Sort donor fields
	 * @param  object $query The query
	 */
	function gmt_donors_admin_table_sortable_columns_sorting( $query ) {

		if ( !is_admin() || empty( $query->get( 'orderby' ) ) || !isset( $_GET['post_type'] ) || $_GET['post_type'] !== 'gmt_donors' ) return;

		$orderby = $query->get( 'orderby' );

		if ( $orderby === 'count' ) {
			$query->set( 'meta_key', 'gmt_donations_count_donated' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'total' ) {
			$query->set( 'meta_key', 'gmt_donations_total_donated' );
			$query->set( 'orderby', 'meta_value_num' );
		}

		if ( $orderby === 'recurring' ) {
			$query->set( 'meta_key', 'gmt_donations_recurring' );
			$query->set( 'orderby', 'meta_value' );
		}

	}
	add_action( 'pre_get_posts', 'gmt_donors_admin_table_sortable_columns_sorting' );
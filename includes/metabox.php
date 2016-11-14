<?php

	/**
	 * Create the metabox
	 */
	function gmt_donations_create_metaboxes() {
		add_meta_box( 'gmt_donations_metabox_forms', 'Donation Details', 'gmt_donations_render_metabox_forms', 'gmt_donation_forms', 'normal', 'default');
		add_meta_box( 'gmt_donations_metabox_forms_total', 'Donation Stats', 'gmt_donations_render_metabox_forms_total', 'gmt_donation_forms', 'side', 'high');
		add_meta_box( 'gmt_donations_metabox_donations', 'Donation Details', 'gmt_donations_render_metabox_donations', 'gmt_donations', 'normal', 'default');
		add_meta_box( 'gmt_donations_metabox_donors', 'Donor Details', 'gmt_donations_render_metabox_donors', 'gmt_donors', 'normal', 'default');
	}
	add_action( 'add_meta_boxes', 'gmt_donations_create_metaboxes' );



	/**
	 * Create the metabox amount default values
	 */
	function gmt_donations_metabox_amounts_defaults() {
		$options = gmt_donations_get_theme_options();
		return array(
			0 => array(
				'amount' => '',
				'description' =>'',
			),
		);
	}


	/**
	 * Create the metabox detail default values
	 */
	function gmt_donations_metabox_details_defaults() {
		$options = gmt_donations_get_theme_options();
		return array(
			'default_amount' => 0,

			// Goals
			'show_goal' => 'off',
			'goal_target' => '',

			// Labels
			'label_amount' => __( 'Amount', 'gmt_donations' ),
			'label_description' => __( 'What it does', 'gmt_donations' ),
			'show_other_amount' => 'off',
			'show_recurring' => 'off',

			// In Honor
			'show_in_honor' => 'off',
			'send_in_honor_email' => 'off',
			'in_honor_email_subject' => sprintf( __( 'A donation was made in honor of [name] to %s', 'gmt_donations' ), get_bloginfo( 'name' ) ),
			'in_honor_email_message' => sprintf( __( 'A donation of %s was made in honor of %s to %s by %s. This donation will allow us to do even more good. Thank you for being a part of what we do.', 'gmt_donations' ), '[amount]', '[name]', get_bloginfo( 'name' ), '[donor]' ),

			// In Honor Placeholders
			'in_honor_thank_you_email' => sprintf( __( 'We\'ve sent an email to %s letting them know about your donation in honor of %s.', 'gmt_donations' ), '[email]', '[name]' ),
			'in_honor_thank_you_no_email' => sprintf( __( 'Your donation in honor of %s will allow us to do even more good.', 'gmt_donations' ), '[name]' ),
			'in_honor_email_email' => sprintf( __( 'We\'ve sent an email to %s letting them know about your donation in honor of %s.', 'gmt_donations' ), '[email]', '[name]' ),
			'in_honor_email_no_email' => sprintf( __( 'Your donation in honor of %s will allow us to do even more good.', 'gmt_donations' ), '[name]' ),

			// Messages and emails
			'standard_thank_you_title' => __( 'Thank you!', 'gmt_donations' ),
			'standard_thank_you_text' => sprintf( __( 'Thank you for your donation of %s. %s', 'gmt_donations' ), '[amount]', '[in_honor]' ),
			'standard_send_email' => 'off',
			'standard_email_subject' => '',
			'standard_email_text' => '',
			'big_donor_unique_message' => 'off',
			'big_donor_amount' => '',
			'big_donor_thank_you_title' => '',
			'big_donor_thank_you_text' => '',
			'big_donor_send_email' => 'off',
			'big_donor_email_subject' => '',
			'big_donor_email_text' => '',
			'recurring_unique_message' => 'off',
			'recurring_thank_you_title' => '',
			'recurring_thank_you_text' => '',
			'recurring_send_email' => 'off',
			'recurring_email_subject' => '',
			'recurring_email_text' => '',
		);
	}



	/**
	 * Render the donations metabox
	 */
	function gmt_donations_render_metabox_donations() {

		// Variables
		global $post;
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();
		$form = get_post( get_post_meta( $post->ID, 'gmt_donations_form', true ) );
		$donor = get_post( get_post_meta( $post->ID, 'gmt_donations_donor', true ) );
		$recurring = get_post_meta( $post->ID, 'gmt_donations_recurring', true );
		$in_honor = get_post_meta( $post->ID, 'gmt_donations_in_honor', true );
		$in_honor_email = get_post_meta( $post->ID, 'gmt_donations_in_honor_email', true );
		$in_honor_donor = get_post_meta( $post->ID, 'gmt_donations_in_honor_donor', true );

		?>

			<div>
				<p>
					<strong><?php _e( 'Amount' ); ?>:</strong> <?php echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( get_post_meta( $post->ID, 'gmt_donations_amount', true ), 2 ) ); ?>
				</p>

				<p>
					<strong><?php _e( 'Recurring' ); ?>:</strong> <?php ( $recurring ? _e( 'Yes', 'gmt_donations' ) : _e( 'No', 'gmt_donations' ) ); ?>
				</p>

				<p>
					<strong><?php _e( 'In Honor' ); ?>:</strong>
					<?php
						echo ' ';
						if ( empty( $in_honor ) ) {
							_e( 'No', 'gmt_donations' );
						} else {
							echo esc_html( $in_honor );
							if ( !empty( $in_honor_email ) ) {
								printf( __( ' at %s', 'gmt_donations' ), esc_html( $in_honor_email ) );
							}
							if ( !empty( $in_honor_donor ) ) {
								printf( __( ' from %s', 'gmt_donations' ), esc_html( $in_honor_donor ) );
							}
						}
					?>
				</p>

				<p>
					<strong><?php _e( 'Donor' ); ?>:</strong> <a href="post.php?post=<?php echo $donor->ID; ?>&action=edit"><?php echo $donor->post_title;  ?></a>
				</p>

				<p>
					<strong><?php _e( 'Form' ); ?>:</strong> <a href="post.php?post=<?php echo $form->ID; ?>&action=edit"><?php echo $form->post_title;  ?></a>
				</p>

				<p>
					<strong><?php _e( 'Source' ); ?>:</strong> <?php echo esc_html( ucfirst( get_post_meta( $post->ID, 'gmt_donations_source', true ) ) );  ?>
				</p>
			</div>

		<?php
	}



	/**
	 * Render the donor metabox
	 */
	function gmt_donations_render_metabox_donors() {

		// Variables
		global $post;
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();
		$recurring = get_post_meta( $post->ID, 'gmt_donations_recurring', true );

		?>

			<div>
				<p>
					<strong><?php _e( 'Email' ); ?>:</strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( get_post_meta( $post->ID, 'gmt_donations_email', true ) ); ?></a>
				</p>

				<p>
					<strong><?php _e( 'Donations' ); ?>:</strong> <a href="edit.php?post_type=gmt_donations&donor=<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( get_post_meta( $post->ID, 'gmt_donations_count_donated', true ) ); ?></a>
				</p>

				<p>
					<strong><?php _e( 'Total Donated' ); ?>:</strong> <a href="edit.php?post_type=gmt_donations&donor=<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( $currencies[$options['currency']]['symbol'] . number_format( get_post_meta( $post->ID, 'gmt_donations_total_donated', true ), 2 ) );  ?></a>
				</p>

				<p>
					<strong><?php _e( 'Recurring Donation' ); ?>:</strong> <?php echo ( empty( $recurring ) ? __( 'None', 'gmt_donations' ) : '<a href="post.php?post=' . get_post_meta( $post->ID, 'gmt_donations_recurring_id', true ) . '&action=edit">' . $currencies[$options['currency']]['symbol'] . esc_html( number_format( $recurring, 2 ) ) ) . '</a>'; ?>
				</p>
			</div>

		<?php
	}



	/**
	 * Render the forms total metabox
	 */
	function gmt_donations_render_metabox_forms_total() {

		// Variables
		global $post;
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();
		$total = get_post_meta( $post->ID, 'gmt_donations_total_donated', true );

		?>

			<div>
				<p>
					<strong><?php _e( 'Donations' ); ?>:</strong> <a href="edit.php?post_type=gmt_donations&form=<?php echo $post->ID; ?>"><?php echo esc_html( get_post_meta( $post->ID, 'gmt_donations_count_donated', true ) ); ?></a>
				</p>

				<p>
					<strong><?php _e( 'Total Donated' ); ?>:</strong> <a href="edit.php?post_type=gmt_donations&form=<?php echo $post->ID; ?>"><?php echo $currencies[$options['currency']]['symbol'] . esc_html( number_format( ( empty( $total ) ? 0 : $total ), 2 ) );  ?></a>
				</p>
			</div>

		<?php
	}



	/**
	 * Render the forms metabox
	 */
	function gmt_donations_render_metabox_forms() {

		// Variables
		global $post;
		$options = gmt_donations_get_theme_options();
		$currencies = gmt_donations_settings_field_currency_choices();
		$amounts_saved = get_post_meta( $post->ID, 'gmt_donation_amounts', true );
		$amounts_details = empty( $amounts_saved ) || !is_array( $amounts_saved ) ? gmt_donations_metabox_amounts_defaults() : $amounts_saved;
		$saved = get_post_meta( $post->ID, 'gmt_donation_details', true );
		$defaults = gmt_donations_metabox_details_defaults();
		$details = wp_parse_args( $saved, $defaults );
		$total = get_post_meta( $post->ID, 'gmt_donations_total_donated', true );

		?>

			<fieldset>
				<label><?php _e( 'Shortcode' ) ?></label><br>
				<input type="text" class="large-text" value="[donation_form id=&#34;<?php echo $post->ID; ?>&#34;]" readonly>
				<br>
			</fieldset>

			<fieldset>

				<br>
				<h3><?php _e( 'Amounts', 'gmt_donations' ); ?></h3>

				<div id="gmt_donation_amounts" data-donation-amount-count="<?php echo count( $amounts_details ); ?>">

					<?php foreach( $amounts_details as $key => $amount ) : ?>

						<div class="gmt_donation_amount">

							<h4><?php _e( 'Donation Amount', 'gmt_donations' ) ?> <?php echo $key + 1; ?></h4>

							<div>
								<label class="description" for="gmt_donation_amounts_<?php echo $key; ?>_amount"><?php _e( 'Amount', 'gmt_donations' ); ?>:</label>
								<input type="number" min="1" step="any" name="gmt_donation_amounts[<?php echo $key; ?>][amount]" class="small-text" id="gmt_donation_amounts_<?php echo $key; ?>_amount" value="<?php echo esc_attr( $amount['amount'] ); ?>">
							</div>
							<br>

							<div>
								<label class="description" for="gmt_donation_amounts_<?php echo $key; ?>_description"><?php _e( 'Description of the impact', 'gmt_donations' ); ?></label>
								<input type="text" name="gmt_donation_amounts[<?php echo $key; ?>][description]" class="large-text" id="gmt_donation_amounts_<?php echo $key; ?>_description" value="<?php echo stripslashes( esc_attr( $amount['description'] ) ); ?>">
							</div>
							<br>

							<div>
								<label>
									<input type="radio" name="gmt_donation_details[default_amount]" value="<?php echo $key; ?>" <?php checked( $details['default_amount'], $key ); ?>>
									<?php printf( __( 'Use donation amount %s as the default amount', 'gmt_donations' ), $key + 1 ); ?>
								</label>
							</div>
							<br>

						</div>

					<?php endforeach; ?>

				</div>

				<p><button class="button" data-add-donation-amount><?php _e( 'Add Another Amount', 'gmt_donations' ); ?></button></p>

			</fieldset>

			<fieldset>

				<br>
				<h3><?php _e( 'Campaign Goal', 'gmt_donations' ); ?></h3>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[show_goal]" value="on" <?php checked( $details['show_goal'], 'on' ); ?>>
						<?php _e( 'Show campaign goal thermometer' ); ?>
					</label>

					<p>
						<strong><?php _e( 'Donations' ); ?>:</strong> <a href="edit.php?post_type=gmt_donations&form=<?php echo $post->ID; ?>"><?php echo esc_html( get_post_meta( $post->ID, 'gmt_donations_count_donated', true ) ); ?></a>
					</p>

					<p>
						<strong><?php _e( 'Total Donated' ); ?>:</strong> <a href="edit.php?post_type=gmt_donations&form=<?php echo $post->ID; ?>"><?php echo $currencies[$options['currency']]['symbol'] . esc_html( number_format( ( empty( $total ) ? 0 : $total ), 2 ) );  ?></a>
					</p>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_goal_target"><?php _e( 'Target fundraising goal', 'gmt_donations' ); ?></label><br>
					<?php echo $currencies[$options['currency']]['symbol']; ?> <input type="number" min="1" step="any" name="gmt_donation_details[goal_target]" class="regular-text" id="gmt_donation_details_goal_target" value="<?php echo esc_attr( $details['goal_target'] ); ?>">
				</div>
				<br>

			</fieldset>

			<fieldset>

				<br>
				<h3><?php _e( 'Labels', 'gmt_donations' ); ?></h3>

				<div>
					<label class="description" for="gmt_donation_details_label_amount"><?php _e( 'Donation amount label', 'gmt_donations' ); ?></label><br>
					<input type="text" name="gmt_donation_details[label_amount]" class="regular-text" id="gmt_donation_details_label_amount" value="<?php echo stripslashes( esc_attr( $details['label_amount'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_label_description"><?php _e( 'Donation impact label', 'gmt_donations' ); ?></label><br>
					<input type="text" name="gmt_donation_details[label_description]" class="regular-text" id="gmt_donation_details_label_description" value="<?php echo stripslashes( esc_attr( $details['label_description'] ) ); ?>">
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[show_other_amount]" value="on" <?php checked( $details['show_other_amount'], 'on' ); ?>>
						<?php _e( 'Show "other amount" field' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[show_recurring]" value="on" <?php checked( $details['show_recurring'], 'on' ); ?>>
						<?php _e( 'Show "make this recurring" checkbox' ); ?>
					</label>
				</div>

			</fieldset>

			<fieldset>

				<br>
				<h3><?php _e( 'In Honor', 'gmt_donations' ); ?></h3>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[show_in_honor]" value="on" <?php checked( $details['show_in_honor'], 'on' ); ?>>
						<?php _e( 'Show "donate in honor of" field' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[send_in_honor_email]" value="on" <?php checked( $details['send_in_honor_email'], 'on' ); ?>>
						<?php _e( 'Send email to honoree' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_in_honor_email_subject"><?php printf( __( 'Email subject for donation honree. Use %s to dynamically add the donation amount, %s to add the honoree\'s name, and %s to add the donor\'s name.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[name]</code>', '<code>[donor]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[in_honor_email_subject]" class="large-text" id="gmt_donation_details_in_honor_email_subject" value="<?php echo stripslashes( esc_attr( $details['in_honor_email_subject'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_in_honor_email_message"><?php printf( __( 'Email message to send to the donation honoree. Use %s to dynamically add the donation amount, %s to add the honoree\'s name, and %s to add the donor\'s name.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[name]</code>', '<code>[donor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[in_honor_email_message]" class="large-text" id="gmt_donation_details_in_honor_email_message" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['in_honor_email_message'] ) ); ?></textarea>
				</div>
				<br>

			</fieldset>

			<fieldset>

				<br>
				<h3><?php _e( 'Messages &amp; Emails', 'gmt_donations' ); ?></h3>

				<h4><?php _e( 'In Honor Placeholders', 'gmt_donations' ); ?></h4>

				<p><em><strong><?php _e( 'Note', 'gmt_donations' ) ?>:</strong> <?php printf( __( 'The "In Honor" messages and emails are not sent directly to donors. Instead, they can be appended to thank you pages and emails by adding the %s placeholder to any of the other emails or thank you messages below. The right message will automatically be chosen based on the donation details.', 'gmt_donations' ), '<code>[in_honor]</code>' ); ?></em></p>

				<div>
					<label class="description" for="gmt_donation_details_in_honor_thank_you_email"><?php printf( __( 'Thank you message content for donations made "in honor of" that included an email address. Use the %s placeholder to dynamically add the honoree\'s name, %s to add their email address, and %s to add the donor\'s name.', 'gmt_donations' ), '<code>[name]</code>', '<code>[email]</code>', '<code>[donor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[in_honor_thank_you_email]" class="large-text" id="gmt_donation_details_in_honor_thank_you_email" cols="50" rows="5"><?php echo stripslashes( esc_textarea( $details['in_honor_thank_you_email'] ) ); ?></textarea>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_in_honor_thank_you_no_email"><?php printf( __( 'Thank you message content for donations made "in honor of" that did not provide an email. Use the %s placeholder to dynamically add the honoree\'s name.', 'gmt_donations' ), '<code>[name]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[in_honor_thank_you_no_email]" class="large-text" id="gmt_donation_details_in_honor_thank_you_no_email" cols="50" rows="5"><?php echo stripslashes( esc_textarea( $details['in_honor_thank_you_no_email'] ) ); ?></textarea>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_in_honor_email_email"><?php printf( __( 'Thank you email content for donations made "in honor of" that included an email address. Use the %s placeholder to dynamically add the honoree\'s name, %s to add their email address, and %s to add the donor\'s name.', 'gmt_donations' ), '<code>[name]</code>', '<code>[email]</code>', '<code>[donor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[in_honor_email_email]" class="large-text" id="gmt_donation_details_in_honor_email_email" cols="50" rows="5"><?php echo stripslashes( esc_textarea( $details['in_honor_email_email'] ) ); ?></textarea>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_in_honor_email_no_email"><?php printf( __( 'Thank you email content for donations made "in honor of" that did not provide an email. Use the %s placeholder to dynamically add the honoree\'s name.', 'gmt_donations' ), '<code>[name]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[in_honor_email_no_email]" class="large-text" id="gmt_donation_details_in_honor_email_no_email" cols="50" rows="5"><?php echo stripslashes( esc_textarea( $details['in_honor_email_no_email'] ) ); ?></textarea>
				</div>
				<br>


				<h4><?php _e( 'Standard Donors', 'gmt_donations' ); ?></h4>

				<div>
					<label class="description" for="gmt_donation_details_standard_thank_you_title"><?php printf( __( 'Thank you message title. Use %s to dynamically add the donation amount.', 'gmt_donations' ), '<code>[amount]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[standard_thank_you_title]" class="large-text" id="gmt_donation_details_standard_thank_you_title" value="<?php echo stripslashes( esc_attr( $details['standard_thank_you_title'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_standard_thank_you_text"><?php printf( __( 'Thank you message content. Use %s to dynamically add the donation amount, and %s to dynamically add the donor\'s email address. Use %s to add the appropriate "in honor" placeholder message from above.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[email]</code>', '<code>[in_honor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[standard_thank_you_text]" class="large-text" id="gmt_donation_details_standard_thank_you_text" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['standard_thank_you_text'] ) ); ?></textarea>
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[standard_send_email]" value="on" <?php checked( $details['standard_send_email'], 'on' ); ?>>
						<?php _e( 'Send a thank you email to donors', 'gmt_donations' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_standard_email_subject"><?php printf( __( 'Thank you email subject. Use %s to dynamically add the donation amount.', 'gmt_donations' ), '<code>[amount]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[standard_email_subject]" class="large-text" id="gmt_donation_details_standard_email_subject" value="<?php echo stripslashes( esc_attr( $details['standard_email_subject'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_standard_email_text"><?php printf( __( 'Thank you email message. Use %s to dynamically add the donation amount, and %s to dynamically add the donor\'s email address. Use %s to add the appropriate "in honor" placeholder message from above.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[email]</code>', '<code>[in_honor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[standard_email_text]" class="large-text" id="gmt_donation_details_standard_email_text" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['standard_email_text'] ) ); ?></textarea>
				</div>
				<br>

				<h4><?php _e( 'Big Donors', 'gmt_donations' ); ?></h4>

				<p><?php _e( 'Show unique messaging to donors who donate a target amount of money. These settings will take priority over standard messaging for donors who meet the donation amount criteria.' ); ?></p>

				<div>
					<label class="description" for="gmt_donation_details_big_donor_amount"><?php _e( 'Big donor target donation amount', 'gmt_donations' ); ?></label><br>
					<?php echo $currencies[$options['currency']]['symbol']; ?> <input type="number" min="1" step="any" name="gmt_donation_details[big_donor_amount]" class="regular-text" id="gmt_donation_details_big_donor_amount" value="<?php echo esc_attr( $details['big_donor_amount'] ); ?>">
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[big_donor_unique_message]" value="on" <?php checked( $details['big_donor_unique_message'], 'on' ); ?>>
						<?php _e( 'Show big donors a unique message', 'gmt_donations' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_big_donor_thank_you_title"><?php printf( __( 'Thank you message title for big donors. Use %s to dynamically add the donation amount.', 'gmt_donations' ), '<code>[amount]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[big_donor_thank_you_title]" class="large-text" id="gmt_donation_details_big_donor_thank_you_title" value="<?php echo stripslashes( esc_attr( $details['big_donor_thank_you_title'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_big_donor_thank_you_text"><?php printf( __( 'Thank you message content for big donors. Use %s to dynamically add the donation amount, and %s to dynamically add the donor\'s email address. Use %s to add the appropriate "in honor" placeholder message from above.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[email]</code>', '<code>[in_honor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[big_donor_thank_you_text]" class="large-text" id="gmt_donation_details_big_donor_thank_you_text" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['big_donor_thank_you_text'] ) ); ?></textarea>
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[big_donor_send_email]" value="on" <?php checked( $details['big_donor_send_email'], 'on' ); ?>>
						<?php _e( 'Send a thank you email to big donors', 'gmt_donations' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_big_donor_email_subject"><?php printf( __( 'Thank you email subject for big donors. Use %s to dynamically add the donation amount.', 'gmt_donations' ), '<code>[amount]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[big_donor_email_subject]" class="large-text" id="gmt_donation_details_big_donor_email_subject" value="<?php echo stripslashes( esc_attr( $details['big_donor_email_subject'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_big_donor_email_text"><?php printf( __( 'Thank you email message content for big donors. Use %s to dynamically add the donation amount, and %s to dynamically add the donor\'s email address. Use %s to add the appropriate "in honor" placeholder message from above.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[email]</code>', '<code>[in_honor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[big_donor_email_text]" class="large-text" id="gmt_donation_details_big_donor_email_text" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['big_donor_email_text'] ) ); ?></textarea>
				</div>
				<br>

				<h4><?php _e( 'Recurring Donors', 'gmt_donations' ); ?></h4>

				<p><?php _e( 'Show unique messaging to donors who sign up for monthly recurring donations. These settings will take priority over standard and big donor messaging.' ); ?></p>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[recurring_unique_message]" value="on" <?php checked( $details['recurring_unique_message'], 'on' ); ?>>
						<?php _e( 'Show recurring donors a unique message', 'gmt_donations' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_recurring_thank_you_title"><?php printf( __( 'Thank you message title for recurring donors. Use %s to dynamically add the donation amount.', 'gmt_donations' ), '<code>[amount]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[recurring_thank_you_title]" class="large-text" id="gmt_donation_details_recurring_thank_you_title" value="<?php echo stripslashes( esc_attr( $details['recurring_thank_you_title'] ) ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_recurring_thank_you_text"><?php printf( __( 'Thank you message content for recurring donors. Use %s to dynamically add the donation amount, and %s to dynamically add the donor\'s email address. Use %s to add the appropriate "in honor" placeholder message from above.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[email]</code>', '<code>[in_honor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[recurring_thank_you_text]" class="large-text" id="gmt_donation_details_recurring_thank_you_text" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['recurring_thank_you_text'] ) ); ?></textarea>
				</div>
				<br>

				<div>
					<label>
						<input type="checkbox" name="gmt_donation_details[recurring_send_email]" value="on" <?php checked( $details['recurring_send_email'], 'on' ); ?>>
						<?php _e( 'Send a thank you email to recurring donors', 'gmt_donations' ); ?>
					</label>
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_recurring_email_subject"><?php printf( __( 'Thank you email subject for recurring donors. Use %s to dynamically add the donation amount.', 'gmt_donations' ), '<code>[amount]</code>' ); ?></label><br>
					<input type="text" name="gmt_donation_details[recurring_email_subject]" class="large-text" id="gmt_donation_details_recurring_email_subject" value="<?php echo esc_attr( $details['recurring_email_subject'] ); ?>">
				</div>
				<br>

				<div>
					<label class="description" for="gmt_donation_details_recurring_email_text"><?php printf( __( 'Thank you email message content for recurring donors. Use %s to dynamically add the donation amount, and %s to dynamically add the donor\'s email address. Use %s to add the appropriate "in honor" placeholder message from above.', 'gmt_donations' ), '<code>[amount]</code>', '<code>[email]</code>', '<code>[in_honor]</code>' ); ?></label><br>
					<textarea name="gmt_donation_details[recurring_email_text]" class="large-text" id="gmt_donation_details_recurring_email_text" cols="50" rows="10"><?php echo stripslashes( esc_textarea( $details['recurring_email_text'] ) ); ?></textarea>
				</div>
				<br>

			</fieldset>

		<?php

		// Security field
		wp_nonce_field( 'gmt_donations_form_metabox_nonce', 'gmt_donations_form_metabox_process' );

	}



	// Method to sort amounts by amount
	function gmt_donations_sort_by_amount( $a, $b ) {
		return $a['amount'] - $b['amount'];
	}



	/**
	 * Save the metabox
	 * @param  Number $post_id The post ID
	 * @param  Array  $post    The post data
	 */
	function gmt_donations_save_metabox( $post_id, $post ) {

		if ( !isset( $_POST['gmt_donations_form_metabox_process'] ) ) return;

		// Verify data came from edit screen
		if ( !wp_verify_nonce( $_POST['gmt_donations_form_metabox_process'], 'gmt_donations_form_metabox_nonce' ) ) {
			return $post->ID;
		}

		// Verify user has permission to edit post
		if ( !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID;
		}

		// Sanitize and save details
		if ( isset( $_POST['gmt_donation_details'] ) ) {
			$details = array();
			$checkboxes = array( 'show_goal', 'show_other_amount', 'show_recurring', 'show_in_honor', 'send_in_honor_email', 'standard_send_email', 'big_donor_unique_message', 'big_donor_send_email', 'recurring_unique_message', 'recurring_send_email' );
			foreach ( $_POST['gmt_donation_details'] as $key => $detail ) {
				$details[$key] = wp_filter_post_kses( $detail );
			}
			foreach ( $checkboxes as $checkbox ) {
				if ( array_key_exists( $checkbox, $details ) ) continue;
				$details[$checkbox] = 'off';
			}
			update_post_meta( $post->ID, 'gmt_donation_details', $details );
		}

		// Sanitize and save amounts
		if ( isset( $_POST['gmt_donation_amounts'] ) ) {
			$amounts = array();
			foreach ( $_POST['gmt_donation_amounts'] as $key => $amount ) {
				if ( !is_array( $amount ) ) continue;
				foreach( $amount as $id => $value ) {
					if ( empty( $value ) ) continue;
					$amounts[$key][$id] = wp_filter_post_kses( $value );
				}
			}
			usort( $amounts, 'gmt_donations_sort_by_amount' );
			update_post_meta( $post->ID, 'gmt_donation_amounts', $amounts );
		}

	}
	add_action('save_post', 'gmt_donations_save_metabox', 1, 2);



	/**
	 * Save data to revisions
	 * @param  Number $post_id The post ID
	 */
	function gmt_donations_save_revisions( $post_id ) {

		// Check if it's a revision
		$parent_id = wp_is_post_revision( $post_id );

		// If is revision
		if ( $parent_id ) {

			// Get the data
			$parent = get_post( $parent_id );
			$details = get_post_meta( $parent->ID, 'gmt_donation_details', true );

			// If data exists, add to revision
			if ( !empty( $details ) && is_array( $details ) ) {
				$defaults = gmt_donations_metabox_details_defaults();
				foreach ( $defaults as $key => $value ) {
					if ( array_key_exists( $key, $details ) ) {
						add_metadata( 'post', $post_id, 'gmt_donation_details_' . $key, $details[$key] );
					}
				}
			}

		}

	}
	add_action( 'save_post', 'gmt_donations_save_revisions' );



	/**
	 * Restore data with post revisions
	 * @param  Number $post_id     The post ID
	 * @param  Number $revision_id The revision ID
	 */
	function gmt_donations_restore_revisions( $post_id, $revision_id ) {

		// Variables
		$post = get_post( $post_id );
		$revision = get_post( $revision_id );
		$defaults = gmt_donations_metabox_details_defaults();
		$details = array();

		// Update content
		foreach ( $defaults as $key => $value ) {
			$detail_revision = get_metadata( 'post', $revision->ID, 'gmt_donation_details_' . $key, true );
			if ( isset( $detail_revision ) ) {
				$details[$key] = $detail_revision;
			}
		}
		update_post_meta( $post_id, 'gmt_donation_details', $details );

	}
	add_action( 'wp_restore_post_revision', 'gmt_donations_restore_revisions', 10, 2 );



	/**
	 * Get the data to display on the revisions page
	 * @param  Array $fields The fields
	 * @return Array The fields
	 */
	function gmt_donations_get_revisions_fields( $fields ) {
		$defaults = gmt_donations_metabox_details_defaults();
		foreach ( $defaults as $key => $value ) {
			$fields['gmt_donation_details_' . $key] = ucfirst( $key );
		}
		return $fields;
	}
	add_filter( '_wp_post_revision_fields', 'gmt_donations_get_revisions_fields' );



	/**
	 * Display the data on the revisions page
	 * @param  String|Array $value The field value
	 * @param  Array        $field The field
	 */
	function gmt_donations_display_revisions_fields( $value, $field ) {
		global $revision;
		return get_metadata( 'post', $revision->ID, $field, true );
	}
	add_filter( '_wp_post_revision_field_my_meta', 'gmt_donations_display_revisions_fields', 10, 2 );



	/**
	 * Load the "New Amount" script
	 */
	function gmt_donations_load_metabox_scripts( $hook ) {
		global $post_type;
		if ( $post_type != 'gmt_donation_forms' ) return;
		wp_enqueue_script( 'gmt-donations-add-amount', plugins_url( '../includes/js/gmt-donations-add-amount.js' , __FILE__ ), array( 'jquery' ) );
	}
	add_action( 'admin_enqueue_scripts', 'gmt_donations_load_metabox_scripts', 10, 1 );
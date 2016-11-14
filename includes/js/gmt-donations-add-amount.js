/**
 * Add additional amounts to form
 */
 jQuery(document).ready(function($){

	'use strict';

	// Variables
	var $amounts = $('#gmt_donation_amounts');
	var $button = $('[data-add-donation-amount]');

	var addAmount = function () {

		// Variables
		var key = $amounts.data( 'donation-amount-count' );
		var template =
			'<div class="gmt_donation_amount">' +
				'<h4>Donation Amount ' + (key + 1) + '</h4>' +
				'<div>' +
					'<label class="description" for="gmt_donation_amounts_' + key + '_amount">Amount:</label>' +
					'<input type="number" min="1" step="any" name="gmt_donation_amounts[' + key + '][amount]" class="small-text" id="gmt_donation_amounts_' + key + '_amount" value="">' +
				'</div>' +
				'<br>' +
				'<div>' +
					'<label class="description" for="gmt_donation_amounts_' + key + '_description">Description of the impact</label>' +
					'<input type="text" name="gmt_donation_amounts[' + key + '][description]" class="large-text" id="gmt_donation_amounts_' + key + '_description" value="">' +
				'</div>' +
				'<br>' +
				'<div>' +
					'<label>' +
						'<input type="radio" name="gmt_donation_details[default_amount]" value="' + key + '">' +
						'Use donation amount ' + (key + 1) + ' as the default amount' +
					'</label>' +
				'</div>' +
				'<br>' +
			'</div>';

		// Inject template into the DOM
		$('#gmt_donation_amounts .gmt_donation_amount').last().append( template );
		$amounts.data( 'donation-amount-count', key + 1 );

	};

	$button.click(function(event) {
		event.preventDefault();
		addAmount();
	});

});
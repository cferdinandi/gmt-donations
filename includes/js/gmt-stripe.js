/**
 * GMT Stripe Handler
 * @description  Handle Stripe button
 * @version  1.0.0
 * @author   Chris Ferdinandi
 * @license  MIT
 */
;(function (window, document, undefined) {

	'use strict';

	// Feature test
	var supports = 'querySelector' in document && 'addEventListener' in window;
	if ( !supports ) return;


	//
	// Variables
	//

	var key = document.querySelector( '[data-gmt-donations-stripe-key]' );
	if ( !key ) return;
	var handler = StripeCheckout.configure({
		key: key.getAttribute( 'data-gmt-donations-stripe-key' ),
		locale: 'auto',
	});
	var stripeBtns = document.querySelectorAll( '.gmt-donation-form-button-stripe' );

	//
	// Methods
	//

	/**
	 * Get the closest matching element up the DOM tree.
	 * @param  {Element} elem     Starting element
	 * @param  {String}  selector Class to match against
	 * @return {Boolean|Element}  Returns null if not match found
	 */
	var getClosest = function ( elem, selector ) {
		var hasClassList = 'classList' in document.documentElement;
		for ( ; elem && elem !== document && elem.nodeType === 1; elem = elem.parentNode ) {
			if ( hasClassList ) {
				if ( elem.classList.contains( selector.substr(1) ) ) {
					return elem;
				}
			} else {
				if ( new RegExp('(^|\\s)' + selector.substr(1) + '(\\s|$)').test( elem.className ) ) {
					return elem;
				}
			}
		}
		return null;
	};

	/**
	 * Handle click events
	 */
	var clickHandler = function (event) {

		// Check if Stripe button was clicked
		var toggle = getClosest( event.target, '.gmt-donation-form-button-stripe' );
		if ( !toggle ) return;

		// Prevent form from submitting
		event.preventDefault();

		// Get donation amount
		var form = getClosest( toggle, '.gmt-donation-form' );
		var error = form.querySelector( '.gmt-donation-form-alert' );
		var other = form['gmt_donation_form[other]'].value;
		var amount = ( other ? other : form['gmt_donation_form[amount]'].value ) * 100;
		var recurring = form.querySelector( '.gmt-donation-form-recurring-label' );
		var panelLabel = recurring && recurring.checked ? recurring.getAttribute( 'data-panel-label-recurring' ) : recurring.getAttribute( 'data-panel-label' );

		// If no amount selected, display error
		if ( amount === 0 ) {
			error.innerHTML = error.getAttribute( 'data-error-no-amount' );
			error.style.display = 'block';
			error.style.visibility = 'visible';
			error.focus();
			return;
		}

		handler.open({
			name: toggle.getAttribute( 'data-business-name' ),
			image: toggle.getAttribute( 'data-image' ),
			description: toggle.getAttribute( 'data-description' ),
			zipCode: true,
			panelLabel: panelLabel,
			amount: amount,
			token: function(token, args) {
				var input = document.createElement('input');
				input.type = 'hidden';
				input.value = token.id;
				input.name = 'stripe_token';
				form.insertBefore( input, form.childNodes[0] );
				form.submit();
			}
		});
	};

	/**
	 * Handle pop events
	 */
	var popHandler = function () {
		handler.close();
	};


	//
	// Inits and event listeners
	//

	for (var i = 0 ; i < stripeBtns.length; i++) {
		stripeBtns[i].removeAttribute( 'disabled' );
	}

	document.addEventListener( 'click', clickHandler, false );
	document.addEventListener( 'popstate', popHandler, false );

})(window, document);
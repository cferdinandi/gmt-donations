/**
 * GMT Donations
 * @description  Handle donation forms
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

	var inHonor = document.querySelectorAll( '.gmt-donation-form-in-honor' );

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
	 * Clear "other amount" field if fixed amount selected
	 */
	var clearOther = function (event) {

		// Variables
		var amount = getClosest( event.target, '.gmt-donation-form-amount' );
		var form = getClosest( event.target, '.gmt-donation-form' );
		var others = form.querySelector( '.gmt-donation-form-other' );

		// Only run if an amount was clicked and "other amount" field exists
		if ( !amount || !others ) return;

		// Clear the "other amount" field
		others.value = '';

	};

	/**
	 * Toggle "in honor of" donation fields
	 */
	var toggleInHonor = function (event) {

		// Variables
		var inHonor = getClosest( event.target, '.gmt-donation-form-in-honor-toggle' );
		if ( !inHonor ) return;
		var form = getClosest( event.target, '.gmt-donation-form' );
		var content = form.querySelector( inHonor.getAttribute( 'data-in-honor' ) );
		if ( !content ) return;

		// If clicked, show content
		if ( inHonor.checked ) {
			content.style.display = 'block';
			content.style.visibility = 'visible';
			return;
		}

		// Otherwise, hide content
		content.style.display = 'none';
		content.style.visibility = 'hidden';

	};

	/**
	 * Handle click events
	 */
	var clickHandler = function (event) {
		clearOther(event);
		toggleInHonor(event);
	};

	/**
	 * Deselect fixed amount radio button if other field has value
	 */
	var inputHandler = function (event) {

		// Variables
		var other = getClosest( event.target, '.gmt-donation-form-other' );
		var form = getClosest( event.target, '.gmt-donation-form' );
		var amounts = form.querySelectorAll( '.gmt-donation-form-amount' );

		// Only run if "other amount" has value and fixed amounts exist
		if ( !other || other.value.length < 1 || amounts.length < 1 ) return;

		// Deselect fixed amounts
		for (var i = 0; i < amounts.length; i++) {
			amounts[i].checked = false;
		}

	};


	//
	// Inits and event listeners
	//

	// Run event listeners
	if ( document.querySelector( '.gmt-donation-form-in-honor' ) || document.querySelector( '.gmt-donation-form' ) ) {
		document.addEventListener('click', clickHandler, false);
		document.addEventListener('input', inputHandler, false);
	}

	// Show "in honor of" checkbox
	for (var i = 0; i < inHonor.length; i++) {
		inHonor[i].style.display = 'block';
		inHonor[i].style.visibility = 'visible';
	}

})(window, document);
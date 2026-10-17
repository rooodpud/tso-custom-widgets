/**
 * TS Trip Style Picker — choosing a button highlights the parts of the page it names (by CSS ID),
 * fades the other parts the picker names, shows the button's tip and scrolls to the first match.
 * Choosing the same button again clears it. Pickers are set up again when Elementor re-renders
 * the widget in the editor preview.
 */
( function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function ids( btn ) {
		return ( btn.getAttribute( 'data-parts' ) || '' ).split( ' ' ).filter( Boolean );
	}

	function setup( root ) {
		if ( root.getAttribute( 'data-ts-pick-ready' ) ) {
			return;
		}
		root.setAttribute( 'data-ts-pick-ready', '1' );

		var buttons = root.querySelectorAll( '.ts-pick__btn' );
		var tip = root.querySelector( '.ts-pick__tip' );
		var fade = '1' === root.getAttribute( 'data-fade' );
		var scroll = '1' === root.getAttribute( 'data-scroll' );
		var all = [];
		var i;

		// Every part named by any button; parts not on the page are skipped.
		for ( i = 0; i < buttons.length; i++ ) {
			ids( buttons[ i ] ).forEach( function ( id ) {
				var el = document.getElementById( id );
				if ( el && all.indexOf( el ) < 0 ) {
					all.push( el );
				}
			} );
		}

		function clear() {
			var style = window.getComputedStyle( root );
			all.forEach( function ( el ) {
				el.classList.add( 'ts-pick-part' );
				el.classList.remove( 'ts-pick-match', 'ts-pick-faded' );
				el.style.setProperty( '--ts-pick-ring', style.getPropertyValue( '--ts-pick-ring' ).trim() || '#F3692A' );
				el.style.setProperty( '--ts-pick-fade', style.getPropertyValue( '--ts-pick-fade' ).trim() || '.5' );
			} );
			for ( var j = 0; j < buttons.length; j++ ) {
				buttons[ j ].setAttribute( 'aria-pressed', 'false' );
			}
			if ( tip ) {
				tip.textContent = '';
			}
		}

		function choose( btn ) {
			var on = 'true' !== btn.getAttribute( 'aria-pressed' );
			clear();
			if ( ! on ) {
				return;
			}
			btn.setAttribute( 'aria-pressed', 'true' );
			if ( tip ) {
				tip.textContent = btn.getAttribute( 'data-tip' ) || '';
			}
			var wanted = ids( btn ).map( function ( id ) {
				return document.getElementById( id );
			} ).filter( Boolean );
			all.forEach( function ( el ) {
				var hit = wanted.indexOf( el ) > -1;
				el.classList.toggle( 'ts-pick-match', hit );
				el.classList.toggle( 'ts-pick-faded', fade && ! hit );
			} );
			if ( scroll && wanted.length ) {
				wanted[ 0 ].scrollIntoView( { behavior: reduce ? 'auto' : 'smooth', block: 'center' } );
			}
		}

		for ( i = 0; i < buttons.length; i++ ) {
			buttons[ i ].addEventListener( 'click', function () {
				choose( this );
			} );
		}
	}

	function init() {
		var roots = document.querySelectorAll( '[data-ts-pick]' );
		for ( var i = 0; i < roots.length; i++ ) {
			setup( roots[ i ] );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// Elementor editor: run again when a widget is (re)drawn.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/ts-trip-picker.default', init );
		}
	} );
}() );

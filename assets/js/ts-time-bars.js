/**
 * TS Travel Time Bars — the bars grow the first time the chart scrolls into view.
 * Without this script (or without IntersectionObserver) the bars are simply drawn at full length.
 */
( function () {
	'use strict';

	var observer = null;

	function show( root ) {
		root.classList.add( 'is-in' );
	}

	function arm( root ) {
		if ( root.getAttribute( 'data-ts-tb-ready' ) ) {
			return;
		}
		root.setAttribute( 'data-ts-tb-ready', '1' );
		if ( ! root.classList.contains( 'ts-tb--grow' ) ) {
			return;
		}
		if ( ! ( 'IntersectionObserver' in window ) || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			return;
		}
		if ( ! observer ) {
			observer = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						show( entry.target );
						observer.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.25 } );
		}
		root.classList.add( 'is-armed' );
		observer.observe( root );
	}

	function init() {
		var roots = document.querySelectorAll( '[data-ts-tb]' );
		var i;
		for ( i = 0; i < roots.length; i++ ) {
			arm( roots[ i ] );
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
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/ts-time-bars.default', init );
		}
	} );
}() );

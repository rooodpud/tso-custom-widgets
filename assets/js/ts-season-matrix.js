/**
 * TS Season Matrix — outlines one month column.
 *
 * data-ts-ym="none"     no outline
 * data-ts-ym="4"        always April (month number 1–12)
 * data-ts-ym="current"  the visitor's month, or the month chosen in a TS Destination Atlas on the
 *                       same page (the atlas sets window.tsAtlasMonth and fires "ts-atlas-month").
 * The month is worked out in the browser, so a cached page cannot show an old month.
 */
( function () {
	'use strict';

	function outline( root, month ) {
		var cells = root.querySelectorAll( '[data-m]' );
		var i;
		for ( i = 0; i < cells.length; i++ ) {
			cells[ i ].classList.toggle( 'is-now', parseInt( cells[ i ].getAttribute( 'data-m' ), 10 ) === month );
		}
	}

	function currentMonth() {
		return 'number' === typeof window.tsAtlasMonth ? window.tsAtlasMonth : new Date().getMonth();
	}

	function init() {
		var roots = document.querySelectorAll( '[data-ts-ym]' );
		var i, mode;
		for ( i = 0; i < roots.length; i++ ) {
			mode = roots[ i ].getAttribute( 'data-ts-ym' );
			if ( 'current' === mode ) {
				outline( roots[ i ], currentMonth() );
			}
		}
	}

	document.addEventListener( 'ts-atlas-month', function ( e ) {
		var roots = document.querySelectorAll( '[data-ts-ym="current"]' );
		var i;
		for ( i = 0; i < roots.length; i++ ) {
			outline( roots[ i ], e.detail.month );
		}
	} );

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	// Elementor editor: run again when a widget is (re)drawn.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/ts-season-matrix.default', init );
		}
	} );
}() );

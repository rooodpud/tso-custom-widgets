/**
 * TS Destination Atlas — month picker, pins and list.
 *
 * The markup carries everything: each pin / row has data-id, rows have data-levels (twelve
 * 0/1/2 values) and data-notes (twelve texts); the root has data-ts-atlas = JSON with the
 * month names, level names and the first month. Choosing a month recolours pins and rows and
 * swaps the level chip and note. The chosen month is published as window.tsAtlasMonth and the
 * "ts-atlas-month" event, so a TS Season Matrix on the same page can outline it.
 */
( function () {
	'use strict';

	function parse( text, fallback ) {
		try {
			return JSON.parse( text );
		} catch ( e ) {
			return fallback;
		}
	}

	function setClass( el, level ) {
		el.classList.remove( 's0', 's1', 's2' );
		el.classList.add( 's' + level );
	}

	function render( root, cfg, month ) {
		var buttons = root.querySelectorAll( '.ts-at__month' );
		var rows = root.querySelectorAll( '.ts-at__row' );
		var i, row, levels, notes, level, pin, state, note;

		for ( i = 0; i < buttons.length; i++ ) {
			buttons[ i ].setAttribute( 'aria-pressed', i === month ? 'true' : 'false' );
		}
		for ( i = 0; i < rows.length; i++ ) {
			row = rows[ i ];
			levels = ( row.getAttribute( 'data-levels' ) || '' ).split( ',' );
			notes = parse( row.getAttribute( 'data-notes' ), [] );
			level = parseInt( levels[ month ], 10 );
			if ( isNaN( level ) ) {
				level = 2;
			}
			setClass( row, level );
			state = row.querySelector( '.ts-at__state' );
			if ( state ) {
				setClass( state, level );
				state.textContent = cfg.names[ level ] + ( cfg.inWord ? ' ' + cfg.inWord : '' ) + ' ' + cfg.months[ month ];
			}
			note = row.querySelector( '.ts-at__note' );
			if ( note ) {
				note.textContent = notes[ month ] || '';
			}
			pin = root.querySelector( '.ts-at__pin[data-id="' + row.getAttribute( 'data-id' ) + '"]' );
			if ( pin ) {
				setClass( pin, level );
			}
		}

		window.tsAtlasMonth = month;
		document.dispatchEvent( new CustomEvent( 'ts-atlas-month', { detail: { month: month } } ) );
	}

	function hot( root, id, on ) {
		var targets = root.querySelectorAll( '[data-id="' + id + '"]' );
		var i;
		for ( i = 0; i < targets.length; i++ ) {
			targets[ i ].classList.toggle( 'hot', on );
		}
	}

	function setup( root ) {
		var cfg, month, buttons, hovers, i;

		if ( root.getAttribute( 'data-ts-atlas-ready' ) ) {
			return;
		}
		root.setAttribute( 'data-ts-atlas-ready', '1' );

		cfg = parse( root.getAttribute( 'data-ts-atlas' ), null );
		if ( ! cfg || ! cfg.months || ! cfg.names ) {
			return;
		}
		month = 'current' === cfg.start ? new Date().getMonth() : parseInt( cfg.start, 10 );
		if ( isNaN( month ) || month < 0 || month > 11 ) {
			month = new Date().getMonth();
		}

		buttons = root.querySelectorAll( '.ts-at__month' );
		for ( i = 0; i < buttons.length; i++ ) {
			buttons[ i ].addEventListener( 'click', function ( e ) {
				month = parseInt( e.currentTarget.getAttribute( 'data-m' ), 10 );
				render( root, cfg, month );
			} );
		}

		// Pin and row light up together.
		hovers = root.querySelectorAll( '.ts-at__pin, .ts-at__row' );
		for ( i = 0; i < hovers.length; i++ ) {
			( function ( el ) {
				var id = el.getAttribute( 'data-id' );
				el.addEventListener( 'mouseenter', function () { hot( root, id, true ); } );
				el.addEventListener( 'mouseleave', function () { hot( root, id, false ); } );
				el.addEventListener( 'focus', function () { hot( root, id, true ); } );
				el.addEventListener( 'blur', function () { hot( root, id, false ); } );
			}( hovers[ i ] ) );
		}

		render( root, cfg, month );
	}

	function init() {
		var roots = document.querySelectorAll( '[data-ts-atlas]' );
		var i;
		for ( i = 0; i < roots.length; i++ ) {
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
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/ts-atlas.default', init );
		}
	} );
}() );

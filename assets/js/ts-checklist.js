/**
 * TS Info List — "Checklist" look: counts the ticked items ("3 of 11 packed").
 *
 * Listens once on the document, so lists added or re-rendered later (Elementor editor preview)
 * work without set-up. Ticks are not saved: the list starts empty again when the page is reloaded.
 * Without this script the boxes can still be ticked; only the counter stays at its starting text.
 */
( function () {
	'use strict';

	function count( list ) {
		var out = list.querySelector( '.ts-check__count' );
		if ( ! out ) {
			return;
		}
		var boxes = list.querySelectorAll( '.ts-check__box' );
		var done = list.querySelectorAll( '.ts-check__box:checked' ).length;
		var text;
		if ( boxes.length && done === boxes.length && out.getAttribute( 'data-done' ) ) {
			text = out.getAttribute( 'data-done' );
		} else {
			text = [ done, out.getAttribute( 'data-middle' ) || '', boxes.length, out.getAttribute( 'data-word' ) || '' ].join( ' ' ).replace( /\s+/g, ' ' ).trim();
		}
		if ( out.textContent !== text ) {
			out.textContent = text;
		}
	}

	document.addEventListener( 'change', function ( event ) {
		var box = event.target;
		if ( box && box.classList && box.classList.contains( 'ts-check__box' ) ) {
			var list = box.closest( '.ts-info--checklist' );
			if ( list ) {
				count( list );
			}
		}
	} );

	// A browser may keep ticks when the visitor comes back to the page: count them once.
	function all() {
		Array.prototype.forEach.call( document.querySelectorAll( '.ts-info--checklist' ), count );
	}
	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', all );
	} else {
		all();
	}
	window.addEventListener( 'pageshow', all );
}() );

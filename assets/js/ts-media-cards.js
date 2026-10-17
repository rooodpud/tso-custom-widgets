/**
 * TS Media Cards: filter buttons and clickable rating levels.
 * One document-level listener, so it also works for cards added or re-rendered in the Elementor editor.
 * - Group buttons (.ts-mcards__filter): one is always chosen; "all" shows every group.
 * - Level buttons (.ts-mcards__level): clicking one shows only that level; clicking it again shows all levels.
 * A card is shown when it matches both. The count line ("Showing 6 activities") is updated.
 */
( function () {
	'use strict';

	function pressed( root, selector, attr ) {
		var on = root.querySelector( selector + '[aria-pressed="true"]' );
		return on ? on.getAttribute( attr ) : '';
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest ? event.target.closest( '.ts-mcards__filter, .ts-mcards__level' ) : null;
		if ( ! button ) {
			return;
		}
		var root = button.closest( '.ts-mcards' );
		if ( ! root ) {
			return;
		}

		if ( button.classList.contains( 'ts-mcards__filter' ) ) {
			Array.prototype.forEach.call( root.querySelectorAll( '.ts-mcards__filter' ), function ( b ) {
				b.setAttribute( 'aria-pressed', b === button ? 'true' : 'false' );
			} );
		} else {
			var wasOn = 'true' === button.getAttribute( 'aria-pressed' );
			Array.prototype.forEach.call( root.querySelectorAll( '.ts-mcards__level' ), function ( b ) {
				b.setAttribute( 'aria-pressed', ( b === button && ! wasOn ) ? 'true' : 'false' );
			} );
		}

		var group = pressed( root, '.ts-mcards__filter', 'data-filter' ) || 'all';
		var level = pressed( root, '.ts-mcards__level', 'data-level' );
		var shown = 0;

		Array.prototype.forEach.call( root.querySelectorAll( '.ts-mcards__item' ), function ( item ) {
			var match = ( 'all' === group || item.getAttribute( 'data-group' ) === group ) &&
				( '' === level || item.getAttribute( 'data-level' ) === level );
			item.hidden = ! match;
			if ( match ) {
				shown++;
			}
		} );

		var count = root.querySelector( '.ts-mcards__count' );
		if ( count ) {
			var word = 1 === shown ? count.getAttribute( 'data-one' ) : count.getAttribute( 'data-many' );
			count.textContent = [ count.getAttribute( 'data-before' ), String( shown ), word ]
				.map( function ( part ) {
					return String( part || '' ).trim();
				} )
				.filter( function ( part ) {
					return '' !== part;
				} )
				.join( ' ' );
		}
	} );
}() );

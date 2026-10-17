/**
 * TS Tabs — switches panels (ARIA tabs pattern).
 *
 * Listens once on the document, so tabs added or re-rendered later (Elementor editor preview)
 * work without set-up. Click opens a tab; Left/Right arrows, Home and End move between tabs and
 * open them. The server renders the starting tab open, so the page reads correctly without this.
 */
( function () {
	'use strict';

	function tabsOf( tab ) {
		var bar = tab.closest( '.ts-tabs__bar' );
		return bar ? Array.prototype.slice.call( bar.querySelectorAll( '.ts-tabs__tab' ) ) : [];
	}

	function open( tab, focus ) {
		tabsOf( tab ).forEach( function ( t ) {
			var on = t === tab;
			var panel = document.getElementById( t.getAttribute( 'aria-controls' ) );
			t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
			t.setAttribute( 'tabindex', on ? '0' : '-1' );
			if ( panel ) {
				if ( on ) {
					panel.removeAttribute( 'hidden' );
				} else {
					panel.setAttribute( 'hidden', '' );
				}
			}
		} );
		if ( focus ) {
			tab.focus();
		}
	}

	function tabFrom( event ) {
		var el = event.target;
		return el && el.closest ? el.closest( '.ts-tabs__tab' ) : null;
	}

	document.addEventListener( 'click', function ( event ) {
		var tab = tabFrom( event );
		if ( tab ) {
			open( tab, false );
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		var tab = tabFrom( event );
		if ( ! tab ) {
			return;
		}
		var tabs = tabsOf( tab );
		var i = tabs.indexOf( tab );
		var next = null;

		switch ( event.key ) {
			case 'ArrowRight':
				next = tabs[ ( i + 1 ) % tabs.length ];
				break;
			case 'ArrowLeft':
				next = tabs[ ( i - 1 + tabs.length ) % tabs.length ];
				break;
			case 'Home':
				next = tabs[ 0 ];
				break;
			case 'End':
				next = tabs[ tabs.length - 1 ];
				break;
		}

		if ( next ) {
			event.preventDefault();
			open( next, true );
		}
	} );
}() );

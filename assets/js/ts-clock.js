/**
 * TS Cards — live clock in place of a card's title.
 *
 * <span class="ts-clock" data-ts-clock="Asia/Muscat" data-ts-clock-format="h24">fallback</span>
 *
 * The time is worked out in the visitor's browser (never on the server, so a cached page cannot
 * show an old time) for the place's time zone, and refreshed every 15 seconds and whenever the
 * tab comes back into view. Clocks are looked up again on every refresh, so ones added or
 * re-rendered later (Elementor editor preview) start by themselves. If the browser cannot work
 * out the time for that place, the fallback text stays.
 */
( function () {
	'use strict';

	var formatters = {};

	function formatter( zone, h12 ) {
		var key = zone + '|' + ( h12 ? 12 : 24 );
		if ( ! ( key in formatters ) ) {
			try {
				formatters[ key ] = new Intl.DateTimeFormat( h12 ? 'en-US' : 'en-GB', {
					hour: h12 ? 'numeric' : '2-digit',
					minute: '2-digit',
					hour12: h12,
					timeZone: zone
				} );
			} catch ( e ) {
				formatters[ key ] = null;
			}
		}
		return formatters[ key ];
	}

	function tick() {
		var now = new Date();
		var clocks = document.querySelectorAll( '[data-ts-clock]' );
		var i, el, f, text;
		for ( i = 0; i < clocks.length; i++ ) {
			el = clocks[ i ];
			f = formatter( el.getAttribute( 'data-ts-clock' ), 'h12' === el.getAttribute( 'data-ts-clock-format' ) );
			if ( ! f ) {
				continue;
			}
			text = f.format( now ).replace( 'AM', 'am' ).replace( 'PM', 'pm' );
			// 24-hour: some browsers write midnight as 24:05.
			if ( 0 === text.indexOf( '24:' ) ) {
				text = '00:' + text.slice( 3 );
			}
			if ( el.textContent !== text ) {
				el.textContent = text;
			}
		}
	}

	if ( ! window.Intl || ! Intl.DateTimeFormat ) {
		return;
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', tick );
	} else {
		tick();
	}
	window.setInterval( tick, 15000 );
	document.addEventListener( 'visibilitychange', function () {
		if ( ! document.hidden ) {
			tick();
		}
	} );
	window.addEventListener( 'pageshow', tick );
	// Elementor editor preview: cards are drawn (and redrawn) after the page has loaded, so show the time as
	// soon as they are. tick() only writes when the text differs, so its own update does not repeat it.
	if ( window.jQuery && window.MutationObserver ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && elementorFrontend.isEditMode && elementorFrontend.isEditMode() ) {
				var again = 0;
				new MutationObserver( function () {
					window.clearTimeout( again );
					again = window.setTimeout( tick, 200 );
				} ).observe( document.body, { childList: true, subtree: true } );
			}
		} );
	}
}() );

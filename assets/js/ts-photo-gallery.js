/**
 * TS Photo Gallery: category buttons and the enlarged view.
 * One document-level listener, so it also works for galleries added or re-rendered in the Elementor editor.
 * - Category buttons (.ts-pgal__filter): one is always chosen; "all" shows every picture.
 * - Picture buttons (.ts-pgal__open): open the enlarged view, which steps through the pictures
 *   currently shown (previous / next, arrow keys, swipe, Esc).
 */
( function () {
	'use strict';

	var box = null;
	var list = [];
	var index = 0;
	var opener = null;
	var touchX = null;

	function build() {
		box = document.createElement( 'div' );
		box.className = 'ts-pgal-lb';
		box.hidden = true;
		box.setAttribute( 'role', 'dialog' );
		box.setAttribute( 'aria-modal', 'true' );
		box.setAttribute( 'aria-label', 'Photo viewer' );
		box.innerHTML =
			'<span class="ts-pgal-lb__count" aria-hidden="true"></span>' +
			'<button type="button" class="ts-pgal-lb__close" aria-label="Close">&times;</button>' +
			'<button type="button" class="ts-pgal-lb__prev" aria-label="Previous photo">&#8249;</button>' +
			'<figure class="ts-pgal-lb__fig"><img class="ts-pgal-lb__img" alt="" /><figcaption class="ts-pgal-lb__cap"></figcaption></figure>' +
			'<button type="button" class="ts-pgal-lb__next" aria-label="Next photo">&#8250;</button>';
		document.body.appendChild( box );

		box.addEventListener( 'click', function ( event ) {
			var t = event.target;
			if ( t.closest( '.ts-pgal-lb__close' ) || t === box ) {
				close();
			} else if ( t.closest( '.ts-pgal-lb__prev' ) ) {
				step( -1 );
			} else if ( t.closest( '.ts-pgal-lb__next' ) ) {
				step( 1 );
			}
		} );
		box.addEventListener( 'touchstart', function ( event ) {
			touchX = event.changedTouches[ 0 ].clientX;
		}, { passive: true } );
		box.addEventListener( 'touchend', function ( event ) {
			if ( null === touchX ) {
				return;
			}
			var dx = event.changedTouches[ 0 ].clientX - touchX;
			touchX = null;
			if ( Math.abs( dx ) > 50 ) {
				step( dx < 0 ? 1 : -1 );
			}
		}, { passive: true } );
	}

	function show() {
		var button = list[ index ];
		var img = box.querySelector( '.ts-pgal-lb__img' );
		img.src = button.getAttribute( 'data-full' );
		img.alt = button.getAttribute( 'data-alt' ) || '';
		box.querySelector( '.ts-pgal-lb__cap' ).textContent = button.getAttribute( 'data-caption' ) || '';
		box.querySelector( '.ts-pgal-lb__count' ).textContent = ( index + 1 ) + ' / ' + list.length;
		var many = list.length > 1;
		box.querySelector( '.ts-pgal-lb__prev' ).hidden = ! many;
		box.querySelector( '.ts-pgal-lb__next' ).hidden = ! many;
	}

	function step( by ) {
		index = ( index + by + list.length ) % list.length;
		show();
	}

	function open( button ) {
		var root = button.closest( '.ts-pgal' );
		list = Array.prototype.filter.call( root.querySelectorAll( '.ts-pgal__open' ), function ( b ) {
			return ! b.closest( '.ts-pgal__item' ).hidden;
		} );
		index = Math.max( 0, list.indexOf( button ) );
		if ( ! box ) {
			build();
		}
		opener = button;
		show();
		box.hidden = false;
		document.documentElement.style.overflow = 'hidden';
		box.querySelector( '.ts-pgal-lb__close' ).focus();
	}

	function close() {
		if ( ! box || box.hidden ) {
			return;
		}
		box.hidden = true;
		document.documentElement.style.overflow = '';
		box.querySelector( '.ts-pgal-lb__img' ).removeAttribute( 'src' );
		if ( opener && document.contains( opener ) ) {
			opener.focus();
		}
	}

	document.addEventListener( 'keydown', function ( event ) {
		if ( ! box || box.hidden ) {
			return;
		}
		if ( 'Escape' === event.key ) {
			close();
		} else if ( 'ArrowLeft' === event.key ) {
			step( -1 );
		} else if ( 'ArrowRight' === event.key ) {
			step( 1 );
		} else if ( 'Tab' === event.key ) {
			// Keep keyboard focus inside the viewer while it is open.
			var items = Array.prototype.filter.call( box.querySelectorAll( 'button' ), function ( b ) {
				return ! b.hidden;
			} );
			var first = items[ 0 ];
			var last = items[ items.length - 1 ];
			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		var target = event.target.closest ? event.target : event.target.parentElement;
		if ( ! target ) {
			return;
		}

		var picture = target.closest( '.ts-pgal__open' );
		if ( picture ) {
			open( picture );
			return;
		}

		var button = target.closest( '.ts-pgal__filter' );
		if ( ! button ) {
			return;
		}
		var root = button.closest( '.ts-pgal' );
		Array.prototype.forEach.call( root.querySelectorAll( '.ts-pgal__filter' ), function ( b ) {
			b.setAttribute( 'aria-pressed', b === button ? 'true' : 'false' );
		} );
		var group = button.getAttribute( 'data-filter' );
		var shown = 0;
		Array.prototype.forEach.call( root.querySelectorAll( '.ts-pgal__item' ), function ( item ) {
			var match = 'all' === group || item.getAttribute( 'data-group' ) === group;
			item.hidden = ! match;
			if ( match ) {
				shown++;
			}
		} );
		var count = root.querySelector( '.ts-pgal__count' );
		if ( count ) {
			count.textContent = 'Showing ' + shown + ( 1 === shown ? ' photo' : ' photos' );
		}
	} );
}() );

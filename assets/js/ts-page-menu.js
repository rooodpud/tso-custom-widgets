/**
 * TS Page Menu — builds the "On this page" list and dropdown from the headings of its section,
 * highlights the part being read, and scrolls to a heading when its name is chosen.
 *
 * Where a link points: the heading's own id; or, when the heading has none, the id of the block
 * around it (so a section id set in the editor is used); or an id made from the heading's words.
 * Menus are looked up again when Elementor re-renders the widget in the editor preview.
 */
( function () {
	'use strict';

	var menus = [];
	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function slug( text ) {
		var s = String( text ).toLowerCase().trim();
		if ( s.normalize ) {
			s = s.normalize( 'NFKD' ).replace( /[̀-ͯ]/g, '' );
		}
		s = s.replace( /&/g, ' and ' ).replace( /[^a-z0-9]+/g, '-' ).replace( /^-+|-+$/g, '' );
		return s || 'section';
	}

	function uniqueId( base ) {
		var id = base;
		var n = 2;
		while ( document.getElementById( id ) ) {
			id = base + '-' + n++;
		}
		return id;
	}

	function px( nav, name, fallback ) {
		var v = parseFloat( window.getComputedStyle( nav ).getPropertyValue( name ) );
		return isNaN( v ) ? fallback : v;
	}

	function shown( el ) {
		return !! el && 'none' !== window.getComputedStyle( el ).display;
	}

	/** Space kept free at the top of the screen: toolbar, the dropdown bar when it is showing, and the chosen space. */
	function offset( menu ) {
		var nav = menu.nav;
		var top = 0;
		var bar = document.getElementById( 'wpadminbar' );
		if ( bar && 'fixed' === window.getComputedStyle( bar ).position ) {
			top += bar.offsetHeight;
		}
		if ( shown( menu.jump ) ) {
			top += menu.jump.offsetHeight;
		}
		return top + px( nav, '--ts-pm-space', 24 );
	}

	function build( nav ) {
		var scope = document;
		if ( 'page' !== nav.getAttribute( 'data-scope' ) ) {
			// The outermost Elementor container around the menu (the page band).
			var el = nav.parentElement;
			var band = null;
			while ( el && el !== document.body ) {
				if ( el.classList && ( el.classList.contains( 'e-con' ) || el.classList.contains( 'elementor-section' ) ) ) {
					band = el;
				}
				el = el.parentElement;
			}
			scope = band || document;
		}

		var levels = nav.getAttribute( 'data-levels' );
		var selector = 'h2h3' === levels ? 'h2, h3' : ( 'h3' === levels ? 'h3' : 'h2' );
		var found = Array.prototype.filter.call( scope.querySelectorAll( selector ), function ( h ) {
			return ! h.closest( '.ts-pm' ) && '' !== h.textContent.trim() && h.getClientRects().length;
		} );

		var list = nav.querySelector( '.ts-pm__list' );
		var select = nav.querySelector( '.ts-pm__select' );
		var menu = { nav: nav, jump: nav.querySelector( '.ts-pm__jump' ), select: select, items: [] };
		list.textContent = '';
		if ( select ) {
			select.textContent = '';
		}

		found.forEach( function ( h ) {
			var target = h;
			if ( ! h.id ) {
				// A block with an id that holds this heading and no other listed heading.
				var holder = h.parentElement ? h.parentElement.closest( '[id]' ) : null;
				if ( holder && holder !== scope && scope.contains( holder ) && ! holder.contains( nav ) && 1 === holder.querySelectorAll( selector ).length ) {
					target = holder;
				} else {
					h.id = uniqueId( slug( h.textContent ) );
				}
			}
			var text = h.textContent.trim();
			var sub = 'h2h3' === levels && 'H3' === h.tagName;

			var li = document.createElement( 'li' );
			li.className = 'ts-pm__item' + ( sub ? ' ts-pm__item--sub' : '' );
			var a = document.createElement( 'a' );
			a.className = 'ts-pm__link';
			a.href = '#' + target.id;
			a.textContent = text;
			li.appendChild( a );
			list.appendChild( li );

			if ( select ) {
				var opt = document.createElement( 'option' );
				opt.value = target.id;
				opt.textContent = ( sub ? '– ' : '' ) + text;
				select.appendChild( opt );
			}
			menu.items.push( { target: target, link: a, id: target.id } );
		} );

		nav.classList.toggle( 'is-ready', menu.items.length > 0 );
		return menu;
	}

	function go( menu, id, focus ) {
		var item = null;
		menu.items.forEach( function ( it ) {
			if ( it.id === id ) {
				item = it;
			}
		} );
		if ( ! item ) {
			return false;
		}
		var y = item.target.getBoundingClientRect().top + window.pageYOffset - offset( menu );
		window.scrollTo( { top: Math.max( 0, y ), behavior: reduce ? 'instant' : 'smooth' } );
		if ( window.history && history.replaceState ) {
			history.replaceState( null, '', '#' + id );
		}
		if ( focus ) {
			// Keyboard and screen-reader users continue from the section they chose.
			if ( ! item.target.hasAttribute( 'tabindex' ) ) {
				item.target.setAttribute( 'tabindex', '-1' );
			}
			item.target.focus( { preventScroll: true } );
		}
		return true;
	}

	function spy() {
		menus.forEach( function ( menu ) {
			if ( ! menu.items.length ) {
				return;
			}
			var line = offset( menu ) + 40;
			var current = menu.items[ 0 ];
			menu.items.forEach( function ( it ) {
				if ( it.target.getBoundingClientRect().top <= line ) {
					current = it;
				}
			} );
			// At the very bottom of the page the last section counts as the one being read.
			if ( window.innerHeight + window.pageYOffset >= document.documentElement.scrollHeight - 4 ) {
				var last = menu.items[ menu.items.length - 1 ];
				if ( last.target.getBoundingClientRect().top < window.innerHeight ) {
					current = last;
				}
			}
			menu.items.forEach( function ( it ) {
				var on = it === current;
				if ( on !== it.link.classList.contains( 'is-active' ) ) {
					it.link.classList.toggle( 'is-active', on );
					if ( on ) {
						it.link.setAttribute( 'aria-current', 'true' );
					} else {
						it.link.removeAttribute( 'aria-current' );
					}
				}
			} );
			if ( menu.select && menu.select.value !== current.id && document.activeElement !== menu.select ) {
				menu.select.value = current.id;
			}
		} );
	}

	function menuOf( el ) {
		var nav = el.closest( '.ts-pm' );
		var match = null;
		menus.forEach( function ( m ) {
			if ( m.nav === nav ) {
				match = m;
			}
		} );
		return match;
	}

	function setup() {
		menus = Array.prototype.map.call( document.querySelectorAll( '.ts-pm' ), build );
		spy();
	}

	document.addEventListener( 'click', function ( event ) {
		var link = event.target && event.target.closest ? event.target.closest( '.ts-pm__link' ) : null;
		if ( ! link ) {
			return;
		}
		var menu = menuOf( link );
		if ( menu && go( menu, link.getAttribute( 'href' ).slice( 1 ), true ) ) {
			event.preventDefault();
		}
	} );

	document.addEventListener( 'change', function ( event ) {
		var select = event.target;
		if ( select && select.classList && select.classList.contains( 'ts-pm__select' ) ) {
			var menu = menuOf( select );
			if ( menu ) {
				go( menu, select.value, false );
			}
		}
	} );

	// A handful of position checks per scroll step; done at once, so it also runs in a background tab.
	window.addEventListener( 'scroll', spy, { passive: true } );
	window.addEventListener( 'resize', spy );

	function start() {
		setup();
		// A page opened with #section in its address: the ids made above now exist, so go there.
		var hash = window.location.hash ? decodeURIComponent( window.location.hash.slice( 1 ) ) : '';
		if ( hash && menus.length ) {
			menus.some( function ( menu ) {
				var had = reduce;
				reduce = true;
				var done = go( menu, hash, false );
				reduce = had;
				return done;
			} );
		}
	}

	// Elementor editor preview only: the editor draws (and redraws) elements on its own schedule, often the
	// menu before the headings below it. Watch the page and rebuild, batched, after anything changes outside
	// the menus themselves (their own updates must not start another rebuild).
	function watchEditor() {
		if ( ! window.MutationObserver || ! window.elementorFrontend || ! elementorFrontend.isEditMode || ! elementorFrontend.isEditMode() ) {
			return;
		}
		var again = 0;
		new MutationObserver( function ( records ) {
			var outside = records.some( function ( r ) {
				var node = 1 === r.target.nodeType ? r.target : r.target.parentElement;
				return node && ! node.closest( '.ts-pm' );
			} );
			if ( outside ) {
				window.clearTimeout( again );
				again = window.setTimeout( setup, 200 );
			}
		} ).observe( document.body, { childList: true, subtree: true, characterData: true } );
		setup();
	}
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', watchEditor );
	}

	if ( 'complete' === document.readyState ) {
		start();
	} else {
		window.addEventListener( 'load', start );
	}
}() );

/**
 * TS Enquiry Form (1.7.0).
 *
 * Listens once on the document, so forms added or re-rendered later (Elementor editor preview) work
 * without set-up. Shows the extra questions of the chosen enquiry type, checks the required fields,
 * then sends the form in the background to the TS Enquiries plugin (admin-ajax.php) and shows the
 * thank-you message. Without JavaScript the form is posted normally and the page reloads.
 */
( function () {
	'use strict';

	var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	function config( root ) {
		try {
			return JSON.parse( root.getAttribute( 'data-ts-enq' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	/* Show only the extra questions of the chosen type; hidden ones are not sent. */
	function syncType( root ) {
		var chosen = root.querySelector( 'input[name="enquiry_type"]:checked' );
		var extra = chosen ? chosen.getAttribute( 'data-extra' ) : 'none';
		root.querySelectorAll( '.ts-enq__extra' ).forEach( function ( box ) {
			var on = box.getAttribute( 'data-extra' ) === extra;
			box.classList.toggle( 'is-shown', on );
			box.querySelectorAll( 'input, select, textarea' ).forEach( function ( el ) {
				el.disabled = ! on;
			} );
		} );
	}

	function prepare( root ) {
		if ( root.classList.contains( 'ts-enq--js' ) ) {
			return;
		}
		root.classList.add( 'ts-enq--js' );
		syncType( root );
	}

	function prepareAll() {
		document.querySelectorAll( '.ts-enq' ).forEach( prepare );
	}

	function field( form, name ) {
		return form.querySelector( '[data-field="' + name + '"]' );
	}

	function mark( form, name, bad ) {
		var box = field( form, name );
		if ( ! box ) {
			return false;
		}
		box.classList.toggle( 'is-invalid', bad );
		box.querySelectorAll( 'input, select, textarea' ).forEach( function ( el ) {
			if ( bad ) {
				el.setAttribute( 'aria-invalid', 'true' );
			} else {
				el.removeAttribute( 'aria-invalid' );
			}
		} );
		return bad;
	}

	function value( form, name ) {
		var el = form.elements[ name ];
		return el && typeof el.value === 'string' ? el.value.trim() : '';
	}

	/* Returns the first field that needs attention, or null. */
	function check( form ) {
		var first = null;
		function test( name, bad ) {
			if ( field( form, name ) && mark( form, name, bad ) && ! first ) {
				first = field( form, name ).querySelector( 'input, select, textarea' );
			}
		}
		test( 'full_name', '' === value( form, 'full_name' ) );
		test( 'email', ! EMAIL.test( value( form, 'email' ) ) );
		var from = value( form, 'date_from' );
		var to = value( form, 'date_to' );
		test( 'date_to', !! ( from && to && to < from ) );
		test( 'message', '' === value( form, 'message' ) );
		test( 'consent', ! ( form.elements.consent && form.elements.consent.checked ) );
		return first;
	}

	function summary( form, text ) {
		var box = form.querySelector( '.ts-enq__summary' );
		if ( ! box ) {
			return;
		}
		if ( text ) {
			box.textContent = text;
			box.hidden = false;
		} else {
			box.hidden = true;
		}
	}

	function busy( form, on, cfg ) {
		var button = form.querySelector( '.ts-enq__submit' );
		if ( ! button ) {
			return;
		}
		if ( on ) {
			button.setAttribute( 'data-text', button.textContent );
			if ( cfg.sending ) {
				button.textContent = cfg.sending;
			}
		} else if ( button.hasAttribute( 'data-text' ) ) {
			button.textContent = button.getAttribute( 'data-text' );
		}
		button.disabled = on;
		form.setAttribute( 'aria-busy', on ? 'true' : 'false' );
	}

	function showSuccess( root, form, cfg ) {
		var text = root.querySelector( '.ts-enq__success-text' );
		var first = value( form, 'full_name' ).split( /\s+/ )[ 0 ];
		if ( text && cfg.firstName && first ) {
			text.textContent = ( cfg.thanks || 'Thanks, %s.' ).replace( '%s', first ) + ' ' + ( text.getAttribute( 'data-text' ) || '' );
		}
		root.querySelector( '.ts-enq__main' ).hidden = true;
		var success = root.querySelector( '.ts-enq__success' );
		success.hidden = false;
		success.setAttribute( 'tabindex', '-1' );
		success.focus( { preventScroll: true } );
		success.scrollIntoView( { behavior: 'smooth', block: 'center' } );
	}

	function post( cfg, data ) {
		return fetch( cfg.ajax, { method: 'POST', body: data, credentials: 'same-origin' } ).then( function ( response ) {
			return response.json().catch( function () {
				return { success: false, data: { code: 'error' } };
			} );
		} );
	}

	/* A page kept in a cache may carry an old security code: fetch a fresh one and send again once. */
	function send( root, form, cfg, retried ) {
		var data = new FormData( form );
		data.set( 'action', 'ts_enquiry_submit' );
		return post( cfg, data ).then( function ( res ) {
			var code = res && res.data && res.data.code ? res.data.code : 'error';
			if ( res && res.success ) {
				showSuccess( root, form, cfg );
				return;
			}
			if ( 'expired' === code && ! retried ) {
				var ask = new FormData();
				ask.set( 'action', 'ts_enquiry_nonce' );
				return post( cfg, ask ).then( function ( fresh ) {
					if ( fresh && fresh.success && fresh.data && fresh.data.nonce ) {
						form.elements.nonce.value = fresh.data.nonce;
						return send( root, form, cfg, true );
					}
					throw new Error( 'nonce' );
				} );
			}
			if ( 'invalid' === code && res.data.errors ) {
				var first = null;
				Object.keys( res.data.errors ).forEach( function ( name ) {
					if ( mark( form, name, true ) && ! first ) {
						first = field( form, name ).querySelector( 'input, select, textarea' );
					}
				} );
				summary( form, res.data.message || '' );
				if ( first ) {
					first.focus();
				}
				return;
			}
			summary( form, 'wait' === code ? cfg.errWait : cfg.errSend );
		} );
	}

	document.addEventListener( 'change', function ( event ) {
		var root = event.target.closest ? event.target.closest( '.ts-enq' ) : null;
		if ( ! root ) {
			return;
		}
		prepare( root );
		if ( 'enquiry_type' === event.target.name ) {
			syncType( root );
		}
		var box = event.target.closest( '.ts-enq__field' );
		if ( box ) {
			mark( box.closest( 'form' ), box.getAttribute( 'data-field' ), false );
		}
	} );

	document.addEventListener( 'input', function ( event ) {
		var box = event.target.closest ? event.target.closest( '.ts-enq .ts-enq__field.is-invalid' ) : null;
		if ( box ) {
			mark( box.closest( 'form' ), box.getAttribute( 'data-field' ), false );
		}
	} );

	document.addEventListener( 'submit', function ( event ) {
		var form = event.target;
		if ( ! form.classList || ! form.classList.contains( 'ts-enq__form' ) ) {
			return;
		}
		var root = form.closest( '.ts-enq' );
		var cfg = config( root );
		if ( ! window.fetch || ! window.FormData || ! cfg.ajax ) {
			return; // Normal form post.
		}
		event.preventDefault();
		prepare( root );
		if ( form.getAttribute( 'aria-busy' ) === 'true' ) {
			return;
		}
		var first = check( form );
		if ( first ) {
			summary( form, root.querySelector( '.ts-enq__summary' ).getAttribute( 'data-default' ) || null );
			first.focus();
			return;
		}
		summary( form, null );
		busy( form, true, cfg );
		send( root, form, cfg, false ).catch( function () {
			summary( form, cfg.errSend );
		} ).then( function () {
			busy( form, false, cfg );
		} );
	} );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', prepareAll );
	} else {
		prepareAll();
	}
	// Elementor editor: widgets re-rendered in the preview.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/ts-enquiry-form.default', function ( $scope ) {
				var el = $scope && $scope[ 0 ] ? $scope[ 0 ].querySelector( '.ts-enq' ) : null;
				if ( el ) {
					prepare( el );
				}
			} );
		}
	} );
}() );

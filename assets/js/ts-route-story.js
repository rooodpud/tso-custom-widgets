/**
 * TS Route Story — scroll engine.
 *
 * Same choreography as the Home page route (concept-c-first-light-v7), rewritten so any number
 * of Route Story widgets can sit on a page: each widget carries its days, drives and stop
 * positions in data-tsr; the map lines are already in the markup. Widgets added later (the
 * Elementor editor re-renders them on every change) are picked up automatically.
 */
( function () {
	'use strict';

	var SVGNS = 'http://www.w3.org/2000/svg';
	var DWELL_END = 0.15;
	var TRAVEL_END = 0.85;
	var SHRINK_SCREENS = 1; // phone: scroll budget (screens) for the opening shrink
	var GROW_SCREENS = 1; // phone: scroll budget for the closing grow-back
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var instances = [];

	function svgEl( tag, attrs ) {
		var e = document.createElementNS( SVGNS, tag );
		for ( var k in attrs ) {
			e.setAttribute( k, attrs[ k ] );
		}
		return e;
	}

	function adminBarH() {
		var bar = document.getElementById( 'wpadminbar' );
		return bar && 'fixed' === window.getComputedStyle( bar ).position ? bar.offsetHeight : 0;
	}

	function create( root ) {
		var cfg;
		try {
			cfg = JSON.parse( root.getAttribute( 'data-tsr' ) || '' );
		} catch ( e ) {
			return null;
		}
		if ( ! cfg || ! cfg.days || cfg.days.length < 2 ) {
			return null;
		}

		var DAYS = cfg.days;
		var LEGS = cfg.legs;
		var STOPS = cfg.stops;
		var N = DAYS.length;
		var ASPECT = cfg.aspect || 1.3668;
		var SCROLL = cfg.scroll || 1;
		var CUM = LEGS.map( function ( l ) { return l.end; } );
		var mq = window.matchMedia( '(min-width:' + ( cfg.bp || 1100 ) + 'px)' );

		// A loop returns to its first stop: the return shares that stop's marker and label.
		var uniq = STOPS.map( function ( s, i ) {
			for ( var j = 0; j < i; j++ ) {
				if ( STOPS[ j ].x === s.x && STOPS[ j ].y === s.y ) {
					return j;
				}
			}
			return i;
		} );

		function q( sel, ctx ) { return ( ctx || root ).querySelector( sel ); }
		var desk = q( '.tsr-desk' );
		var mob = q( '.tsr-mob' );

		function headerH() { return ( cfg.offset || 0 ) + adminBarH(); }
		function isDesktop() { return mq.matches; }

		/* ---------- map helpers ---------- */
		function addStops( svg ) {
			var g = svgEl( 'g', { 'class': 'tsr-stops' } );
			for ( var i = 0; i < N; i++ ) {
				if ( uniq[ i ] !== i ) {
					continue;
				}
				var s = STOPS[ i ];
				var st = svgEl( 'g', { 'class': 'tsr-stop', 'data-i': String( i ) } );
				if ( 0 === i ) {
					st.appendChild( svgEl( 'circle', { 'class': 'tsr-origin-ring', cx: s.x, cy: s.y, r: 9 } ) );
				}
				st.appendChild( svgEl( 'circle', { 'class': 'tsr-stop-circle', cx: s.x, cy: s.y, r: 3.4 } ) );
				var tx = svgEl( 'text', { 'class': 'tsr-stop-label', x: s.x + s.dx, y: s.y + s.dy, 'text-anchor': s.a, 'fill-opacity': 1 } );
				tx.textContent = DAYS[ i ].place;
				st.appendChild( tx );
				g.appendChild( st );
			}
			svg.appendChild( g );
		}

		function addYou( svg ) {
			var ring = svgEl( 'circle', { 'class': 'tsr-you-ring', cx: 0, cy: 0, r: 10 } );
			var you = svgEl( 'circle', { 'class': 'tsr-you', cx: 0, cy: 0, r: 5 } );
			svg.appendChild( ring );
			svg.appendChild( you );
			return { ring: ring, you: you };
		}

		function prepFill( fill ) {
			var len = fill.getTotalLength();
			fill.setAttribute( 'stroke-dasharray', String( len ) );
			fill.setAttribute( 'stroke-dashoffset', String( len ) );
			return len;
		}

		function setActiveStop( svg, ui ) {
			var stops = svg.querySelectorAll( '.tsr-stop' );
			for ( var i = 0; i < stops.length; i++ ) {
				stops[ i ].classList.toggle( 'is-active', +stops[ i ].getAttribute( 'data-i' ) === ui );
			}
		}

		function place( m, pt ) {
			m.you.setAttribute( 'cx', pt.x );
			m.you.setAttribute( 'cy', pt.y );
			m.ring.setAttribute( 'cx', pt.x );
			m.ring.setAttribute( 'cy', pt.y );
		}

		/* ---------- stage: day card + drive panel ---------- */
		function makeStage( box ) {
			var c = {
				card: q( '.tsr-card', box ), travel: q( '.tsr-travel', box ), daytag: q( '.tsr-daytag', box ),
				place: q( '.tsr-place', box ), desc: q( '.tsr-desc', box ), dist: q( '.tsr-dist', box ),
				from: q( '.tsr-from', box ), to: q( '.tsr-to', box ), bearing: q( '.tsr-bearing', box ),
				km: q( '.tsr-km', box ), time: q( '.tsr-time', box ), pBg: q( '.tsr-leg-bg', box ),
				pFill: q( '.tsr-leg-fill', box ), pDot: q( '.tsr-leg-dot', box ), pRing: q( '.tsr-leg-ring', box ),
				dots: q( '.tsr-dots', box ), bg: q( '.tsr-bg', box ),
				photos: box.querySelectorAll( '.tsr-photo' ),
				shown: -1, leg: -1, legLength: 0
			};
			var dots = c.dots ? c.dots.children : [];

			function setDots( i ) {
				for ( var j = 0; j < dots.length; j++ ) {
					dots[ j ].classList.toggle( 'is-current', j === i );
					dots[ j ].classList.toggle( 'is-done', j < i );
				}
			}
			function setPhotos( i ) {
				for ( var j = 0; j < c.photos.length; j++ ) {
					var p = c.photos[ j ];
					var k = +p.getAttribute( 'data-day' );
					// Load a day's photo only when it is close.
					if ( Math.abs( k - i ) <= 1 && ! p.style.backgroundImage && DAYS[ k ].photo ) {
						p.style.backgroundImage = 'url(' + JSON.stringify( DAYS[ k ].photo ) + ')';
					}
					p.classList.toggle( 'is-on', k === i );
				}
			}
			function renderCard( i ) {
				if ( i === c.shown ) {
					return;
				}
				c.shown = i;
				var s = DAYS[ i ];
				c.daytag.textContent = s.tag;
				c.place.textContent = s.place;
				c.desc.textContent = s.desc;
				c.dist.textContent = s.dist;
				if ( c.bg ) {
					c.bg.style.backgroundColor = s.tint;
				}
				setPhotos( i );
				setDots( i );
			}
			function setLeg( k ) {
				if ( k === c.leg ) {
					return;
				}
				c.leg = k;
				var leg = LEGS[ k ];
				c.from.textContent = DAYS[ k ].place;
				c.to.textContent = DAYS[ k + 1 ].place;
				c.bearing.textContent = leg.bearing;
				c.km.textContent = leg.km;
				c.time.textContent = leg.time;
				c.pBg.setAttribute( 'd', leg.mini );
				c.pFill.setAttribute( 'd', leg.mini );
				c.legLength = c.pFill.getTotalLength();
				c.pFill.style.strokeDasharray = c.legLength;
				c.pFill.style.strokeDashoffset = c.legLength;
			}
			function legDot( t ) {
				var len = t * c.legLength;
				c.pFill.style.strokeDashoffset = c.legLength - len;
				var p = c.pFill.getPointAtLength( len );
				c.pDot.setAttribute( 'cx', p.x );
				c.pDot.setAttribute( 'cy', p.y );
				c.pRing.setAttribute( 'cx', p.x );
				c.pRing.setAttribute( 'cy', p.y );
			}
			function renderPhase( ph ) {
				var idx = ph.idx;
				var t = ph.t;
				if ( idx >= N - 1 || t <= DWELL_END ) {
					renderCard( idx );
					c.card.style.opacity = 1;
					c.travel.style.opacity = 0;
				} else if ( t >= TRAVEL_END ) {
					var arriveT = ( t - TRAVEL_END ) / ( 1 - TRAVEL_END );
					renderCard( Math.min( idx + 1, N - 1 ) );
					c.card.style.opacity = arriveT;
					c.travel.style.opacity = Math.max( 0, 1 - arriveT / 0.3 );
				} else {
					var travelT = ( t - DWELL_END ) / ( TRAVEL_END - DWELL_END );
					setLeg( idx );
					legDot( travelT );
					var fe = 0.10;
					var to = Math.min( travelT / fe, 1, ( 1 - travelT ) / fe );
					c.travel.style.opacity = Math.max( 0, to );
					c.card.style.opacity = Math.max( 0, 1 - travelT / 0.12 );
				}
			}
			return { renderPhase: renderPhase, reset: function () { c.shown = -1; c.leg = -1; } };
		}

		/* ---------- phase maths (shared) ---------- */
		function computePhase( p ) {
			var raw = p * ( N - 1 );
			if ( raw >= N - 1 - 1e-6 ) {
				return { done: true, idx: N - 1, t: 0, c: 1 };
			}
			var idx = Math.min( N - 2, Math.floor( raw ) );
			var t = raw - idx;
			var c;
			if ( t <= DWELL_END ) {
				c = 0;
			} else if ( t >= TRAVEL_END ) {
				c = 1;
			} else {
				c = ( t - DWELL_END ) / ( TRAVEL_END - DWELL_END );
			}
			return { done: false, idx: idx, t: t, c: c };
		}
		function fracOfPhase( ph ) {
			if ( ph.done ) {
				return 1;
			}
			var start = ph.idx > 0 ? CUM[ ph.idx - 1 ] : 0;
			return start + ph.c * ( CUM[ ph.idx ] - start );
		}
		function activeStop( ph ) {
			if ( ph.done ) {
				return uniq[ N - 1 ];
			}
			return uniq[ ph.c >= 1 ? ph.idx + 1 : ph.idx ];
		}

		/* ---------- wide screens ---------- */
		var d = {};
		function deskInit() {
			d.svg = q( '.tsr-map--desk', desk );
			d.fill = q( '.tsr-map-fill', d.svg );
			d.len = prepFill( d.fill );
			addStops( d.svg );
			d.m = addYou( d.svg );
			d.stage = makeStage( desk );
			d.scroller = q( '.tsr-scroller', desk );
			d.col = q( '.tsr-colright', desk );
		}
		function deskSizes() {
			var pinH = window.innerHeight - headerH();
			var col = d.col.clientWidth;
			var maxW = col > 0 ? col : 520;
			var maxH = Math.max( 220, pinH - 120 );
			d.svg.style.width = Math.min( maxW, maxH * ASPECT ) + 'px';
			d.svg.style.height = 'auto';
			d.scroller.style.height = ( N * window.innerHeight * SCROLL ) + 'px';
		}
		function deskUpdate() {
			var rect = d.scroller.getBoundingClientRect();
			var scrollable = rect.height - ( window.innerHeight - headerH() );
			var p = scrollable > 0 ? -rect.top / scrollable : 0;
			p = Math.max( 0, Math.min( 1, p ) );
			var ph = computePhase( p );
			d.stage.renderPhase( ph );
			var frac = fracOfPhase( ph );
			var L = frac * d.len;
			d.fill.setAttribute( 'stroke-dasharray', String( d.len ) );
			d.fill.setAttribute( 'stroke-dashoffset', String( d.len - L ) );
			setActiveStop( d.svg, activeStop( ph ) );
			var on = frac > 0.001 && frac < 0.9995;
			d.m.you.style.opacity = on ? '1' : '0';
			d.m.ring.style.opacity = on ? '1' : '0';
			place( d.m, d.fill.getPointAtLength( L ) );
		}

		/* ---------- phones: one continuous journey ---------- */
		var m = {};
		function mInit() {
			m.scene = q( '.tsr-scene', mob );
			m.stage = makeStage( mob );
			m.svg = q( '.tsr-map--mini', mob );
			m.fill = q( '.tsr-map-fill', m.svg );
			m.len = prepFill( m.fill );
			addStops( m.svg );
			m.m = addYou( m.svg );
			m.wrap = q( '.tsr-miniwrap', mob );
			m.intro = q( '.tsr-mintro', mob );
			m.finale = q( '.tsr-finale', mob );
			m.stagepad = q( '.tsr-stagepad', mob );
			m.rail = q( '.tsr-rail', mob );
			m.cap = q( '.tsr-mini-cap', mob );
			var btn = q( '.tsr-mini-toggle', mob );
			if ( btn ) {
				btn.addEventListener( 'click', function () {
					var next = 'true' !== btn.getAttribute( 'aria-pressed' );
					btn.setAttribute( 'aria-pressed', String( next ) );
					btn.textContent = next ? cfg.miniOn : cfg.miniOff;
					m.wrap.classList.toggle( 'is-hidden', ! next );
				} );
			}
		}
		function totalScreens() { return SHRINK_SCREENS + ( N - 1 ) + GROW_SCREENS; }
		function mSizes() {
			var pinH = window.innerHeight - headerH();
			m.scene.style.height = ( pinH + totalScreens() * window.innerHeight * SCROLL ) + 'px';
			var fMaxW = Math.min( 480, window.innerWidth - 64 );
			var fMaxH = Math.max( 180, pinH - 230 );
			m.bigW = Math.min( fMaxW, fMaxH * ASPECT );
			m.homeW = window.innerWidth < 400 ? 80 : 96;
		}
		// e: 0 = small in the corner, 1 = big and centred (start and end).
		function setMapGeom( e ) {
			var homeW = m.homeW || 96;
			var bigW = m.bigW || 320;
			var w = homeW + ( bigW - homeW ) * e;
			var box = m.wrap.parentElement;
			var cw = box.clientWidth;
			var ch = box.clientHeight;
			var homeLeft = cw - homeW;
			var homeTop = 22;
			var bigLeft = ( cw - bigW ) / 2;
			var bigTop = Math.max( 8, ( ch - bigW / ASPECT ) / 2 - 10 );
			m.wrap.style.width = w + 'px';
			m.wrap.style.left = ( homeLeft + ( bigLeft - homeLeft ) * e ) + 'px';
			m.wrap.style.top = ( homeTop + ( bigTop - homeTop ) * e ) + 'px';
			m.wrap.style.right = 'auto';
			m.svg.classList.toggle( 'is-small', e < 0.55 );
			if ( m.cap ) {
				m.cap.style.opacity = Math.max( 0, 1 - e / 0.4 ).toFixed( 3 );
			}
		}
		function sceneP() {
			var rect = m.scene.getBoundingClientRect();
			var scrollable = rect.height - ( window.innerHeight - headerH() );
			if ( scrollable <= 0 ) {
				return 0;
			}
			return Math.max( 0, Math.min( 1, -rect.top / scrollable ) );
		}
		function mUpdate() {
			var p = sceneP();
			if ( p <= 0.0001 || p >= 0.9999 ) {
				return;
			}
			var shrinkEnd = SHRINK_SCREENS / totalScreens();
			var legsEnd = ( SHRINK_SCREENS + ( N - 1 ) ) / totalScreens();

			if ( p <= shrinkEnd ) {
				// 1: the big overview shrinks into the corner.
				var t = shrinkEnd > 0 ? p / shrinkEnd : 1;
				setMapGeom( 1 - t * t * ( 3 - 2 * t ) );
				m.wrap.style.opacity = '1';
				m.wrap.style.pointerEvents = '';
				m.intro.style.opacity = Math.max( 0, 1 - t / 0.4 ).toFixed( 3 );
				var stageOp = Math.max( 0, Math.min( 1, ( t - 0.55 ) / 0.4 ) );
				m.stagepad.style.opacity = stageOp.toFixed( 3 );
				m.stagepad.style.pointerEvents = stageOp > 0.5 ? '' : 'none';
				m.rail.style.opacity = stageOp.toFixed( 3 );
				m.rail.style.pointerEvents = stageOp > 0.5 ? '' : 'none';
				m.finale.style.opacity = 0;
				m.stage.renderPhase( computePhase( 0 ) );
				m.fill.setAttribute( 'stroke-dasharray', String( m.len ) );
				m.fill.setAttribute( 'stroke-dashoffset', String( m.len ) );
				place( m.m, m.fill.getPointAtLength( 0 ) );
			} else if ( p <= legsEnd ) {
				// 2: day by day, map held small in the corner.
				var ph = computePhase( ( p - shrinkEnd ) / ( legsEnd - shrinkEnd ) );
				m.stage.renderPhase( ph );
				setMapGeom( 0 );
				m.intro.style.opacity = 0;
				m.stagepad.style.opacity = 1;
				m.stagepad.style.pointerEvents = '';
				m.rail.style.opacity = 1;
				m.rail.style.pointerEvents = '';
				m.finale.style.opacity = 0;
				var L = fracOfPhase( ph ) * m.len;
				m.fill.setAttribute( 'stroke-dasharray', String( m.len ) );
				m.fill.setAttribute( 'stroke-dashoffset', String( m.len - L ) );
				place( m.m, m.fill.getPointAtLength( L ) );
			} else {
				// 3: the map grows back into the completed route.
				var t2 = ( 1 - legsEnd ) > 0 ? ( p - legsEnd ) / ( 1 - legsEnd ) : 1;
				setMapGeom( t2 * t2 * ( 3 - 2 * t2 ) );
				m.wrap.style.opacity = '1';
				m.wrap.style.pointerEvents = '';
				m.intro.style.opacity = 0;
				var fadeOut = Math.max( 0, 1 - t2 / 0.35 );
				m.stagepad.style.opacity = fadeOut.toFixed( 3 );
				m.stagepad.style.transform = 'translateY(' + ( -10 * ( 1 - fadeOut ) ) + 'px)';
				m.stagepad.style.pointerEvents = t2 > 0.02 ? 'none' : '';
				m.rail.style.opacity = fadeOut.toFixed( 3 );
				m.rail.style.pointerEvents = t2 > 0.02 ? 'none' : '';
				m.fill.setAttribute( 'stroke-dasharray', String( m.len ) );
				m.fill.setAttribute( 'stroke-dashoffset', '0' );
				var textT = Math.max( 0, Math.min( 1, ( t2 - 0.4 ) / 0.5 ) );
				m.finale.style.opacity = textT.toFixed( 3 );
				m.finale.style.transform = 'translateY(' + ( 10 * ( 1 - textT ) ) + 'px)';
			}
		}

		/* ---------- plumbing ---------- */
		function reflow() {
			root.style.setProperty( '--tsr-top', headerH() + 'px' );
			if ( reduceMotion ) {
				return;
			}
			var wide = isDesktop();
			root.classList.toggle( 'tsr--desk', wide );
			root.classList.toggle( 'tsr--mob', ! wide );
			if ( wide ) {
				deskSizes();
			} else {
				mSizes();
			}
		}
		function update() {
			if ( reduceMotion ) {
				return;
			}
			if ( isDesktop() ) {
				deskUpdate();
			} else {
				mUpdate();
			}
		}

		if ( reduceMotion ) {
			root.classList.add( 'tsr--reduce' );
			return { root: root, update: update, reflow: reflow };
		}

		deskInit();
		mInit();
		reflow();
		deskUpdate();
		mUpdate();

		var onMq = function () {
			reflow();
			update();
		};
		if ( mq.addEventListener ) {
			mq.addEventListener( 'change', onMq );
		} else if ( mq.addListener ) {
			mq.addListener( onMq );
		}

		return { root: root, update: update, reflow: reflow };
	}

	/* ---------- one scroll/resize listener for every widget ---------- */
	function alive() {
		instances = instances.filter( function ( inst ) { return document.documentElement.contains( inst.root ); } );
		return instances;
	}

	var ticking = false;
	function onScroll() {
		if ( ticking ) {
			return;
		}
		ticking = true;
		window.requestAnimationFrame( function () {
			ticking = false;
			alive().forEach( function ( inst ) { inst.update(); } );
		} );
	}
	function onResize() {
		alive().forEach( function ( inst ) {
			inst.reflow();
			inst.update();
		} );
	}

	function scan() {
		var roots = document.querySelectorAll( '.tsr[data-tsr]' );
		for ( var i = 0; i < roots.length; i++ ) {
			if ( ! roots[ i ].tsrReady ) {
				roots[ i ].tsrReady = true;
				var inst = create( roots[ i ] );
				if ( inst ) {
					instances.push( inst );
				}
			}
		}
	}

	function start() {
		scan();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onResize );
		if ( window.visualViewport ) {
			window.visualViewport.addEventListener( 'resize', onResize );
		}
		// The Elementor editor re-renders a widget on every change: pick up the new copy.
		if ( window.MutationObserver ) {
			var queued = false;
			var hasRoute = function ( node ) {
				return 1 === node.nodeType && ( node.classList.contains( 'tsr' ) || !! node.querySelector( '.tsr' ) );
			};
			new MutationObserver( function ( records ) {
				if ( queued ) {
					return;
				}
				var found = records.some( function ( r ) {
					return Array.prototype.some.call( r.addedNodes, hasRoute );
				} );
				if ( ! found ) {
					return;
				}
				queued = true;
				window.requestAnimationFrame( function () {
					queued = false;
					scan();
				} );
			} ).observe( document.body, { childList: true, subtree: true } );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );

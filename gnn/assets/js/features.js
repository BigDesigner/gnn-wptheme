/**
 * GNN front-end features: smooth scroll, scroll-to-top, preloader/loading
 * fade-out and scroll-in animations. Config comes from gnnFeatures.
 */
( function () {
	'use strict';

	var cfg = window.gnnFeatures || {};
	var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* Preloader / loading overlay: hide once the page has loaded. */
	function hideOverlays() {
		[ '.gnn-preloader', '.gnn-loading' ].forEach( function ( sel ) {
			var el = document.querySelector( sel );
			if ( el ) {
				el.classList.add( 'is-done' );
				setTimeout( function () { el.remove(); }, 600 );
			}
		} );
	}
	if ( cfg.preloader || cfg.loading ) {
		window.addEventListener( 'load', hideOverlays );
		// Safety net in case load already fired.
		setTimeout( hideOverlays, 4000 );
	}

	/* Smooth scroll for same-page anchor links. */
	if ( cfg.smoothScroll && ! reduce ) {
		document.addEventListener( 'click', function ( e ) {
			var a = e.target.closest( 'a[href*="#"]' );
			if ( ! a ) { return; }
			var url = a.getAttribute( 'href' );
			if ( ! url || url.charAt( 0 ) !== '#' ) {
				// Allow full URLs that point to the current page + hash.
				if ( a.pathname !== location.pathname || a.hostname !== location.hostname ) { return; }
			}
			var id = url.slice( url.indexOf( '#' ) + 1 );
			if ( ! id ) { return; }
			var target = document.getElementById( id ) || document.querySelector( '[name="' + id + '"]' );
			if ( ! target ) { return; }
			e.preventDefault();
			target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			history.pushState( null, '', '#' + id );
		} );
	}

	/* Scroll-to-top button. */
	if ( cfg.scrollTop ) {
		var btn = document.querySelector( '.gnn-scrolltop' );
		if ( btn ) {
			var onScroll = function () {
				btn.classList.toggle( 'is-visible', window.pageYOffset > 300 );
			};
			window.addEventListener( 'scroll', onScroll, { passive: true } );
			onScroll();
			btn.addEventListener( 'click', function () {
				window.scrollTo( { top: 0, behavior: reduce ? 'auto' : 'smooth' } );
			} );
		}
	}

	/* Scroll-in animations for elements with the gnn-anim class. */
	if ( cfg.scrollAnim && ! reduce && 'IntersectionObserver' in window ) {
		var items = document.querySelectorAll( '.gnn-anim' );
		if ( items.length ) {
			var io = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-in' );
						io.unobserve( entry.target );
					}
				} );
			}, { threshold: 0.15 } );
			items.forEach( function ( el ) { io.observe( el ); } );
		}
	}
} )();

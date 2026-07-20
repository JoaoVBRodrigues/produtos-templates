/**
 * LeadSnap Main JS
 * Micro-interactions, scroll animations, and UI enhancements.
 *
 * @package LeadSnap
 * @version 1.0.0
 */

( function () {
	'use strict';

	// ── Scroll-triggered section animations ───────────────────────
	function initScrollAnimations() {
		if ( ! ( 'IntersectionObserver' in window ) ) return;
		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

		const observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'ls-in-view' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
		);

		document.querySelectorAll( '.ls-animate-up' ).forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	// ── Smooth scroll for anchor links ───────────────────────────
	function initSmoothScroll() {
		document.querySelectorAll( 'a[href^="#"]' ).forEach( function ( anchor ) {
			anchor.addEventListener( 'click', function ( e ) {
				const target = document.querySelector( this.getAttribute( 'href' ) );
				if ( ! target ) return;
				e.preventDefault();
				target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
			} );
		} );
	}

	// ── CTA pulse on hero scroll exit ────────────────────────────
	// Adds a floating CTA button when user scrolls past the hero.
	function initStickyCtaHint() {
		const hero = document.querySelector( '.leadsnap-hero' );
		const cta  = document.querySelector( '.leadsnap-sticky-cta' );
		if ( ! hero || ! cta ) return;

		const observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					cta.classList.toggle( 'ls-visible', ! entry.isIntersecting );
				} );
			},
			{ threshold: 0 }
		);
		observer.observe( hero );
	}

	// ── Accordion / FAQ ───────────────────────────────────────────
	function initFaqAccordion() {
		document.querySelectorAll( '.leadsnap-faq-question' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				const item   = btn.closest( '.leadsnap-faq-item' );
				const answer = item && item.querySelector( '.leadsnap-faq-answer' );
				if ( ! answer ) return;

				const isOpen = item.classList.contains( 'ls-open' );

				// Close all.
				document.querySelectorAll( '.leadsnap-faq-item.ls-open' ).forEach( function ( openItem ) {
					openItem.classList.remove( 'ls-open' );
					const ans = openItem.querySelector( '.leadsnap-faq-answer' );
					if ( ans ) ans.style.maxHeight = null;
				} );

				// Open clicked (toggle).
				if ( ! isOpen ) {
					item.classList.add( 'ls-open' );
					answer.style.maxHeight = answer.scrollHeight + 'px';
				}
			} );
		} );
	}

	// ── Logo bar marquee (social proof) ──────────────────────────
	function initLogoMarquee() {
		const track = document.querySelector( '.ls-logo-track' );
		if ( ! track ) return;
		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

		// Duplicate logos for seamless loop.
		const clone = track.cloneNode( true );
		clone.setAttribute( 'aria-hidden', 'true' );
		track.parentNode.appendChild( clone );
	}

	// ── Init ──────────────────────────────────────────────────────
	function init() {
		initScrollAnimations();
		initSmoothScroll();
		initStickyCtaHint();
		initFaqAccordion();
		initLogoMarquee();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

} )();

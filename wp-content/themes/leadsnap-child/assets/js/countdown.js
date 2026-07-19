/**
 * LeadSnap Countdown Timer
 *
 * Features:
 *  - Fixed date mode: data-target="2026-12-31T23:59:59"
 *  - Evergreen mode:  data-evergreen="1" data-days="3"
 *    → stores target timestamp in a cookie so the countdown is
 *      personal to each visitor and resets after N days.
 *  - Respects prefers-reduced-motion (no tick animation)
 *  - Animated number flip on each second tick
 *  - Accessible: updates aria-live region
 *
 * @package LeadSnap
 * @version 1.0.0
 */

( function () {
	'use strict';

	// ── Config ────────────────────────────────────────────────────
	const COOKIE_PREFIX = 'leadsnap_cdown_';
	const COOKIE_DAYS   = 3; // Default evergreen days (overridden by data-days)

	// ── Utilities ─────────────────────────────────────────────────

	/**
	 * Pad a number to 2 digits.
	 * @param {number} n
	 * @returns {string}
	 */
	function pad( n ) {
		return String( Math.max( 0, n ) ).padStart( 2, '0' );
	}

	/**
	 * Get a cookie value by name.
	 * @param {string} name
	 * @returns {string|null}
	 */
	function getCookie( name ) {
		const match = document.cookie.match(
			new RegExp( '(?:^|; )' + name.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) + '=([^;]*)' )
		);
		return match ? decodeURIComponent( match[1] ) : null;
	}

	/**
	 * Set a cookie with an expiry in days.
	 * @param {string} name
	 * @param {string} value
	 * @param {number} days
	 */
	function setCookie( name, value, days ) {
		const expires = new Date( Date.now() + days * 864e5 ).toUTCString();
		document.cookie = name + '=' + encodeURIComponent( value ) +
			'; expires=' + expires +
			'; path=/; SameSite=Lax';
	}

	/**
	 * Resolve the target timestamp for a given countdown element.
	 * Handles both fixed-date and evergreen modes.
	 *
	 * @param {HTMLElement} el
	 * @returns {number} Unix timestamp in milliseconds.
	 */
	function resolveTarget( el ) {
		const isEvergreen = el.dataset.evergreen === '1';

		if ( isEvergreen ) {
			const days      = parseInt( el.dataset.days, 10 ) || COOKIE_DAYS;
			const cookieKey = COOKIE_PREFIX + btoa( el.id || 'default' ).replace( /=/g, '' );
			const stored    = getCookie( cookieKey );

			if ( stored ) {
				const ts = parseInt( stored, 10 );
				if ( ts > Date.now() ) {
					return ts;
				}
				// Expired cookie — reset.
			}

			// Generate a new target N days from now.
			const newTarget = Date.now() + days * 864e5;
			setCookie( cookieKey, String( newTarget ), days );
			return newTarget;
		}

		// Fixed date mode.
		const rawTarget = el.dataset.target;
		if ( rawTarget ) {
			const ts = new Date( rawTarget ).getTime();
			if ( ! isNaN( ts ) ) {
				return ts;
			}
		}

		// Fallback: 48 hours from now.
		console.warn( '[LeadSnap] Countdown: no valid target found on element', el );
		return Date.now() + 48 * 3600 * 1000;
	}

	/**
	 * Update a single countdown element.
	 *
	 * @param {HTMLElement} el     The .leadsnap-countdown wrapper.
	 * @param {number}      target Unix timestamp in ms.
	 * @param {boolean}     reducedMotion
	 * @returns {boolean} False when countdown has expired.
	 */
	function updateCountdown( el, target, reducedMotion ) {
		const diff = target - Date.now();

		if ( diff <= 0 ) {
			// Mark as expired and freeze at 00:00:00:00.
			el.classList.add( 'ls-expired' );
			el.setAttribute( 'aria-label', 'Contagem encerrada' );
			updateUnits( el, 0, 0, 0, 0, reducedMotion );
			return false;
		}

		const totalSeconds = Math.floor( diff / 1000 );
		const days    = Math.floor( totalSeconds / 86400 );
		const hours   = Math.floor( ( totalSeconds % 86400 ) / 3600 );
		const minutes = Math.floor( ( totalSeconds % 3600 ) / 60 );
		const seconds = totalSeconds % 60;

		updateUnits( el, days, hours, minutes, seconds, reducedMotion );
		return true;
	}

	/**
	 * Write the unit values into the DOM, optionally animating.
	 *
	 * @param {HTMLElement} el
	 * @param {number} days
	 * @param {number} hours
	 * @param {number} minutes
	 * @param {number} seconds
	 * @param {boolean} reducedMotion
	 */
	function updateUnits( el, days, hours, minutes, seconds, reducedMotion ) {
		const values = { days, hours, minutes, seconds };

		el.querySelectorAll( '[data-unit]' ).forEach( function ( numEl ) {
			const unit     = numEl.dataset.unit;
			const newValue = pad( values[ unit ] ?? 0 );

			if ( numEl.textContent === newValue ) return; // No change — skip repaint.

			numEl.textContent = newValue;

			if ( ! reducedMotion && unit === 'seconds' ) {
				// Tick animation only on seconds (minimal motion).
				numEl.classList.remove( 'ls-tick' );
				void numEl.offsetWidth; // Force reflow to restart animation.
				numEl.classList.add( 'ls-tick' );

				numEl.addEventListener( 'animationend', function handler() {
					numEl.classList.remove( 'ls-tick' );
					numEl.removeEventListener( 'animationend', handler );
				}, { once: true } );
			}
		} );

		// Update accessible label every minute (not every second — too noisy).
		if ( seconds === 0 || seconds === 59 ) {
			el.setAttribute(
				'aria-label',
				`Contagem regressiva: ${days} dias, ${hours} horas, ${minutes} minutos, ${seconds} segundos`
			);
		}
	}

	// ── Initialization ────────────────────────────────────────────

	/**
	 * Initialize all countdown timers on the page.
	 */
	function initCountdowns() {
		const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		const countdowns    = document.querySelectorAll( '.leadsnap-countdown' );

		if ( ! countdowns.length ) return;

		countdowns.forEach( function ( el, index ) {
			// Give each element a stable ID for cookie keying.
			if ( ! el.id ) {
				el.id = 'leadsnap-cdown-' + index;
			}

			const target  = resolveTarget( el );
			let   running = true;

			// Initial render (no animation on first paint).
			updateCountdown( el, target, true );

			// Tick every second.
			const interval = setInterval( function () {
				if ( ! running ) {
					clearInterval( interval );
					return;
				}
				running = updateCountdown( el, target, reducedMotion );
				if ( ! running ) {
					clearInterval( interval );
				}
			}, 1000 );
		} );
	}

	// ── Animated Number Counter ───────────────────────────────────

	/**
	 * Animate a .ls-counter element from 0 to its data-target value.
	 * Uses IntersectionObserver so it only animates when visible.
	 *
	 * @param {HTMLElement} el
	 */
	function initCounter( el ) {
		const target   = parseInt( el.dataset.target, 10 ) || 0;
		const prefix   = el.dataset.prefix || '';
		const suffix   = el.dataset.suffix || '';
		const duration = 2000; // ms
		const step     = 1000 / 60; // ~60 fps
		const steps    = Math.ceil( duration / step );
		let   current  = 0;
		let   count    = 0;

		function formatNumber( n ) {
			return prefix + n.toLocaleString( 'pt-BR' ) + suffix;
		}

		// Set initial value before animation.
		el.textContent = formatNumber( 0 );

		const interval = setInterval( function () {
			count++;
			current = Math.round( ( target / steps ) * count );

			if ( count >= steps ) {
				current = target;
				clearInterval( interval );
			}

			el.textContent = formatNumber( current );
		}, step );
	}

	/**
	 * Initialize all animated counters using IntersectionObserver.
	 */
	function initCounters() {
		const counters = document.querySelectorAll( '.ls-counter[data-target]' );
		if ( ! counters.length ) return;

		if ( 'IntersectionObserver' in window ) {
			const observer = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						if ( entry.isIntersecting ) {
							initCounter( entry.target );
							observer.unobserve( entry.target );
						}
					} );
				},
				{ threshold: 0.3 }
			);
			counters.forEach( function ( el ) { observer.observe( el ); } );
		} else {
			// Fallback for old browsers.
			counters.forEach( initCounter );
		}
	}

	// ── DOMContentLoaded ──────────────────────────────────────────

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initCountdowns();
			initCounters();
		} );
	} else {
		// Script was deferred and DOM is already ready.
		initCountdowns();
		initCounters();
	}

} )();

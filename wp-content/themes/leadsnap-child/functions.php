<?php
/**
 * LeadSnap Child Theme Functions
 *
 * @package     LeadSnap
 * @version     1.0.0
 * @author      João Rodrigues
 * @license     GPL-2.0-or-later
 * @link        https://github.com/JoaoVBRodrigues/leadsnap-child
 *
 * Table of Contents:
 * 1.  Theme Setup
 * 2.  Enqueue Scripts & Styles
 * 3.  Google Fonts
 * 4.  Countdown Widget (Shortcode + Widget)
 * 5.  Social Counter (Shortcode)
 * 6.  Current Year Shortcode
 * 7.  Lead Capture Helper Functions
 * 8.  ActiveCampaign Integration
 * 9.  Mailchimp Integration
 * 10. Webhook Integration
 * 11. LGPD / GDPR Consent Helpers
 * 12. Pixel Placeholders (Meta Pixel + GA4)
 * 13. Performance Optimizations
 * 14. Security Hardening
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Never access directly.
}

// ================================================================
// 1. THEME SETUP
// ================================================================

/**
 * LeadSnap theme setup.
 * Runs after the parent (Hello Elementor) setup.
 *
 * @since 1.0.0
 */
add_action( 'after_setup_theme', 'leadsnap_setup' );
function leadsnap_setup(): void {
	// Text domain for translations — always load before any UI text.
	load_child_theme_textdomain(
		'leadsnap-child',
		get_stylesheet_directory() . '/languages'
	);

	// Extended theme support on top of Hello Elementor defaults.
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support(
		'html5',
		[ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ]
	);
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
}

// ================================================================
// 2. ENQUEUE SCRIPTS & STYLES
// ================================================================

/**
 * Enqueue parent + child theme assets.
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', 'leadsnap_enqueue_assets' );
function leadsnap_enqueue_assets(): void {
	$version    = wp_get_theme()->get( 'Version' );
	$child_uri  = get_stylesheet_directory_uri();

	// ── Parent theme stylesheet (Hello Elementor) ──────────────────
	wp_enqueue_style(
		'hello-elementor-style',
		get_template_directory_uri() . '/style.css',
		[],
		wp_get_theme( 'hello-elementor' )->get( 'Version' )
	);

	// ── Child theme stylesheet (LeadSnap design system) ───────────
	wp_enqueue_style(
		'leadsnap-child-style',
		get_stylesheet_uri(),
		[ 'hello-elementor-style' ],
		$version
	);

	// ── Countdown JS (defer — non-blocking) ───────────────────────
	wp_enqueue_script(
		'leadsnap-countdown',
		$child_uri . '/assets/js/countdown.js',
		[],
		$version,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	// ── Main JS (animations, counters, misc) ──────────────────────
	wp_enqueue_script(
		'leadsnap-main',
		$child_uri . '/assets/js/main.js',
		[],
		$version,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	// Pass PHP data to JS where needed.
	wp_localize_script(
		'leadsnap-countdown',
		'leadsnapData',
		[
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'leadsnap_nonce' ),
			'evergreen'  => [
				'enabled'  => true,   // Evergreen countdown: resets via cookie
				'days'     => 3,      // Reset period in days
				'cookieKey' => 'leadsnap_cdown_target',
			],
		]
	);
}

// ================================================================
// 3. GOOGLE FONTS — Loaded via PHP for performance control
// ================================================================

/**
 * Preconnect to Google Fonts and enqueue Syne + DM Sans.
 * Both fonts use display=swap to prevent FOIT.
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', 'leadsnap_enqueue_fonts', 5 );
function leadsnap_enqueue_fonts(): void {
	// Preconnect hints (must be <link rel="preconnect">) — added via filter.
	add_filter( 'wp_resource_hints', 'leadsnap_resource_hints', 10, 2 );

	wp_enqueue_style(
		'leadsnap-google-fonts',
		'https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;1,9..40,400&display=swap',
		[],
		null
	);
}

/**
 * Add preconnect resource hints for Google Fonts CDN.
 *
 * @since  1.0.0
 * @param  array  $urls  URLs to print for resource hints.
 * @param  string $relation_type  The relation type (dns-prefetch, preconnect, etc.).
 * @return array
 */
function leadsnap_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = [
			'href'        => 'https://fonts.googleapis.com',
			'crossorigin' => '',
		];
		$urls[] = [
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		];
	}
	return $urls;
}

// ================================================================
// 4. COUNTDOWN WIDGET (Shortcode)
// ================================================================

/**
 * Countdown shortcode.
 *
 * Usage:
 *   Fixed date:   [leadsnap_countdown target="2026-12-31T23:59:59"]
 *   Evergreen:    [leadsnap_countdown evergreen="1" days="3"]
 *   Labels (i18n):[leadsnap_countdown target="..." labels="Dias,Horas,Min,Seg"]
 *
 * @since  1.0.0
 * @param  array $atts Shortcode attributes.
 * @return string HTML output.
 */
add_shortcode( 'leadsnap_countdown', 'leadsnap_countdown_shortcode' );
function leadsnap_countdown_shortcode( array $atts = [] ): string {
	$atts = shortcode_atts(
		[
			'target'    => '',          // ISO datetime string for fixed mode
			'evergreen' => '0',         // '1' to enable evergreen mode
			'days'      => '3',         // Days before evergreen resets
			'labels'    => __( 'Dias,Horas,Min,Seg', 'leadsnap-child' ),
			'class'     => '',          // Extra CSS classes
			'pulse'     => '1',         // Add pulse animation to numbers
		],
		$atts,
		'leadsnap_countdown'
	);

	$labels   = array_map( 'trim', explode( ',', $atts['labels'] ) );
	$labels   = array_pad( $labels, 4, '' ); // Ensure 4 labels minimum.
	$extra_class = sanitize_html_class( $atts['class'] );

	$data_attrs  = '';
	if ( '1' === $atts['evergreen'] ) {
		$data_attrs .= ' data-evergreen="1"';
		$data_attrs .= ' data-days="' . absint( $atts['days'] ) . '"';
	} elseif ( ! empty( $atts['target'] ) ) {
		// Sanitize: only allow ISO 8601-like strings.
		$target      = preg_replace( '/[^0-9T:\-+Z]/', '', $atts['target'] );
		$data_attrs .= ' data-target="' . esc_attr( $target ) . '"';
	}

	$pulse_class = '1' === $atts['pulse'] ? ' ls-pulse-mode' : '';

	ob_start();
	?>
	<div class="leadsnap-countdown<?php echo ' ' . $extra_class . $pulse_class; ?>"
		 role="timer"
		 aria-live="polite"
		 aria-label="<?php esc_attr_e( 'Contagem regressiva', 'leadsnap-child' ); ?>"
		 <?php echo $data_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attr built from sanitized data. ?>>

		<?php
		$units = [ 'days', 'hours', 'minutes', 'seconds' ];
		foreach ( $units as $i => $unit ) :
			if ( $i > 0 ) :
				?>
				<span class="ls-countdown-separator" aria-hidden="true">:</span>
				<?php
			endif;
			?>
			<span class="ls-countdown-unit">
				<span class="ls-countdown-number"
					  data-unit="<?php echo esc_attr( $unit ); ?>">00</span>
				<span class="ls-countdown-label">
					<?php echo esc_html( $labels[ $i ] ?? $unit ); ?>
				</span>
			</span>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

// ================================================================
// 5. SOCIAL COUNTER SHORTCODE
// ================================================================

/**
 * Animated social proof counter.
 *
 * Usage: [leadsnap_counter number="12000" suffix="+" prefix="" label="membros ativos"]
 *
 * @since  1.0.0
 * @param  array $atts Shortcode attributes.
 * @return string HTML output.
 */
add_shortcode( 'leadsnap_counter', 'leadsnap_counter_shortcode' );
function leadsnap_counter_shortcode( array $atts = [] ): string {
	$atts = shortcode_atts(
		[
			'number' => '12000',
			'prefix' => '',
			'suffix' => '+',
			'label'  => __( 'membros ativos', 'leadsnap-child' ),
		],
		$atts,
		'leadsnap_counter'
	);

	$number = absint( str_replace( [ '.', ',' ], '', $atts['number'] ) );

	ob_start();
	?>
	<div class="leadsnap-counter-wrapper" aria-label="<?php echo esc_attr( $atts['label'] ); ?>">
		<div class="ls-counter"
			 data-target="<?php echo esc_attr( $number ); ?>"
			 data-prefix="<?php echo esc_attr( $atts['prefix'] ); ?>"
			 data-suffix="<?php echo esc_attr( $atts['suffix'] ); ?>"
			 aria-atomic="true">
			<?php echo esc_html( $atts['prefix'] . number_format( $number, 0, ',', '.' ) . $atts['suffix'] ); ?>
		</div>
		<p class="ls-counter-label"><?php echo esc_html( $atts['label'] ); ?></p>
	</div>
	<?php
	return ob_get_clean();
}

// ================================================================
// 6. CURRENT YEAR SHORTCODE
// ================================================================

/**
 * Dynamic current year — prevents outdated copyright.
 * Usage: [leadsnap_year]
 *
 * @since 1.0.0
 */
add_shortcode( 'leadsnap_year', function(): string {
	return esc_html( gmdate( 'Y' ) );
} );

// ================================================================
// 7. LEAD CAPTURE HELPER FUNCTIONS
// ================================================================

/**
 * Sanitize and validate a phone number (WhatsApp format).
 *
 * @since  1.0.0
 * @param  string $phone Raw phone input.
 * @return string|false  Sanitized phone or false on invalid.
 */
function leadsnap_sanitize_phone( string $phone ) {
	// Keep only digits and the leading +.
	$phone = preg_replace( '/[^\d+]/', '', $phone );

	// Brazilian mobile: 10–11 digits or E.164 format (+55XXXXXXXXXXX).
	if ( preg_match( '/^\+?\d{10,15}$/', $phone ) ) {
		return $phone;
	}

	return false;
}

/**
 * Get the client IP address for lead logging.
 * Never stores or processes without consent — used for rate limiting.
 *
 * @since  1.0.0
 * @return string Anonymized IP (last octet stripped for privacy).
 */
function leadsnap_get_client_ip(): string {
	$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; // phpcs:ignore WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders
	// Anonymize last octet for LGPD compliance.
	return preg_replace( '/(\d+)$/', '0', $ip ) ?? '0.0.0.0';
}

// ================================================================
// 8. ACTIVECAMPAIGN INTEGRATION
// ================================================================
// Configure via WordPress admin or wp-config.php:
//   define('LEADSNAP_AC_API_URL',   'https://YOURACCOUNTNAME.api-us1.com');
//   define('LEADSNAP_AC_API_KEY',   'your-api-key-here');
//   define('LEADSNAP_AC_LIST_ID',   '1');
//
// The Elementor Form webhook action sends a POST to:
//   admin-ajax.php?action=leadsnap_ac_subscribe
// ================================================================

/**
 * AJAX handler: subscribe a lead to ActiveCampaign.
 * Called by Elementor form webhook or manually.
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_nopriv_leadsnap_ac_subscribe', 'leadsnap_ac_subscribe_handler' );
add_action( 'wp_ajax_leadsnap_ac_subscribe',         'leadsnap_ac_subscribe_handler' );
function leadsnap_ac_subscribe_handler(): void {
	// Nonce verification (Elementor sends custom headers — accept either).
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'leadsnap_nonce' ) ) {
		// Soft-fail: log but do not expose error to frontend.
		error_log( '[LeadSnap] AC subscribe: nonce verification failed.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		wp_send_json_error( [ 'message' => 'Invalid request.' ], 403 );
	}

	$api_url = defined( 'LEADSNAP_AC_API_URL' ) ? LEADSNAP_AC_API_URL : '';
	$api_key = defined( 'LEADSNAP_AC_API_KEY' ) ? LEADSNAP_AC_API_KEY : '';
	$list_id = defined( 'LEADSNAP_AC_LIST_ID' ) ? LEADSNAP_AC_LIST_ID : '1';

	if ( empty( $api_url ) || empty( $api_key ) ) {
		// AC not configured — skip silently (don't break form flow).
		wp_send_json_success( [ 'message' => 'AC not configured — skipped.' ] );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$name  = isset( $_POST['name'] )  ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? leadsnap_sanitize_phone( sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( [ 'message' => 'Invalid email.' ], 422 );
	}

	$body = [
		'contact' => [
			'email'     => $email,
			'firstName' => explode( ' ', $name )[0],
			'lastName'  => implode( ' ', array_slice( explode( ' ', $name ), 1 ) ) ?: '',
			'phone'     => $phone ?: '',
		],
	];

	// 1. Create/update contact.
	$response = wp_remote_post(
		trailingslashit( $api_url ) . 'api/3/contacts',
		[
			'headers' => [
				'Api-Token'    => $api_key,
				'Content-Type' => 'application/json',
			],
			'body'    => wp_json_encode( $body ),
			'timeout' => 10,
		]
	);

	if ( is_wp_error( $response ) ) {
		error_log( '[LeadSnap] AC API error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		wp_send_json_error( [ 'message' => 'Integration error.' ], 500 );
	}

	$data       = json_decode( wp_remote_retrieve_body( $response ), true );
	$contact_id = $data['contact']['id'] ?? null;

	// 2. Add contact to list.
	if ( $contact_id ) {
		wp_remote_post(
			trailingslashit( $api_url ) . 'api/3/contactLists',
			[
				'headers' => [
					'Api-Token'    => $api_key,
					'Content-Type' => 'application/json',
				],
				'body'    => wp_json_encode( [
					'contactList' => [
						'list'    => (int) $list_id,
						'contact' => (int) $contact_id,
						'status'  => 1, // Subscribed.
					],
				] ),
				'timeout' => 10,
			]
		);
	}

	wp_send_json_success( [ 'message' => 'Subscribed to ActiveCampaign.' ] );
}

// ================================================================
// 9. MAILCHIMP INTEGRATION
// ================================================================
// Configure via wp-config.php:
//   define('LEADSNAP_MC_API_KEY',       'your-api-key-here');
//   define('LEADSNAP_MC_LIST_ID',       'abc123de'); // Audience ID
//   define('LEADSNAP_MC_SERVER_PREFIX', 'us21');     // e.g., us1, us21
// ================================================================

/**
 * AJAX handler: subscribe a lead to Mailchimp.
 *
 * @since 1.0.0
 */
add_action( 'wp_ajax_nopriv_leadsnap_mc_subscribe', 'leadsnap_mc_subscribe_handler' );
add_action( 'wp_ajax_leadsnap_mc_subscribe',         'leadsnap_mc_subscribe_handler' );
function leadsnap_mc_subscribe_handler(): void {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'leadsnap_nonce' ) ) {
		wp_send_json_error( [ 'message' => 'Invalid request.' ], 403 );
	}

	$api_key = defined( 'LEADSNAP_MC_API_KEY' )       ? LEADSNAP_MC_API_KEY       : '';
	$list_id = defined( 'LEADSNAP_MC_LIST_ID' )       ? LEADSNAP_MC_LIST_ID       : '';
	$server  = defined( 'LEADSNAP_MC_SERVER_PREFIX' ) ? LEADSNAP_MC_SERVER_PREFIX : 'us1';

	if ( empty( $api_key ) || empty( $list_id ) ) {
		wp_send_json_success( [ 'message' => 'Mailchimp not configured — skipped.' ] );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$name  = isset( $_POST['name'] )  ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( [ 'message' => 'Invalid email.' ], 422 );
	}

	$name_parts = explode( ' ', trim( $name ) );

	$response = wp_remote_post(
		"https://{$server}.api.mailchimp.com/3.0/lists/{$list_id}/members",
		[
			'headers' => [
				'Authorization' => 'Basic ' . base64_encode( 'anystring:' . $api_key ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				'Content-Type'  => 'application/json',
			],
			'body' => wp_json_encode( [
				'email_address' => $email,
				'status'        => 'subscribed', // Or 'pending' for double opt-in.
				'merge_fields'  => [
					'FNAME' => $name_parts[0]                                                ?? '',
					'LNAME' => implode( ' ', array_slice( $name_parts, 1 ) ) ?: '',
				],
			] ),
			'timeout' => 10,
		]
	);

	if ( is_wp_error( $response ) ) {
		error_log( '[LeadSnap] Mailchimp API error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		wp_send_json_error( [ 'message' => 'Integration error.' ], 500 );
	}

	$status_code = wp_remote_retrieve_response_code( $response );
	if ( in_array( $status_code, [ 200, 400 ], true ) ) {
		// 400 with 'Member Exists' is acceptable — already subscribed.
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( 400 === $status_code && isset( $data['title'] ) && 'Member Exists' === $data['title'] ) {
			wp_send_json_success( [ 'message' => 'Already subscribed.' ] );
		}
	}

	wp_send_json_success( [ 'message' => 'Subscribed to Mailchimp.' ] );
}

// ================================================================
// 10. GENERIC WEBHOOK INTEGRATION
// ================================================================
// The Elementor Form "Webhook" action sends a POST to any URL.
// This helper function forwards data to any endpoint.
// Configure in Elementor: Form > Actions > Webhook > URL
//
// Example webhook URL for Zapier / Make / n8n:
//   https://hooks.zapier.com/hooks/catch/XXXXX/YYYYY/
//
// The handler below can be used for custom PHP-level forwarding.
// ================================================================

/**
 * Forward lead data to a generic webhook endpoint.
 * Called after Elementor form submission via action hook.
 *
 * @since  1.0.0
 * @param  string $webhook_url  The webhook endpoint URL.
 * @param  array  $payload      Data to send (will be JSON-encoded).
 * @return bool   True on success, false on failure.
 */
function leadsnap_send_webhook( string $webhook_url, array $payload ): bool {
	if ( ! filter_var( $webhook_url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	$response = wp_remote_post(
		$webhook_url,
		[
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( $payload ),
			'timeout' => 15,
		]
	);

	if ( is_wp_error( $response ) ) {
		error_log( '[LeadSnap] Webhook error: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return false;
	}

	return true;
}

// ================================================================
// 11. LGPD / GDPR CONSENT HELPERS
// ================================================================

/**
 * Register LGPD consent pages (Privacy Policy + Terms of Use).
 * Creates placeholder pages if they don't exist yet.
 *
 * @since 1.0.0
 */
add_action( 'after_switch_theme', 'leadsnap_create_legal_pages' );
function leadsnap_create_legal_pages(): void {
	$pages = [
		[
			'title'   => __( 'Política de Privacidade', 'leadsnap-child' ),
			'slug'    => 'politica-de-privacidade',
			'content' => '<p>' . __( '[Substitua este texto pela sua política de privacidade conforme a LGPD — Lei 13.709/2018. Este é um placeholder de demonstração.]', 'leadsnap-child' ) . '</p>',
		],
		[
			'title'   => __( 'Termos de Uso', 'leadsnap-child' ),
			'slug'    => 'termos-de-uso',
			'content' => '<p>' . __( '[Substitua este texto pelos seus termos de uso. Este é um placeholder de demonstração.]', 'leadsnap-child' ) . '</p>',
		],
	];

	foreach ( $pages as $page_data ) {
		$existing = get_page_by_path( $page_data['slug'] );
		if ( $existing ) {
			continue; // Do not overwrite existing pages.
		}

		wp_insert_post( [
			'post_title'   => $page_data['title'],
			'post_name'    => $page_data['slug'],
			'post_content' => $page_data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
		] );
	}
}

// ================================================================
// 12. PIXEL PLACEHOLDERS
// ================================================================
// HOW TO USE:
//   1. Meta Pixel: Replace 'YOUR_PIXEL_ID_HERE' with your actual
//      Meta Pixel ID (found in Events Manager > Data Sources).
//      Then uncomment the leadsnap_meta_pixel() function body.
//
//   2. GA4: Replace 'G-XXXXXXXXXX' with your Measurement ID
//      (found in Analytics > Admin > Data Streams > Web).
//      Then uncomment the leadsnap_ga4() function body.
//
//   3. Never hardcode pixel IDs in production templates that
//      will be redistributed — always use placeholder comments
//      or a settings page instead.
// ================================================================

/**
 * Meta Pixel placeholder.
 * Uncomment and replace ID when configuring for a client/demo.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'leadsnap_meta_pixel', 1 );
function leadsnap_meta_pixel(): void {
	// ─────────────────────────────────────────────────────────────
	// META PIXEL PLACEHOLDER — INSTRUÇÕES DE CONFIGURAÇÃO:
	// 1. Substitua 'YOUR_PIXEL_ID_HERE' pelo ID do seu Meta Pixel
	// 2. Remova o comentário abaixo para ativar o rastreamento
	// ─────────────────────────────────────────────────────────────
	// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	/*
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', 'YOUR_PIXEL_ID_HERE');
	fbq('track', 'PageView');
	</script>
	<noscript>
		<img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID_HERE&ev=PageView&noscript=1"/>
	</noscript>
	*/
	// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
}

/**
 * Google Analytics 4 (GA4) placeholder.
 * Uncomment and replace Measurement ID when configuring.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'leadsnap_ga4', 2 );
function leadsnap_ga4(): void {
	// ─────────────────────────────────────────────────────────────
	// GA4 PLACEHOLDER — INSTRUÇÕES DE CONFIGURAÇÃO:
	// 1. Substitua 'G-XXXXXXXXXX' pelo seu Measurement ID do GA4
	// 2. Remova o comentário abaixo para ativar o rastreamento
	// ─────────────────────────────────────────────────────────────
	// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	/*
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'G-XXXXXXXXXX');
	</script>
	*/
	// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
}

// ================================================================
// 13. PERFORMANCE OPTIMIZATIONS
// ================================================================

/**
 * Remove unnecessary WordPress head elements for landing pages.
 * Reduces HTTP requests and page weight.
 *
 * @since 1.0.0
 */
add_action( 'init', 'leadsnap_cleanup_head' );
function leadsnap_cleanup_head(): void {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );        // Hide WP version.
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
}

/**
 * Add preload hints for critical assets.
 *
 * @since 1.0.0
 */
add_action( 'wp_head', 'leadsnap_preload_assets', 1 );
function leadsnap_preload_assets(): void {
	$child_uri = get_stylesheet_directory_uri();
	?>
	<link rel="preload" href="<?php echo esc_url( $child_uri . '/assets/js/countdown.js' ); ?>" as="script">
	<?php
}

// ================================================================
// 14. SECURITY HARDENING
// ================================================================

// Disable file editing in admin (defense in depth).
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}

/**
 * Add security headers via wp_headers filter.
 *
 * @since  1.0.0
 * @param  array $headers Existing headers.
 * @return array Modified headers.
 */
add_filter( 'wp_headers', 'leadsnap_security_headers' );
function leadsnap_security_headers( array $headers ): array {
	$headers['X-Content-Type-Options'] = 'nosniff';
	$headers['X-Frame-Options']        = 'SAMEORIGIN';
	$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
	$headers['Permissions-Policy']     = 'camera=(), microphone=(), geolocation=()';
	return $headers;
}

/**
 * Remove WP version from scripts/styles query string.
 * Obscures WordPress version from automated scanners.
 *
 * @since  1.0.0
 * @param  string $src Asset URL.
 * @return string      URL without ?ver= parameter.
 */
add_filter( 'script_loader_src', 'leadsnap_remove_ver_param', 15 );
add_filter( 'style_loader_src',  'leadsnap_remove_ver_param', 15 );
function leadsnap_remove_ver_param( string $src ): string {
	if ( is_admin() ) {
		return $src;
	}
	// Only remove ver from WordPress core assets, not third-party.
	if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) !== false ) {
		return remove_query_arg( 'ver', $src );
	}
	return $src;
}

// ================================================================
// 15. CUSTOM CONTACT FORM FOR ELEMENTOR FREE
// ================================================================

add_shortcode( 'leadsnap_form', 'leadsnap_free_form_shortcode' );
function leadsnap_free_form_shortcode(): string {
	$thankyou_url = get_permalink( get_page_by_path( 'obrigado' ) ) ?: home_url( '/obrigado' );
	$privacy_url  = get_permalink( get_page_by_path( 'politica-de-privacidade' ) ) ?: home_url( '/politica-de-privacidade' );
	$terms_url    = get_permalink( get_page_by_path( 'termos-de-uso' ) ) ?: home_url( '/termos-de-uso' );

	ob_start();
	?>
	<form action="<?php echo esc_url( $thankyou_url ); ?>" method="POST" class="leadsnap-custom-form">
		<div class="leadsnap-form-field-group">
			<label for="ls-name" class="leadsnap-form-label"><?php esc_html_e( 'Seu nome completo', 'leadsnap-child' ); ?></label>
			<input type="text" id="ls-name" name="leadsnap_name" placeholder="<?php esc_attr_e( 'Ex: Maria Silva', 'leadsnap-child' ); ?>" required class="leadsnap-form-input">
		</div>

		<div class="leadsnap-form-field-group">
			<label for="ls-email" class="leadsnap-form-label"><?php esc_html_e( 'Seu melhor e-mail', 'leadsnap-child' ); ?></label>
			<input type="email" id="ls-email" name="leadsnap_email" placeholder="<?php esc_attr_e( 'Ex: maria@email.com', 'leadsnap-child' ); ?>" required class="leadsnap-form-input">
		</div>

		<div class="leadsnap-form-field-group">
			<label for="ls-phone" class="leadsnap-form-label"><?php esc_html_e( 'WhatsApp (opcional)', 'leadsnap-child' ); ?></label>
			<input type="tel" id="ls-phone" name="leadsnap_phone" placeholder="<?php esc_attr_e( 'Ex: (11) 99999-0000', 'leadsnap-child' ); ?>" class="leadsnap-form-input">
		</div>

		<div class="leadsnap-form-field-group leadsnap-form-acceptance">
			<label class="leadsnap-lgpd-label">
				<input type="checkbox" name="leadsnap_lgpd" required value="1">
				<span>
					<?php
					printf(
						/* translators: 1: privacy policy url, 2: terms url */
						__( 'Concordo com a <a href="%1$s" target="_blank">Política de Privacidade</a> e os <a href="%2$s" target="_blank">Termos de Uso</a>.', 'leadsnap-child' ),
						esc_url( $privacy_url ),
						esc_url( $terms_url )
					);
					?>
				</span>
			</label>
		</div>

		<button type="submit" class="leadsnap-cta-btn leadsnap-submit-btn ls-pulse">
			<?php esc_html_e( 'GARANTIR MINHA VAGA AGORA 🚀', 'leadsnap-child' ); ?>
		</button>
	</form>
	<?php
	return ob_get_clean();
}

// ================================================================
// 16. THEME SETUP ACTIONS
// ================================================================

add_action( 'after_switch_theme', 'leadsnap_theme_setup_actions' );
function leadsnap_theme_setup_actions(): void {
	// Habilita links amigáveis se ainda não estiver configurado
	if ( get_option( 'permalink_structure' ) !== '/%postname%/' ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	// Garante que as regras de reescrita sejam atualizadas
	flush_rewrite_rules( true );
}


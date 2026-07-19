<?php
/**
 * LeadSnap — One-Click Setup Script
 *
 * COMO USAR:
 * 1. Acesse: http://localhost:8080/leadsnap-setup.php
 * 2. As páginas serão criadas automaticamente com conteúdo Elementor
 * 3. APAGUE este arquivo após o setup (por segurança)
 *
 * O que este script faz:
 * - Cria a página "LeadSnap Demo" com layout Elementor completo
 * - Cria a página "Obrigado"
 * - Define a página de captura como Front Page
 * - Configura as opções de leitura do WordPress
 *
 * @package LeadSnap
 * @version 1.0.0
 */

// ── Segurança: só executa via web, não via include ────────────────
if ( php_sapi_name() === 'cli' ) {
	die( 'Run via browser: http://localhost:8080/leadsnap-setup.php' );
}

// ── Bootstrap WordPress ───────────────────────────────────────────
define( 'ABSPATH', __DIR__ . '/' );
require_once __DIR__ . '/wp-load.php';

// ── Autenticação básica ───────────────────────────────────────────
if ( ! is_user_logged_in() ) {
	wp_redirect( wp_login_url( home_url( '/leadsnap-setup.php' ) ) );
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Acesso negado. Faça login como administrador.' );
}

// ── Funções auxiliares ────────────────────────────────────────────

/**
 * Gera um ID único para elementos Elementor (8 chars hex).
 */
function ls_uid(): string {
	return substr( md5( uniqid( '', true ) ), 0, 8 );
}

/**
 * Cria ou retorna uma página existente pelo slug.
 *
 * @param string $title
 * @param string $slug
 * @param string $status
 * @return int Post ID
 */
function ls_create_page( string $title, string $slug, string $status = 'publish' ): int {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}

	return wp_insert_post( [
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => $status,
		'post_type'    => 'page',
		'post_author'  => get_current_user_id(),
		'post_content' => '',
	] );
}

/**
 * Aplica os meta dados do Elementor a uma página.
 *
 * @param int   $post_id
 * @param array $elementor_data  Array de seções Elementor
 */
function ls_set_elementor_data( int $post_id, array $elementor_data ): void {
	$json = wp_json_encode( $elementor_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

	update_post_meta( $post_id, '_elementor_data',          wp_slash( $json ) );
	update_post_meta( $post_id, '_elementor_edit_mode',     'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version',       '3.25.0' );
	update_post_meta( $post_id, '_elementor_page_settings', [
		'hide_title'     => 'yes',
		'page_layout'    => 'elementor_canvas',
		'body_background_color' => '#0B1120',
	] );
	update_post_meta( $post_id, '_wp_page_template',        'elementor_canvas' );

	// Invalidate Elementor CSS cache so styles regenerate.
	delete_post_meta( $post_id, '_elementor_css' );
}

// ──────────────────────────────────────────────────────────────────
// ELEMENTOR DATA — LANDING PAGE COMPLETA
// ──────────────────────────────────────────────────────────────────

function ls_build_landing_page_data(): array {
	return [

		// ══════════════════════════════════════════════════════════
		// SEÇÃO 1 — HERO
		// ══════════════════════════════════════════════════════════
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background'     => 'classic',
				'background_color'          => '#0B1120',
				'padding'                   => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_tablet'            => [ 'top' => '60', 'right' => '20', 'bottom' => '60', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'            => [ 'top' => '40', 'right' => '16', 'bottom' => '40', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                    => 'full_width',
				'content_position'          => 'middle',
				'structure'                 => '20',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100, 'column_padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ] ],
					'elements' => [

						// Badge de urgência
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html'  => '<div style="text-align:center;margin-bottom:24px"><span class="leadsnap-badge" style="background:#FFC107;color:#1a1a1a;padding:8px 20px;border-radius:999px;font-family:\'DM Sans\',sans-serif;font-size:13px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;display:inline-flex;align-items:center;gap:8px"><span style="width:8px;height:8px;background:#FF5722;border-radius:50%;display:inline-block;animation:blink 1.2s ease-in-out infinite"></span>⚡ VAGAS LIMITADAS — Entre antes de esgotar</span></div>',
							],
						],

						// Headline principal
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'        => 'Acesse as Melhores Ofertas<br><span style="color:#FF5722">Antes de Todo Mundo</span>',
								'header_size'  => 'h1',
								'align'        => 'center',
								'title_color'  => '#F8FAFC',
								'typography_typography'   => 'custom',
								'typography_font_family'  => 'Syne',
								'typography_font_size'    => [ 'unit' => 'px', 'size' => 60 ],
								'typography_font_size_tablet' => [ 'unit' => 'px', 'size' => 44 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 32 ],
								'typography_font_weight' => '800',
								'typography_line_height' => [ 'unit' => 'em', 'size' => 1.15 ],
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '16', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Subheadline
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => [
								'editor'   => '<p style="text-align:center;color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:20px;line-height:1.7;max-width:620px;margin:0 auto 32px">Entre no grupo exclusivo e receba cupons de <strong style="color:#F8FAFC">até 80% OFF</strong> diretamente no seu WhatsApp — sem custo algum.</p>',
							],
						],

						// Countdown
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '<div style="text-align:center;margin-bottom:32px"><p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:13px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:12px">⏱ Oferta expira em:</p>' . do_shortcode( '[leadsnap_countdown evergreen="1" days="3"]' ) . '</div>',
							],
						],

						// CTA Button Principal
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'button',
							'settings'   => [
								'text'             => 'QUERO ENTRAR AGORA →',
								'link'             => [ 'url' => '#formulario', 'is_external' => '' ],
								'align'            => 'center',
								'background_color' => '#FF5722',
								'hover_color'      => '#ffffff',
								'background_hover_color' => '#E64A19',
								'border_radius'    => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => true ],
								'text_padding'     => [ 'top' => '18', 'right' => '48', 'bottom' => '18', 'left' => '48', 'unit' => 'px', 'isLinked' => false ],
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 18 ],
								'typography_font_weight' => '700',
								'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 0.5 ],
								'button_text_color' => '#ffffff',
								'css_classes'       => 'leadsnap-cta-btn ls-pulse',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '16', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Nota de prova social abaixo do CTA
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '<p style="text-align:center;color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin-top:8px">🔒 Grátis · Sem spam · Cancele quando quiser</p>',
							],
						],

					],
				],
			],
		],

		// ══════════════════════════════════════════════════════════
		// SEÇÃO 2 — PROVA SOCIAL (Contador + Depoimentos)
		// ══════════════════════════════════════════════════════════
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0F172A',
				'padding'               => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100 ],
					'elements' => [

						// Contador de membros
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '<div style="text-align:center;margin-bottom:48px">' . do_shortcode( '[leadsnap_counter number="12000" suffix="+" label="membros ativos no grupo"]' ) . '</div>',
							],
						],

						// Título da seção
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'       => 'O que nossos membros dizem',
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => '#F8FAFC',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 36 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 26 ],
								'typography_font_weight' => '700',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '40', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Depoimentos em HTML
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;max-width:1100px;margin:0 auto">

  <div class="leadsnap-testimonial-card" style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div class="ls-avatar-initials" style="width:48px;height:48px;border-radius:50%;background:#FF5722;color:#fff;font-family:Syne,sans-serif;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">MS</div>
      <div>
        <p style="color:#F8FAFC;font-family:\'DM Sans\',sans-serif;font-weight:700;margin:0;font-size:15px">Maria S.</p>
        <p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">São Paulo, SP</p>
      </div>
    </div>
    <p class="ls-stars" style="color:#FFC107;font-size:14px;letter-spacing:2px;margin-bottom:12px">★★★★★</p>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">"Economizei mais de R$ 400 no primeiro mês. Os cupons chegam antes de qualquer outro grupo que participo!"</p>
  </div>

  <div class="leadsnap-testimonial-card" style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:48px;height:48px;border-radius:50%;background:#3B82F6;color:#fff;font-family:Syne,sans-serif;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">RC</div>
      <div>
        <p style="color:#F8FAFC;font-family:\'DM Sans\',sans-serif;font-weight:700;margin:0;font-size:15px">Roberto C.</p>
        <p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">Belo Horizonte, MG</p>
      </div>
    </div>
    <p style="color:#FFC107;font-size:14px;letter-spacing:2px;margin-bottom:12px">★★★★★</p>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">"Uso para comprar itens da minha loja. Os descontos são reais e chegam antes de esgotar. Vale muito!"</p>
  </div>

  <div class="leadsnap-testimonial-card" style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:48px;height:48px;border-radius:50%;background:#22C55E;color:#fff;font-family:Syne,sans-serif;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">AL</div>
      <div>
        <p style="color:#F8FAFC;font-family:\'DM Sans\',sans-serif;font-weight:700;margin:0;font-size:15px">Ana L.</p>
        <p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">Curitiba, PR</p>
      </div>
    </div>
    <p style="color:#FFC107;font-size:14px;letter-spacing:2px;margin-bottom:12px">★★★★★</p>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">"Indiquei para toda a família. É o único grupo de ofertas que não te enche de spam — só cupons bons!"</p>
  </div>

</div>',
							],
						],
					],
				],
			],
		],

		// ══════════════════════════════════════════════════════════
		// SEÇÃO 3 — BENEFÍCIOS
		// ══════════════════════════════════════════════════════════
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0B1120',
				'padding'               => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100 ],
					'elements' => [

						// Heading
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'       => 'Por que entrar agora?',
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => '#F8FAFC',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 36 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 26 ],
								'typography_font_weight' => '700',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Grid de benefícios
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;max-width:1100px;margin:0 auto 48px">

  <div class="leadsnap-benefit-card" style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;text-align:center;transition:border-color .2s">
    <div style="width:56px;height:56px;background:rgba(255,87,34,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#FF5722" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h3 style="color:#F8FAFC;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 12px">Cupons Exclusivos Primeiro</h3>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Receba ofertas antes de qualquer grupo público. Nossos membros têm acesso prioritário antes do estoque esgotar.</p>
  </div>

  <div class="leadsnap-benefit-card" style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;text-align:center">
    <div style="width:56px;height:56px;background:rgba(59,130,246,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#3B82F6" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    </div>
    <h3 style="color:#F8FAFC;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 12px">Alertas em Tempo Real</h3>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Notificação direta no WhatsApp assim que a oferta é publicada. Sem precisar ficar verificando manualmente.</p>
  </div>

  <div class="leadsnap-benefit-card" style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;text-align:center">
    <div style="width:56px;height:56px;background:rgba(34,197,94,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#22C55E" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h3 style="color:#F8FAFC;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 12px">100% Gratuito</h3>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Zero mensalidade, zero taxa de entrada. O grupo é e sempre será gratuito para todos os membros.</p>
  </div>

</div>

<!-- Callout Box -->
<div style="max-width:800px;margin:0 auto;background:linear-gradient(135deg,rgba(255,87,34,.15),rgba(255,193,7,.1));border:1px solid rgba(255,87,34,.3);border-radius:16px;padding:32px;text-align:center">
  <p style="color:#FFC107;font-family:Syne,sans-serif;font-weight:700;font-size:20px;margin:0 0 8px">⚡ Atenção: vagas do grupo são limitadas</p>
  <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:16px;line-height:1.7;margin:0">Para manter a qualidade das ofertas e o desempenho do grupo, fechamos as inscrições periodicamente. Garanta sua vaga agora enquanto está aberto.</p>
</div>',
							],
						],

					],
				],
			],
		],

		// ══════════════════════════════════════════════════════════
		// SEÇÃO 4 — FORMULÁRIO DE CAPTURA
		// ══════════════════════════════════════════════════════════
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0F172A',
				'padding'               => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
				'custom_id'             => 'formulario',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100 ],
					'elements' => [

						// Heading
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'       => 'Garantir Minha Vaga Agora',
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => '#F8FAFC',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 36 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 26 ],
								'typography_font_weight' => '700',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '12', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Sub do formulário
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => [
								'editor' => '<p style="text-align:center;color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:17px;margin-bottom:40px">Preencha abaixo e receba o acesso em menos de 1 minuto</p>',
							],
						],

						// Formulário customizado para Elementor Free
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '[leadsnap_form]',
							],
						],

					],
				],
			],
		],

		// ══════════════════════════════════════════════════════════
		// SEÇÃO 5 — FAQ
		// ══════════════════════════════════════════════════════════
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0B1120',
				'padding'               => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100 ],
					'elements' => [

						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'       => 'Perguntas Frequentes',
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => '#F8FAFC',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 36 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 26 ],
								'typography_font_weight' => '700',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'accordion',
							'settings'   => [
								'tabs'  => [
									[ 'tab_title' => 'Como recebo as ofertas?',          'tab_content' => 'Após se cadastrar, você receberá um link para o grupo do WhatsApp. As ofertas são enviadas diretamente no grupo em tempo real, assim que são selecionadas pela nossa equipe.' ],
									[ 'tab_title' => 'O grupo é realmente gratuito?',   'tab_content' => 'Sim, 100% gratuito. Não há mensalidade, taxa de entrada ou qualquer cobrança agora ou no futuro.' ],
									[ 'tab_title' => 'Posso sair quando quiser?',       'tab_content' => 'Claro! Você pode sair do grupo do WhatsApp a qualquer momento, sem necessidade de avisar ou pagar qualquer taxa.' ],
									[ 'tab_title' => 'Quantas ofertas recebo por dia?', 'tab_content' => 'Em média, enviamos de 5 a 10 cupons e ofertas selecionadas por dia. Priorizamos qualidade — só mandamos quando a oferta é realmente boa.' ],
									[ 'tab_title' => 'Meus dados estão seguros?',       'tab_content' => 'Sim. Seus dados são tratados conforme a LGPD (Lei 13.709/2018). Nunca vendemos ou compartilhamos suas informações com terceiros. Consulte nossa <a href="/politica-de-privacidade" style="color:#FF5722">Política de Privacidade</a>.' ],
								],
								'title_html_tag' => 'h3',
								'border_color'         => 'rgba(255,255,255,.08)',
								'title_color'          => '#F8FAFC',
								'title_active_color'   => '#FF5722',
								'content_color'        => '#94A3B8',
								'icon_color'           => '#FF5722',
								'icon_active_color'    => '#FF5722',
								'typography_typography' => 'custom',
								'typography_font_family' => 'DM Sans',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 17 ],
								'typography_font_weight' => '600',
								'content_typography_typography' => 'custom',
								'content_typography_font_family' => 'DM Sans',
								'content_typography_font_size'   => [ 'unit' => 'px', 'size' => 15 ],
								'_element_width'        => 'initial',
								'_element_custom_width' => [ 'unit' => 'px', 'size' => 800 ],
								'_element_vertical_align' => 'center',
								'css_classes'           => 'ls-section-padding',
							],
						],

					],
				],
			],
		],

		// ══════════════════════════════════════════════════════════
		// SEÇÃO 6 — CTA FINAL + RODAPÉ
		// ══════════════════════════════════════════════════════════
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0F172A',
				'border_top_width'      => [ 'top' => '1', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
				'border_color'          => 'rgba(255,255,255,.06)',
				'padding'               => [ 'top' => '64', 'right' => '20', 'bottom' => '48', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '40', 'right' => '16', 'bottom' => '32', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100 ],
					'elements' => [

						// CTA final repetido
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'       => 'Não perca mais essa chance',
								'header_size' => 'h2',
								'align'       => 'center',
								'title_color' => '#F8FAFC',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 32 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 24 ],
								'typography_font_weight' => '700',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '16', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'button',
							'settings'   => [
								'text'                   => 'QUERO ENTRAR AGORA →',
								'link'                   => [ 'url' => '#formulario', 'is_external' => '' ],
								'align'                  => 'center',
								'background_color'       => '#FF5722',
								'background_hover_color' => '#E64A19',
								'button_text_color'      => '#ffffff',
								'border_radius'          => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => true ],
								'text_padding'           => [ 'top' => '18', 'right' => '48', 'bottom' => '18', 'left' => '48', 'unit' => 'px', 'isLinked' => false ],
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 18 ],
								'typography_font_weight' => '700',
								'css_classes'            => 'leadsnap-cta-btn',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Rodapé com links legais e copyright
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '<div class="leadsnap-footer" style="text-align:center;border-top:1px solid rgba(255,255,255,.06);padding-top:32px">
  <p style="color:#475569;font-family:\'DM Sans\',sans-serif;font-size:14px;margin-bottom:12px">
    <a href="/politica-de-privacidade" style="color:#475569;text-decoration:none;margin:0 12px">Política de Privacidade</a>
    <span style="color:#334155">·</span>
    <a href="/termos-de-uso" style="color:#475569;text-decoration:none;margin:0 12px">Termos de Uso</a>
    <span style="color:#334155">·</span>
    <a href="#formulario" style="color:#475569;text-decoration:none;margin:0 12px">Contato</a>
  </p>
  <p style="color:#334155;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">
    © ' . gmdate( 'Y' ) . ' LeadSnap. Todos os direitos reservados.<br>
    <small style="color:#1E293B">Este é um template de demonstração. Substitua com seus dados reais.</small>
  </p>
</div>',
							],
						],

					],
				],
			],
		],

	];
}

// ──────────────────────────────────────────────────────────────────
// PÁGINA DE OBRIGADO
// ──────────────────────────────────────────────────────────────────

function ls_build_thank_you_data(): array {
	return [
		[
			'id'       => ls_uid(),
			'elType'   => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0B1120',
				'min_height'            => [ 'unit' => 'vh', 'size' => 100 ],
				'content_position'      => 'middle',
				'padding'               => [ 'top' => '80', 'right' => '20', 'bottom' => '80', 'left' => '20', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [
				[
					'id'       => ls_uid(),
					'elType'   => 'column',
					'settings' => [ '_column_size' => 100 ],
					'elements' => [

						// Ícone de sucesso
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '<div style="text-align:center;margin-bottom:32px"><div style="width:80px;height:80px;background:rgba(34,197,94,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#22C55E" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div></div>',
							],
						],

						// Título
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'heading',
							'settings'   => [
								'title'       => '🎉 Você está dentro!',
								'header_size' => 'h1',
								'align'       => 'center',
								'title_color' => '#F8FAFC',
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 48 ],
								'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 32 ],
								'typography_font_weight' => '800',
								'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '16', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
							],
						],

						// Instrução
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'text-editor',
							'settings'   => [
								'editor' => '<p style="text-align:center;color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:18px;line-height:1.7;max-width:560px;margin:0 auto 40px">Verifique seu <strong style="color:#F8FAFC">WhatsApp e e-mail</strong> — você receberá o link de acesso em instantes.<br><br>Enquanto isso, que tal indicar para um amigo?</p>',
							],
						],

						// Botão compartilhar
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'button',
							'settings'   => [
								'text'                   => '📲 Compartilhar com um amigo',
								'link'                   => [ 'url' => 'https://api.whatsapp.com/send?text=' . rawurlencode( 'Acabei de entrar no melhor grupo de ofertas! Entre você também: ' . home_url() ), 'is_external' => 'on' ],
								'align'                  => 'center',
								'background_color'       => '#22C55E',
								'background_hover_color' => '#16A34A',
								'button_text_color'      => '#ffffff',
								'border_radius'          => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => true ],
								'text_padding'           => [ 'top' => '16', 'right' => '40', 'bottom' => '16', 'left' => '40', 'unit' => 'px', 'isLinked' => false ],
								'typography_typography'  => 'custom',
								'typography_font_family' => 'Syne',
								'typography_font_size'   => [ 'unit' => 'px', 'size' => 17 ],
								'typography_font_weight' => '700',
							],
						],

						// Pixel de conversão placeholder (comentado)
						[
							'id'         => ls_uid(),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => [
								'html' => '<!-- PIXEL DE CONVERSÃO
Para ativar o evento de conversão do Meta Pixel nesta página:
1. Ative o Meta Pixel em functions.php (descomente leadsnap_meta_pixel())
2. Adicione abaixo: fbq("track", "Lead");
Para GA4: gtag("event", "generate_lead");
-->',
							],
						],

					],
				],
			],
		],
	];
}

// ──────────────────────────────────────────────────────────────────
// EXECUÇÃO
// ──────────────────────────────────────────────────────────────────

$results = [];
$errors  = [];

// 1. Criar página de captura principal
$landing_id = ls_create_page( 'LeadSnap Demo', 'leadsnap-demo' );
if ( is_wp_error( $landing_id ) ) {
	$errors[] = 'Erro ao criar página principal: ' . $landing_id->get_error_message();
} else {
	ls_set_elementor_data( $landing_id, ls_build_landing_page_data() );
	$results[] = [
		'label' => '✅ Página de Captura',
		'value' => get_permalink( $landing_id ),
		'id'    => $landing_id,
	];
}

// 2. Criar página de Obrigado
$thank_you_id = ls_create_page( 'Obrigado', 'obrigado' );
if ( is_wp_error( $thank_you_id ) ) {
	$errors[] = 'Erro ao criar página de Obrigado: ' . $thank_you_id->get_error_message();
} else {
	ls_set_elementor_data( $thank_you_id, ls_build_thank_you_data() );
	$results[] = [
		'label' => '✅ Página de Obrigado',
		'value' => get_permalink( $thank_you_id ),
		'id'    => $thank_you_id,
	];
}

// 3. Criar páginas legais (Política + Termos)
leadsnap_create_legal_pages();
$results[] = [
	'label' => '✅ Páginas Legais',
	'value' => get_permalink( get_page_by_path( 'politica-de-privacidade' ) ),
	'id'    => null,
];

// 4. Definir Front Page e Configurações Globais
if ( ! is_wp_error( $landing_id ) ) {
	update_option( 'show_on_front',  'page' );
	update_option( 'page_on_front',  $landing_id );
	$results[] = [
		'label' => '✅ Front Page configurada',
		'value' => home_url( '/' ),
		'id'    => null,
	];
}

// Configura links amigáveis e atualiza as regras de reescrita
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( true );
$results[] = [
	'label' => '✅ Permalinks amigáveis configurados (/%postname%/)',
	'value' => '',
	'id'    => null,
];

// Garante que a página de política de privacidade esteja publicada
$privacy_page = get_page_by_path( 'politica-de-privacidade' );
if ( $privacy_page ) {
	wp_update_post( [
		'ID'          => $privacy_page->ID,
		'post_status' => 'publish',
	] );
	$results[] = [
		'label' => '✅ Política de Privacidade publicada',
		'value' => get_permalink( $privacy_page->ID ),
		'id'    => $privacy_page->ID,
	];
}

// 5. Invalidar cache do Elementor (força regeneração de CSS)
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	$results[] = [ 'label' => '✅ Cache do Elementor limpo', 'value' => '', 'id' => null ];
}

// ──────────────────────────────────────────────────────────────────
// OUTPUT HTML
// ──────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>LeadSnap Setup</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', -apple-system, sans-serif; background: #0B1120; color: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 32px 16px; }
  .card { background: #111827; border: 1px solid rgba(255,255,255,.1); border-radius: 20px; padding: 48px; max-width: 640px; width: 100%; }
  h1 { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 800; margin-bottom: 8px; }
  .sub { color: #94A3B8; font-size: 16px; margin-bottom: 40px; line-height: 1.6; }
  .item { display: flex; align-items: flex-start; gap: 12px; padding: 16px 0; border-bottom: 1px solid rgba(255,255,255,.06); }
  .item:last-child { border-bottom: none; }
  .label { font-weight: 600; font-size: 16px; }
  .url { color: #FF5722; font-size: 14px; text-decoration: none; word-break: break-all; }
  .url:hover { text-decoration: underline; }
  .error { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.3); border-radius: 12px; padding: 16px; margin-top: 24px; color: #FCA5A5; }
  .actions { margin-top: 40px; display: flex; gap: 12px; flex-wrap: wrap; }
  .btn { display: inline-block; padding: 14px 28px; border-radius: 10px; font-weight: 700; font-size: 15px; text-decoration: none; transition: .2s; }
  .btn-primary { background: #FF5722; color: #fff; }
  .btn-primary:hover { background: #E64A19; }
  .btn-secondary { background: rgba(255,255,255,.08); color: #F8FAFC; border: 1px solid rgba(255,255,255,.12); }
  .btn-secondary:hover { background: rgba(255,255,255,.12); }
  .warning { background: rgba(255,193,7,.1); border: 1px solid rgba(255,193,7,.3); border-radius: 12px; padding: 16px; margin-top: 24px; color: #FDE68A; font-size: 14px; }
</style>
</head>
<body>
<div class="card">
  <h1>🚀 LeadSnap Setup</h1>
  <p class="sub">As páginas foram criadas e configuradas com sucesso. Confira abaixo:</p>

  <?php foreach ( $results as $r ) : ?>
  <div class="item">
    <div>
      <div class="label"><?php echo esc_html( $r['label'] ); ?></div>
      <?php if ( ! empty( $r['value'] ) ) : ?>
        <a class="url" href="<?php echo esc_url( $r['value'] ); ?>" target="_blank"><?php echo esc_html( $r['value'] ); ?></a>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if ( ! empty( $errors ) ) : ?>
  <div class="error">
    <strong>⚠ Erros encontrados:</strong><br>
    <?php foreach ( $errors as $e ) : ?>
      <p style="margin-top:8px"><?php echo esc_html( $e ); ?></p>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="warning">
    ⚠ <strong>Importante:</strong> Delete o arquivo <code>leadsnap-setup.php</code> da raiz do WordPress após o setup por segurança.
  </div>

  <div class="actions">
    <a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">Ver Landing Page →</a>
    <a class="btn btn-secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" target="_blank">Gerenciar Páginas</a>
    <a class="btn btn-secondary" href="<?php echo esc_url( get_permalink( $landing_id ) . '?elementor' ); ?>" target="_blank">Editar no Elementor</a>
  </div>
</div>
</body>
</html>
<?php

<?php
/**
 * LeadSnap CLI Setup — executa sem browser
 * Uso: php leadsnap-cli-setup.php
 *
 * Conecta diretamente ao DB e insere as páginas + metas do Elementor.
 */

// ── Bootstrap WordPress ───────────────────────────────────────────
define( 'ABSPATH', __DIR__ . '/' );

// Simular ambiente CLI para WordPress não redirecionar
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/leadsnap-cli-setup.php';

require_once __DIR__ . '/wp-load.php';

echo "\n=== LeadSnap CLI Setup ===\n\n";

// ── Helpers ───────────────────────────────────────────────────────

function ls_uid(): string {
	return substr( md5( uniqid( '', true ) ), 0, 8 );
}

function ls_get_or_create_page( string $title, string $slug, string $status = 'publish' ): int {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		echo "  [SKIP] Página '$slug' já existe (ID: {$existing->ID})\n";
		return $existing->ID;
	}

	$id = wp_insert_post( [
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_status'  => $status,
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	] );

	if ( is_wp_error( $id ) ) {
		echo "  [ERROR] Falha ao criar '$slug': " . $id->get_error_message() . "\n";
		return 0;
	}

	echo "  [OK] Página '$slug' criada (ID: $id)\n";
	return $id;
}

function ls_set_elementor_data( int $post_id, array $data ): void {
	$json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	update_post_meta( $post_id, '_elementor_data',          wp_slash( $json ) );
	update_post_meta( $post_id, '_elementor_edit_mode',     'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version',       '3.25.0' );
	update_post_meta( $post_id, '_elementor_page_settings', [
		'hide_title'  => 'yes',
		'page_layout' => 'elementor_canvas',
	] );
	update_post_meta( $post_id, '_wp_page_template',        'elementor_canvas' );
	delete_post_meta( $post_id, '_elementor_css' );
}

// ── LANDING PAGE DATA ─────────────────────────────────────────────

function ls_landing_data(): array {
	return [

		// HERO
		[
			'id'       => ls_uid(), 'elType' => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0B1120',
				'padding'               => [ 'top' => '100', 'right' => '24', 'bottom' => '100', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '60', 'right' => '16', 'bottom' => '60', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [ [
				'id' => ls_uid(), 'elType' => 'column',
				'settings' => [ '_column_size' => 100 ],
				'elements' => [

					// Badge
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '<div style="text-align:center;margin-bottom:28px">
<span style="display:inline-flex;align-items:center;gap:8px;background:#FFC107;color:#1A1A1A;padding:8px 20px;border-radius:999px;font-family:\'DM Sans\',sans-serif;font-size:13px;font-weight:700;letter-spacing:.05em;text-transform:uppercase">
<span style="width:8px;height:8px;background:#FF5722;border-radius:50%;display:inline-block"></span>
⚡ VAGAS LIMITADAS — Entre antes de esgotar
</span></div>',
					] ],

					// Headline
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
						'title'                      => 'Acesse as Melhores Ofertas<br><span style="color:#FF5722">Antes de Todo Mundo</span>',
						'header_size'                => 'h1',
						'align'                      => 'center',
						'title_color'                => '#F8FAFC',
						'typography_typography'      => 'custom',
						'typography_font_family'     => 'Syne',
						'typography_font_size'       => [ 'unit' => 'px', 'size' => 58 ],
						'typography_font_size_tablet'=> [ 'unit' => 'px', 'size' => 42 ],
						'typography_font_size_mobile'=> [ 'unit' => 'px', 'size' => 30 ],
						'typography_font_weight'     => '800',
						'typography_line_height'     => [ 'unit' => 'em', 'size' => 1.15 ],
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					// Subheadline
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'text-editor', 'settings' => [
						'editor' => '<div style="display:flex;justify-content:center;align-items:center;text-align:center;width:100%;"><p style="text-align:center;color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:19px;line-height:1.75;max-width:640px;margin:0 auto 32px">Entre no grupo exclusivo e receba cupons de <strong style="color:#F8FAFC">até 80% OFF</strong> diretamente no seu WhatsApp — sem custo algum.</p></div>',
					] ],

					// Countdown
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;margin-bottom:36px;width:100%"><p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:12px;letter-spacing:.1em;text-transform:uppercase;margin-bottom:12px">⏱ Oferta expira em:</p>' . do_shortcode( '[leadsnap_countdown evergreen="1" days="3"]' ) . '</div>',
					] ],

					// CTA Button
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'button', 'settings' => [
						'text'                         => 'QUERO ENTRAR AGORA →',
						'link'                         => [ 'url' => '#formulario', 'is_external' => '' ],
						'align'                        => 'center',
						'background_color'             => '#FF5722',
						'button_text_color'            => '#FFFFFF',
						'hover_color'                  => '#FFFFFF',
						'background_hover_color'       => '#E64A19',
						'border_radius'                => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => true ],
						'text_padding'                 => [ 'top' => '20', 'right' => '56', 'bottom' => '20', 'left' => '56', 'unit' => 'px', 'isLinked' => false ],
						'typography_typography'        => 'custom',
						'typography_font_family'       => 'Syne',
						'typography_font_size'         => [ 'unit' => 'px', 'size' => 18 ],
						'typography_font_weight'       => '700',
						'typography_letter_spacing'    => [ 'unit' => 'px', 'size' => 0.5 ],
						'css_classes'                  => 'leadsnap-cta-btn ls-pulse',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '20', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					// Social proof note
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '<div style="display:flex;justify-content:center;align-items:center;text-align:center;width:100%"><p style="color:#475569;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">🔒 Grátis · Sem spam · Cancele quando quiser</p></div>',
					] ],

				],
			] ],
		],

		// PROVA SOCIAL
		[
			'id'       => ls_uid(), 'elType' => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0F172A',
				'padding'               => [ 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [ [
				'id' => ls_uid(), 'elType' => 'column',
				'settings' => [ '_column_size' => 100 ],
				'elements' => [

					// Contador
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '<div style="text-align:center;margin-bottom:56px">' . do_shortcode('[leadsnap_counter number="12000" suffix="+" label="membros ativos no grupo"]') . '</div>',
					] ],

					// Seção título
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
						'title'                      => 'O que nossos membros dizem',
						'header_size'                => 'h2',
						'align'                      => 'center',
						'title_color'                => '#F8FAFC',
						'typography_typography'      => 'custom',
						'typography_font_family'     => 'Syne',
						'typography_font_size'       => [ 'unit' => 'px', 'size' => 36 ],
						'typography_font_size_mobile'=> [ 'unit' => 'px', 'size' => 24 ],
						'typography_font_weight'     => '700',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '40', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					// Depoimentos grid
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;max-width:1100px;margin:0 auto">
  <div style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:48px;height:48px;border-radius:50%;background:#FF5722;color:#fff;font-family:Syne,sans-serif;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">MS</div>
      <div><p style="color:#F8FAFC;font-family:\'DM Sans\',sans-serif;font-weight:700;margin:0;font-size:15px">Maria S.</p><p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">São Paulo, SP</p></div>
    </div>
    <p style="color:#FFC107;font-size:14px;letter-spacing:2px;margin-bottom:12px">★★★★★</p>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">"Economizei mais de R$ 400 no primeiro mês. Os cupons chegam antes de qualquer outro grupo!"</p>
  </div>
  <div style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:48px;height:48px;border-radius:50%;background:#3B82F6;color:#fff;font-family:Syne,sans-serif;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">RC</div>
      <div><p style="color:#F8FAFC;font-family:\'DM Sans\',sans-serif;font-weight:700;margin:0;font-size:15px">Roberto C.</p><p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">Belo Horizonte, MG</p></div>
    </div>
    <p style="color:#FFC107;font-size:14px;letter-spacing:2px;margin-bottom:12px">★★★★★</p>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">"Uso para comprar itens para minha loja. Descontos reais antes de esgotar. Vale muito!"</p>
  </div>
  <div style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:48px;height:48px;border-radius:50%;background:#22C55E;color:#fff;font-family:Syne,sans-serif;font-weight:700;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0">AL</div>
      <div><p style="color:#F8FAFC;font-family:\'DM Sans\',sans-serif;font-weight:700;margin:0;font-size:15px">Ana L.</p><p style="color:#64748B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">Curitiba, PR</p></div>
    </div>
    <p style="color:#FFC107;font-size:14px;letter-spacing:2px;margin-bottom:12px">★★★★★</p>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">"Indiquei para toda a família. Único grupo que não enche de spam — só cupons bons!"</p>
  </div>
</div>',
					] ],

				],
			] ],
		],

		// BENEFÍCIOS
		[
			'id'       => ls_uid(), 'elType' => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0B1120',
				'padding'               => [ 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [ [
				'id' => ls_uid(), 'elType' => 'column',
				'settings' => [ '_column_size' => 100 ],
				'elements' => [

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
						'title' => 'Por que entrar agora?',
						'header_size' => 'h2', 'align' => 'center', 'title_color' => '#F8FAFC',
						'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
						'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
						'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 24 ],
						'typography_font_weight' => '700',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;max-width:1100px;margin:0 auto 40px">
  <div style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;text-align:center">
    <div style="width:56px;height:56px;background:rgba(255,87,34,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#FF5722" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h3 style="color:#F8FAFC;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 12px">Cupons Exclusivos Primeiro</h3>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Receba antes de qualquer grupo público. Acesso prioritário antes do estoque esgotar.</p>
  </div>
  <div style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;text-align:center">
    <div style="width:56px;height:56px;background:rgba(59,130,246,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#3B82F6" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    </div>
    <h3 style="color:#F8FAFC;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 12px">Alertas em Tempo Real</h3>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Notificação direto no WhatsApp assim que a oferta é publicada.</p>
  </div>
  <div style="background:#111827;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;text-align:center">
    <div style="width:56px;height:56px;background:rgba(34,197,94,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#22C55E" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h3 style="color:#F8FAFC;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 12px">100% Gratuito</h3>
    <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Zero mensalidade, zero taxa. O grupo é e sempre será gratuito para todos.</p>
  </div>
</div>
<div style="max-width:800px;margin:0 auto;background:linear-gradient(135deg,rgba(255,87,34,.12),rgba(255,193,7,.08));border:1px solid rgba(255,87,34,.25);border-radius:16px;padding:28px 32px;text-align:center">
  <p style="color:#FFC107;font-family:Syne,sans-serif;font-weight:700;font-size:18px;margin:0 0 8px">⚡ Vagas do grupo são limitadas</p>
  <p style="color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:15px;line-height:1.7;margin:0">Para manter qualidade, fechamos inscrições periodicamente. Garanta sua vaga agora.</p>
</div>',
					] ],

				],
			] ],
		],

		// FORMULÁRIO
		[
			'id'       => ls_uid(), 'elType' => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0F172A',
				'padding'               => [ 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
				'custom_id'             => 'formulario',
			],
			'elements' => [ [
				'id' => ls_uid(), 'elType' => 'column',
				'settings' => [ '_column_size' => 100 ],
				'elements' => [

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
						'title' => '🎯 Garantir Minha Vaga Agora',
						'header_size' => 'h2', 'align' => 'center', 'title_color' => '#F8FAFC',
						'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
						'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
						'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 24 ],
						'typography_font_weight' => '700',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '12', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'text-editor', 'settings' => [
						'editor' => '<div style="display:flex;justify-content:center;align-items:center;text-align:center;width:100%;color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:17px;margin-bottom:40px">Preencha abaixo e receba o acesso em menos de 1 minuto</div>',
					] ],

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '[leadsnap_form]',
					] ],

				],
			] ],
		],

		// FAQ
		[
			'id'       => ls_uid(), 'elType' => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#0B1120',
				'padding'               => [ 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [ [
				'id' => ls_uid(), 'elType' => 'column',
				'settings' => [ '_column_size' => 100 ],
				'elements' => [

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
						'title' => 'Perguntas Frequentes',
						'header_size' => 'h2', 'align' => 'center', 'title_color' => '#F8FAFC',
						'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
						'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
						'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 24 ],
						'typography_font_weight' => '700',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'accordion', 'settings' => [
						'tabs' => [
							[ 'tab_title' => 'Como recebo as ofertas?',         'tab_content' => 'Após se cadastrar, você receberá um link para o grupo do WhatsApp. As ofertas são enviadas em tempo real, assim que selecionadas pela nossa equipe.' ],
							[ 'tab_title' => 'O grupo é realmente gratuito?',  'tab_content' => 'Sim, 100% gratuito. Não há mensalidade, taxa de entrada ou qualquer cobrança.' ],
							[ 'tab_title' => 'Posso sair quando quiser?',      'tab_content' => 'Claro! Você pode sair do grupo do WhatsApp a qualquer momento, sem necessidade de avisar.' ],
							[ 'tab_title' => 'Quantas ofertas recebo por dia?','tab_content' => 'Em média, de 5 a 10 cupons e ofertas selecionadas por dia. Priorizamos qualidade.' ],
							[ 'tab_title' => 'Meus dados estão seguros?',      'tab_content' => 'Sim. Seus dados são tratados conforme a LGPD (Lei 13.709/2018). Nunca vendemos ou compartilhamos suas informações.' ],
						],
						'title_color'                     => '#F8FAFC',
						'title_active_color'              => '#FF5722',
						'content_color'                   => '#94A3B8',
						'border_color'                    => 'rgba(255,255,255,.08)',
						'icon_color'                      => '#FF5722',
						'icon_active_color'               => '#FF5722',
						'typography_typography'           => 'custom',
						'typography_font_family'          => 'DM Sans',
						'typography_font_size'            => [ 'unit' => 'px', 'size' => 17 ],
						'typography_font_weight'          => '600',
						'content_typography_typography'   => 'custom',
						'content_typography_font_family'  => 'DM Sans',
						'content_typography_font_size'    => [ 'unit' => 'px', 'size' => 15 ],
						'_element_width'                  => 'initial',
						'_element_custom_width'           => [ 'unit' => 'px', 'size' => 800 ],
					] ],

				],
			] ],
		],

		// RODAPÉ + CTA FINAL
		[
			'id'       => ls_uid(), 'elType' => 'section',
			'settings' => [
				'background_background' => 'classic',
				'background_color'      => '#060D18',
				'padding'               => [ 'top' => '64', 'right' => '24', 'bottom' => '48', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
				'padding_mobile'        => [ 'top' => '40', 'right' => '16', 'bottom' => '32', 'left' => '16', 'unit' => 'px', 'isLinked' => false ],
				'layout'                => 'full_width',
			],
			'elements' => [ [
				'id' => ls_uid(), 'elType' => 'column',
				'settings' => [ '_column_size' => 100 ],
				'elements' => [

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
						'title' => 'Não perca mais essa chance',
						'header_size' => 'h2', 'align' => 'center', 'title_color' => '#F8FAFC',
						'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
						'typography_font_size' => [ 'unit' => 'px', 'size' => 30 ],
						'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 22 ],
						'typography_font_weight' => '700',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '24', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'button', 'settings' => [
						'text' => 'QUERO ENTRAR AGORA →',
						'link' => [ 'url' => '#formulario', 'is_external' => '' ],
						'align' => 'center',
						'background_color' => '#FF5722',
						'button_text_color' => '#FFFFFF',
						'background_hover_color' => '#E64A19',
						'border_radius' => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => true ],
						'text_padding' => [ 'top' => '18', 'right' => '48', 'bottom' => '18', 'left' => '48', 'unit' => 'px', 'isLinked' => false ],
						'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
						'typography_font_size' => [ 'unit' => 'px', 'size' => 17 ],
						'typography_font_weight' => '700',
						'css_classes' => 'leadsnap-cta-btn',
						'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '48', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
					] ],

					// Footer links
					[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
						'html' => '<div class="leadsnap-footer" style="text-align:center;border-top:1px solid rgba(255,255,255,.06);padding-top:32px">
<p style="color:#334155;font-family:\'DM Sans\',sans-serif;font-size:14px;margin-bottom:12px">
  <a href="/politica-de-privacidade" style="color:#475569;text-decoration:none;margin:0 12px">Política de Privacidade</a>
  · <a href="/termos-de-uso" style="color:#475569;text-decoration:none;margin:0 12px">Termos de Uso</a>
</p>
<p style="color:#1E293B;font-family:\'DM Sans\',sans-serif;font-size:13px;margin:0">© ' . gmdate('Y') . ' LeadSnap · Template de demonstração</p>
</div>',
						'css_classes' => 'leadsnap-footer-widget',
					] ],

				],
			] ],
		],

	];
}

// ── OBRIGADO PAGE DATA ─────────────────────────────────────────────

function ls_thankyou_data(): array {
	return [ [
		'id'       => ls_uid(), 'elType' => 'section',
		'settings' => [
			'background_background' => 'classic',
			'background_color'      => '#0B1120',
			'min_height'            => [ 'unit' => 'vh', 'size' => 100 ],
			'content_position'      => 'middle',
			'padding'               => [ 'top' => '80', 'right' => '24', 'bottom' => '80', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
			'layout'                => 'full_width',
			'css_classes'           => 'leadsnap-thankyou-section',
		],
		'elements' => [ [
			'id' => ls_uid(), 'elType' => 'column',
			'settings' => [ '_column_size' => 100 ],
			'elements' => [

				[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
					'html' => '<div style="text-align:center;margin-bottom:28px"><div style="width:80px;height:80px;background:rgba(34,197,94,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#22C55E" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div></div>',
				] ],

				[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'heading', 'settings' => [
					'title' => '🎉 Você está dentro!',
					'header_size' => 'h1', 'align' => 'center', 'title_color' => '#F8FAFC',
					'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
					'typography_font_size' => [ 'unit' => 'px', 'size' => 48 ],
					'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 30 ],
					'typography_font_weight' => '800',
					'_margin' => [ 'top' => '0', 'right' => '0', 'bottom' => '16', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
				] ],

				[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'text-editor', 'settings' => [
					'editor' => '<p style="text-align:center;color:#94A3B8;font-family:\'DM Sans\',sans-serif;font-size:18px;line-height:1.7;max-width:560px;margin:0 auto 40px">Verifique seu <strong style="color:#F8FAFC">WhatsApp e e-mail</strong> — você receberá o link de acesso em instantes. Enquanto isso, que tal indicar para um amigo?</p>',
				] ],

				[ 'id' => ls_uid(), 'elType' => 'widget', 'widgetType' => 'button', 'settings' => [
					'text' => '📲 Compartilhar com um amigo',
					'link' => [ 'url' => 'https://api.whatsapp.com/send?text=' . rawurlencode('Entrei no melhor grupo de ofertas! Entre você também: ' . home_url()), 'is_external' => 'on' ],
					'align' => 'center',
					'background_color' => '#22C55E',
					'button_text_color' => '#FFFFFF',
					'background_hover_color' => '#16A34A',
					'border_radius' => [ 'top' => '10', 'right' => '10', 'bottom' => '10', 'left' => '10', 'unit' => 'px', 'isLinked' => true ],
					'text_padding' => [ 'top' => '16', 'right' => '40', 'bottom' => '16', 'left' => '40', 'unit' => 'px', 'isLinked' => false ],
					'typography_typography' => 'custom', 'typography_font_family' => 'Syne',
					'typography_font_size' => [ 'unit' => 'px', 'size' => 17 ],
					'typography_font_weight' => '700',
				] ],

			],
		] ],
	] ];
}

// ──────────────────────────────────────────────────────────────────
// EXECUÇÃO
// ──────────────────────────────────────────────────────────────────

echo "1. Criando páginas...\n";

$landing_id   = ls_get_or_create_page( 'LeadSnap Demo', 'leadsnap-demo' );
$thankyou_id  = ls_get_or_create_page( 'Obrigado', 'obrigado' );
$privacy_id   = ls_get_or_create_page( 'Política de Privacidade', 'politica-de-privacidade' );
$terms_id     = ls_get_or_create_page( 'Termos de Uso', 'termos-de-uso' );

echo "\n2. Aplicando dados do Elementor...\n";

if ( $landing_id ) {
	ls_set_elementor_data( $landing_id, ls_landing_data() );
	echo "  [OK] Elementor data aplicado: Landing Page (ID $landing_id)\n";
}

if ( $thankyou_id ) {
	ls_set_elementor_data( $thankyou_id, ls_thankyou_data() );
	echo "  [OK] Elementor data aplicado: Obrigado (ID $thankyou_id)\n";
}

echo "\n3. Configurando Front Page e Configurações Globais...\n";
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $landing_id );
echo "  [OK] Front Page = ID $landing_id\n";

// Garante que a estrutura de links amigáveis esteja ativada
update_option( 'permalink_structure', '/%postname%/' );
flush_rewrite_rules( true );
echo "  [OK] Permalinks amigáveis configurados (/%postname%/)\n";

// Garante que as páginas de política de privacidade e termos de uso estejam publicadas
if ( $privacy_id ) {
	wp_update_post( [
		'ID'          => $privacy_id,
		'post_status' => 'publish',
	] );
	echo "  [OK] Política de Privacidade publicada.\n";
}
if ( $terms_id ) {
	wp_update_post( [
		'ID'          => $terms_id,
		'post_status' => 'publish',
	] );
	echo "  [OK] Termos de Uso publicado.\n";
}

echo "\n4. Limpando cache do Elementor...\n";
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	echo "  [OK] Cache limpo.\n";
} else {
	// Forçar limpeza manual via option
	delete_option( 'elementor_css_print_method' );
	echo "  [OK] Elementor não carregado via CLI — cache invalidado via DB.\n";
}

echo "\n5. Resultado:\n";
echo "  Landing Page: " . get_permalink( $landing_id ) . "\n";
echo "  Obrigado:     " . get_permalink( $thankyou_id ) . "\n";
echo "  Privacidade:  " . get_permalink( $privacy_id ) . "\n";
echo "  Termos:       " . get_permalink( $terms_id ) . "\n";

echo "\n=== SETUP CONCLUÍDO ✅ ===\n";
echo "Acesse: http://localhost:8080\n\n";

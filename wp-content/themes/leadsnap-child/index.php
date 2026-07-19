<?php
/**
 * LeadSnap Child Theme fallback entry point.
 * Forward rendering to the parent (Hello Elementor) index.php.
 *
 * @package LeadSnap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require get_template_directory() . '/index.php';

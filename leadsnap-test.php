<?php
require_once __DIR__ . '/wp-load.php';

global $wp_filter;

$action = 'elementor/page_templates/canvas/before_content';
echo "=== Hooks on $action ===\n";
if (isset($wp_filter[$action])) {
    foreach ($wp_filter[$action]->callbacks as $priority => $callbacks) {
        echo "Priority $priority:\n";
        foreach ($callbacks as $id => $cb) {
            echo "  - $id\n";
        }
    }
} else {
    echo "No hooks registered!\n";
}

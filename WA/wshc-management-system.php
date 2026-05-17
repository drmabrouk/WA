<?php
/**
 * Plugin Name: WSHC Management System
 * Description: Professional enterprise-level WordPress plugin for the World Sports Health Council.
 * Version: 1.0.0
 * Author: WSHC
 * Text Domain: wshc-ms
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WSHC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WSHC_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once __DIR__ . '/vendor/autoload.php';

use WSHC\Core\Plugin;

function wshc_ms_run() {
    Plugin::get_instance();
}

register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);

add_action('plugins_loaded', 'wshc_ms_run');

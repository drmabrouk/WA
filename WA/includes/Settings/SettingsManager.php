<?php

namespace WSHC\Settings;

/**
 * Manage system-wide settings and data operations.
 */
class SettingsManager {
    /**
     * Initialize settings hooks.
     */
    public function init() {
        add_action('wp_ajax_wshc_export_data', [$this, 'export_system_data']);
        add_action('wp_ajax_wshc_import_data', [$this, 'import_system_data']);
    }

    /**
     * Handle system data export.
     */
    public function export_system_data() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_wshc_system')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        // Logic for exporting system data (e.g., activity logs, user meta)
        $data = [
            'exported_at' => current_time('mysql'),
            'system' => 'WSHC Management System',
            // Placeholder for real data
        ];

        wp_send_json_success([
            'message' => 'System data exported successfully.',
            'data'    => base64_encode(json_encode($data))
        ]);
    }

    /**
     * Handle system data import.
     */
    public function import_system_data() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_wshc_system')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        // Logic for importing system data
        wp_send_json_success(['message' => 'System data imported successfully.']);
    }
}

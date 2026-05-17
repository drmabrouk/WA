<?php

namespace WSHC\Dashboard;

/**
 * Manage the system dashboard.
 */
class DashboardManager {
    /**
     * Initialize dashboard hooks.
     */
    public function init() {
        add_shortcode('wshc_dashboard', [$this, 'render_dashboard']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_dashboard_assets']);
    }

    /**
     * Enqueue dashboard assets.
     */
    public function enqueue_dashboard_assets() {
        if (is_page('wshc-dashboard')) {
            wp_enqueue_style('wshc-style', WSHC_PLUGIN_URL . 'assets/css/style.css', [], '1.0.0');
            wp_enqueue_style('wshc-dashboard-style', WSHC_PLUGIN_URL . 'assets/css/dashboard.css', [], '1.0.0');
            wp_enqueue_script('wshc-dashboard-js', WSHC_PLUGIN_URL . 'assets/js/dashboard.js', ['jquery'], '1.0.0', true);
            wp_localize_script('wshc-dashboard-js', 'wshc_dashboard_obj', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('wshc_dashboard_nonce'),
            ]);
        }
    }

    /**
     * Render the dashboard layout.
     */
    public function render_dashboard() {
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/wshc-login'));
            exit;
        }

        ob_start();
        $this->load_template('dashboard/layout');
        return ob_get_clean();
    }

    /**
     * Load a dashboard template.
     */
    public function load_template($name, $args = []) {
        extract($args);
        $path = WSHC_PLUGIN_DIR . 'templates/' . $name . '.php';
        if (file_exists($path)) {
            include $path;
        }
    }
}

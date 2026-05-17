<?php

namespace WSHC\UserManagement;

/**
 * Handle User CRUD operations.
 */
class UserRegistry {
    /**
     * Initialize hooks.
     */
    public function init() {
        add_action('wp_ajax_wshc_list_users', [$this, 'list_users']);
        add_action('wp_ajax_wshc_save_user', [$this, 'save_user']);
        add_action('wp_ajax_wshc_delete_user', [$this, 'delete_user']);
        add_action('wp_ajax_wshc_toggle_user_status', [$this, 'toggle_user_status']);
    }

    /**
     * List users with search, filter, and pagination.
     */
    public function list_users() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_wshc_users')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $role = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        $args = [
            'number' => 10,
            'paged'  => $paged,
            'search' => '*' . $search . '*',
            'role'   => $role,
        ];

        if ($status === 'suspended') {
            $args['meta_query'] = [
                [
                    'key'     => 'wshc_suspended',
                    'value'   => '1',
                    'compare' => '='
                ]
            ];
        } elseif ($status === 'active') {
            $args['meta_query'] = [
                [
                    'key'     => 'wshc_suspended',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }

        $user_query = new \WP_User_Query($args);
        $users = $user_query->get_results();
        $total_users = $user_query->get_total();

        ob_start();
        include WSHC_PLUGIN_DIR . 'templates/dashboard/users-list-table.php';
        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'total' => $total_users,
            'pages' => ceil($total_users / 10)
        ]);
    }

    /**
     * Create or update a user.
     */
    public function save_user() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_wshc_users')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize_text_field($_POST['role']);

        $user_data = [
            'user_login' => $username,
            'user_email' => $email,
            'role'       => $role,
        ];

        if ($user_id) {
            $user_data['ID'] = $user_id;
            if (!empty($password)) {
                $user_data['user_pass'] = $password;
            }
            $result = wp_update_user($user_data);
        } else {
            $user_data['user_pass'] = $password;
            $result = wp_insert_user($user_data);
        }

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        ActivityLogger::log(get_current_user_id(), $user_id ? 'user_update' : 'user_create', "Affected user ID: $result");

        wp_send_json_success(['message' => 'User saved successfully.']);
    }

    /**
     * Delete a user.
     */
    public function delete_user() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_wshc_users')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $user_id = intval($_POST['user_id']);
        if (get_current_user_id() === $user_id) {
            wp_send_json_error(['message' => 'You cannot delete yourself.']);
        }

        if (wp_delete_user($user_id)) {
            ActivityLogger::log(get_current_user_id(), 'user_delete', "Deleted user ID: $user_id");
            wp_send_json_success(['message' => 'User deleted successfully.']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete user.']);
        }
    }

    /**
     * Toggle user suspension (using user meta).
     */
    public function toggle_user_status() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_wshc_users')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $user_id = intval($_POST['user_id']);
        $status = get_user_meta($user_id, 'wshc_suspended', true);
        
        if ($status) {
            delete_user_meta($user_id, 'wshc_suspended');
            $message = 'User reactivated.';
            ActivityLogger::log(get_current_user_id(), 'user_reactivate', "Reactivated user ID: $user_id");
        } else {
            update_user_meta($user_id, 'wshc_suspended', 1);
            $message = 'User suspended.';
            ActivityLogger::log(get_current_user_id(), 'user_suspend', "Suspended user ID: $user_id");
        }

        wp_send_json_success(['message' => $message]);
    }
}

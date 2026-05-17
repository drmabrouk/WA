<?php

namespace WSHC\UserManagement;

/**
 * Log user activities.
 */
class ActivityLogger {
    /**
     * Log an action.
     *
     * @param int    $user_id
     * @param string $action
     * @param string|array $details
     */
    public static function log($user_id, $action, $details = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'wshc_activity_logs';

        if (is_array($details)) {
            $details = json_encode($details);
        }

        $wpdb->insert($table, [
            'user_id'    => $user_id,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }

    /**
     * Get logs for a user or system.
     */
    public static function get_logs($user_id = null, $limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'wshc_activity_logs';
        
        $query = "SELECT * FROM $table";
        $params = [];

        if ($user_id) {
            $query .= " WHERE user_id = %d";
            $params[] = $user_id;
        }

        $query .= " ORDER BY created_at DESC LIMIT %d";
        $params[] = $limit;

        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
}

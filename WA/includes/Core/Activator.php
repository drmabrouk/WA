<?php

namespace WSHC\Core;

/**
 * Logic to run during plugin activation.
 */
class Activator {
    /**
     * Activate the plugin.
     */
    public static function activate() {
        self::create_tables();
        self::register_roles();
        self::create_pages();
    }

    /**
     * Create custom database tables.
     */
    private static function create_tables() {
        \WSHC\Database\Schema::create_tables();
    }

    /**
     * Register custom user roles and permissions.
     */
    private static function register_roles() {
        add_role('wshc_administrator', 'WSHC Administrator', [
            'read' => true,
            'manage_wshc_system' => true,
            'manage_wshc_users' => true,
        ]);

        add_role('wshc_staff', 'WSHC Staff', [
            'read' => true,
            'manage_wshc_users' => true,
        ]);

        add_role('wshc_member', 'WSHC Member', [
            'read' => true,
        ]);
    }

    /**
     * Automatically generate required WordPress pages.
     */
    private static function create_pages() {
        $pages = [
            'login' => [
                'title'   => 'Login',
                'content' => '[wshc_login_form]',
            ],
            'id' => [
                'title'   => 'Dashboard',
                'content' => '[wshc_dashboard]',
            ],
        ];

        foreach ($pages as $slug => $page_data) {
            if (!get_page_by_path($slug)) {
                wp_insert_post([
                    'post_title'   => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $slug,
                ]);
            }
        }
    }
}

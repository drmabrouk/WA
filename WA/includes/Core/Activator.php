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
        // Hierarchy defined by user: Subscriber -> Member -> Research Member -> Practitioner Member
        // -> Fellowship Member -> Scientific Reviewer -> Programs Manager -> Regional Coordinator
        // -> Secretary-General -> Administrator

        $roles = [
            'wshc_member' => [
                'display_name' => 'Member',
                'caps'         => ['read' => true]
            ],
            'wshc_research_member' => [
                'display_name' => 'Research Member',
                'caps'         => ['read' => true]
            ],
            'wshc_practitioner_member' => [
                'display_name' => 'Practitioner Member',
                'caps'         => ['read' => true]
            ],
            'wshc_fellowship_member' => [
                'display_name' => 'Fellowship Member',
                'caps'         => ['read' => true]
            ],
            'wshc_scientific_reviewer' => [
                'display_name' => 'Scientific Reviewer',
                'caps'         => ['read' => true]
            ],
            'wshc_programs_manager' => [
                'display_name' => 'Programs Manager',
                'caps'         => ['read' => true, 'manage_wshc_users' => true]
            ],
            'wshc_regional_coordinator' => [
                'display_name' => 'Regional Coordinator',
                'caps'         => ['read' => true, 'manage_wshc_users' => true]
            ],
            'wshc_secretary_general' => [
                'display_name' => 'Secretary-General',
                'caps'         => ['read' => true, 'manage_wshc_users' => true, 'manage_wshc_system' => true]
            ]
        ];

        foreach ($roles as $role_key => $data) {
            add_role($role_key, $data['display_name'], $data['caps']);
        }

        // Administrator is core WP role, but we ensure our custom caps are there
        $admin = get_role('administrator');
        if ($admin) {
            $admin->add_cap('manage_wshc_system');
            $admin->add_cap('manage_wshc_users');
        }
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

<?php
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role_label = !empty($roles) ? ucwords(str_replace(['_', 'wshc'], [' ', 'WSHC'], $roles[0])) : 'User';
$base_url = home_url('/id');
?>
<div class="wshc-dashboard-wrapper">
    <!-- Top Navbar -->
    <nav class="wshc-top-nav">
        <div class="nav-left">
            <button id="sidebar-toggle" class="sidebar-btn"><span class="dashicons dashicons-menu"></span></button>
            <span class="system-title">Management System</span>
        </div>
        <div class="nav-right">
            <div class="user-profile-stack">
                <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
                <span class="role-capsule rank-capsule"><?php echo esc_html($role_label); ?></span>
            </div>
            <a href="<?php echo wp_logout_url(home_url('/wshc-login')); ?>" class="logout-icon-link" title="Logout">
                <span class="dashicons dashicons-exit"></span>
            </a>
        </div>
    </nav>

    <div class="dashboard-body">
        <!-- Sidebar -->
        <aside class="wshc-sidebar" id="wshc-sidebar">
            <ul class="nav-menu">
                <li>
                    <a href="<?php echo esc_url(add_query_arg('section', 'dashboard-overview', $base_url)); ?>"
                       class="nav-link <?php echo $current_section === 'dashboard-overview' ? 'active' : ''; ?>">
                        <span class="nav-icon dashicons dashicons-dashboard"></span> Dashboard Overview
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(add_query_arg('section', 'user-management', $base_url)); ?>"
                       class="nav-link <?php echo $current_section === 'user-management' ? 'active' : ''; ?>">
                        <span class="nav-icon dashicons dashicons-groups"></span> User Management
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(add_query_arg('section', 'settings-system', $base_url)); ?>"
                       class="nav-link <?php echo $current_section === 'settings-system' ? 'active' : ''; ?>">
                        <span class="nav-icon dashicons dashicons-admin-settings"></span> Settings
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="wshc-content" id="wshc-main-content">
            <!-- Dashboard Overview Section -->
            <div id="section-dashboard-overview" class="dashboard-section <?php echo $current_section === 'dashboard-overview' ? '' : 'hidden'; ?>">
                <h1 class="section-title">DASHBOARD OVERVIEW</h1>

                <div class="stats-grid">
                    <div class="stat-card users">
                        <div class="stat-icon dashicons dashicons-groups"></div>
                        <div class="stat-info">
                            <span class="stat-label">Total Users</span>
                            <span class="stat-value"><?php echo number_format($stats['total_users']); ?></span>
                        </div>
                    </div>
                    <div class="stat-card active">
                        <div class="stat-icon dashicons dashicons-yes-alt"></div>
                        <div class="stat-info">
                            <span class="stat-label">Active Accounts</span>
                            <span class="stat-value"><?php echo number_format($stats['active_users']); ?></span>
                        </div>
                    </div>
                    <div class="stat-card suspended">
                        <div class="stat-icon dashicons dashicons-dismiss"></div>
                        <div class="stat-info">
                            <span class="stat-label">Suspended</span>
                            <span class="stat-value"><?php echo number_format($stats['suspended_users']); ?></span>
                        </div>
                    </div>
                    <div class="stat-card admins">
                        <div class="stat-icon dashicons dashicons-shield"></div>
                        <div class="stat-info">
                            <span class="stat-label">Administrators</span>
                            <span class="stat-value"><?php
                                $admins = count_users();
                                echo number_format($admins['avail_roles']['wshc_administrator'] ?? 0);
                            ?></span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-secondary-grid">
                    <div class="content-panel">
                        <h2 class="section-title">RECENT SYSTEM ACTIVITIES</h2>
                        <table class="wshc-table compact">
                            <thead>
                                <tr>
                                    <th>Admin</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_logs'] as $log) :
                                    $admin = get_userdata($log->user_id);
                                    $action_label = ucwords(str_replace('_', ' ', $log->action));
                                ?>
                                    <tr>
                                        <td><?php echo $admin ? esc_html($admin->display_name) : 'System'; ?></td>
                                        <td><span class="action-tag <?php echo esc_attr($log->action); ?>"><?php echo esc_html($action_label); ?></span></td>
                                        <td><?php echo esc_html($log->details); ?></td>
                                        <td><?php echo human_time_diff(strtotime($log->created_at), current_time('timestamp')); ?> ago</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div id="section-user-management" class="dashboard-section <?php echo $current_section === 'user-management' ? '' : 'hidden'; ?>">
                <h1 class="section-title">SYSTEM USERS MANAGEMENT</h1>
                <div id="user-management-container">
                    <!-- User list will be loaded here -->
                </div>
            </div>

            <!-- Unified Settings Section -->
            <div id="section-settings-system" class="dashboard-section <?php echo $current_section === 'settings-system' ? '' : 'hidden'; ?>">
                <h1 class="section-title">SYSTEM SETTINGS</h1>

                <div class="settings-tabs">
                    <button class="settings-tab active" data-tab="design-settings">Design Settings</button>
                    <button class="settings-tab" data-tab="system-data">System Data</button>
                </div>

                <div class="settings-tab-content">
                    <div id="tab-design-settings" class="settings-pane active">
                        <div class="content-panel">
                            <h3>Design Configuration</h3>
                            <p>Configure the visual appearance and branding of the management system.</p>
                        </div>
                    </div>
                    <div id="tab-system-data" class="settings-pane hidden">
                        <div class="content-panel">
                            <h3>System Data Management</h3>
                            <p>Manage and export system-level data, backups, and logs.</p>
                            <div class="settings-actions" style="margin-top: 20px; display: flex; gap: 15px;">
                                <button id="export-data-btn" class="wshc-auth-btn" style="width: auto;">Export System Data</button>
                                <button id="import-data-btn" class="wshc-auth-btn" style="width: auto; background: #666;">Import Data Package</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

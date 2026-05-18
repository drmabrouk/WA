<?php
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role_label = !empty($roles) ? ucwords(str_replace(['_', 'wshc'], [' ', 'WSHC'], $roles[0])) : 'User';
?>
<div class="wshc-dashboard-wrapper">
    <!-- Top Navbar -->
    <nav class="wshc-top-nav">
        <div class="nav-left">
            <button id="sidebar-toggle" class="sidebar-btn">☰</button>
            <span class="system-title">WSHC Management System</span>
        </div>
        <div class="nav-right">
            <span class="user-name"><?php echo esc_html($current_user->display_name); ?></span>
            <span class="role-capsule"><?php echo esc_html($role_label); ?></span>
            <a href="<?php echo wp_logout_url(home_url('/wshc-login')); ?>" class="logout-link">Logout</a>
        </div>
    </nav>

    <div class="dashboard-body">
        <!-- Sidebar -->
        <aside class="wshc-sidebar" id="wshc-sidebar">
            <ul>
                <li><a href="#" class="nav-link active" data-section="dashboard-overview"><span class="nav-icon">📊</span> Dashboard Overview</a></li>
                <li><a href="#" class="nav-link" data-section="user-management"><span class="nav-icon">👥</span> User Management</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="wshc-content" id="wshc-main-content">
            <!-- Dashboard Overview Section -->
            <div id="section-dashboard-overview" class="dashboard-section">
                <h1 class="section-title">DASHBOARD OVERVIEW</h1>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-info">
                            <span class="stat-label">Total Users</span>
                            <span class="stat-value"><?php echo number_format($stats['total_users']); ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-info">
                            <span class="stat-label">Active Accounts</span>
                            <span class="stat-value"><?php echo number_format($stats['active_users']); ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🚫</div>
                        <div class="stat-info">
                            <span class="stat-label">Suspended</span>
                            <span class="stat-value"><?php echo number_format($stats['suspended_users']); ?></span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🔑</div>
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
            <div id="section-user-management" class="dashboard-section hidden">
                <h1 class="section-title">SYSTEM USERS MANAGEMENT</h1>
                <div id="user-management-container">
                    <!-- User list will be loaded here -->
                </div>
            </div>
        </main>
    </div>
</div>

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
                <li><a href="#" class="nav-link active" data-section="dashboard-overview">Dashboard</a></li>
                <li><a href="#" class="nav-link" data-section="user-management">System Users Management</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="wshc-content" id="wshc-main-content">
            <div id="section-dashboard-overview" class="dashboard-section">
                <h1>Dashboard Overview</h1>
                <p>Welcome to the WSHC Management System.</p>
            </div>
            <div id="section-user-management" class="dashboard-section hidden">
                <h1>User Management</h1>
                <div id="user-management-container">
                    <!-- User list will be loaded here -->
                </div>
            </div>
        </main>
    </div>
</div>

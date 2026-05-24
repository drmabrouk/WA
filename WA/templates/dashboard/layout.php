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
            <a href="<?php echo wp_logout_url(home_url('/login')); ?>" class="logout-icon-link" title="Logout">
                <span class="dashicons dashicons-exit"></span>
            </a>
        </div>
    </nav>

    <div class="dashboard-body">
        <!-- Sidebar -->
        <aside class="wshc-sidebar" id="wshc-sidebar">
            <ul class="nav-menu">
                <?php if (current_user_can('administrator')) : ?>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('section', 'dashboard-overview', $base_url)); ?>"
                           class="nav-link <?php echo $current_section === 'dashboard-overview' ? 'active' : ''; ?>">
                            <span class="nav-icon dashicons dashicons-dashboard"></span> Dashboard Overview
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('section', 'membership-apps', $base_url)); ?>"
                           class="nav-link <?php echo $current_section === 'membership-apps' ? 'active' : ''; ?>">
                            <span class="nav-icon dashicons dashicons-email-alt"></span> Applications
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('section', 'membership-dir', $base_url)); ?>"
                           class="nav-link <?php echo $current_section === 'membership-dir' ? 'active' : ''; ?>">
                            <span class="nav-icon dashicons dashicons-businessperson"></span> Memberships
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
                <?php else : ?>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('section', 'my-account', $base_url)); ?>"
                           class="nav-link <?php echo $current_section === 'my-account' ? 'active' : ''; ?>">
                            <span class="nav-icon dashicons dashicons-admin-users"></span> My Account
                        </a>
                    </li>
                    <?php if (current_user_can('wshc_visitor')) : ?>
                        <li>
                            <a href="<?php echo esc_url(add_query_arg('section', 'info-apply', $base_url)); ?>"
                               class="nav-link <?php echo $current_section === 'info-apply' ? 'active' : ''; ?>">
                                <span class="nav-icon dashicons dashicons-info"></span> Information
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="wshc-content" id="wshc-main-content">
            <?php if (current_user_can('administrator')) : ?>
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
                                    echo number_format($admins['avail_roles']['administrator'] ?? 0);
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
                                        <th>Actor</th>
                                        <th>Event</th>
                                        <th>Information</th>
                                        <th>Date</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['recent_logs'] as $log) :
                                        $admin = get_userdata($log->user_id);
                                        $action_label = ucwords(str_replace('_', ' ', $log->action));
                                    ?>
                                        <tr>
                                            <td><strong><?php echo $admin ? esc_html($admin->display_name) : 'System'; ?></strong></td>
                                            <td><span class="action-tag <?php echo esc_attr($log->action); ?>"><?php echo esc_html($action_label); ?></span></td>
                                            <td style="font-size: 12px; color: #666;"><?php echo esc_html($log->details); ?></td>
                                            <td><?php echo date('M d, H:i', strtotime($log->created_at)); ?></td>
                                            <td style="text-align: right;">
                                                <?php if ($log->action !== 'rollback') : ?>
                                                    <button class="revert-btn action-btn" data-id="<?php echo $log->id; ?>" title="Rollback Action">
                                                        <span class="dashicons dashicons-undo"></span>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($stats['recent_logs'])) : ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 30px; color: #999;">No recent activities found in the last 48 hours.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Management Section -->
                <div id="section-user-management" class="dashboard-section <?php echo $current_section === 'user-management' ? '' : 'hidden'; ?>">
                    <div id="user-management-container">
                        <!-- User list will be loaded here -->
                    </div>
                </div>

                <!-- Unified Settings Section -->
                <div id="section-settings-system" class="dashboard-section <?php echo $current_section === 'settings-system' ? '' : 'hidden'; ?>">
                    <h1 class="section-title">SYSTEM SETTINGS</h1>

                    <div class="settings-tabs">
                        <button class="settings-tab active" data-tab="design-settings">Design Settings</button>
                        <button class="settings-tab" data-tab="auth-config">Auth Configuration</button>
                        <button class="settings-tab" data-tab="system-data">System Data</button>
                    </div>

                    <div class="settings-tab-content">
                        <div id="tab-design-settings" class="settings-pane active">
                            <div class="content-panel">
                                <h3>Design Configuration</h3>
                                <p>Configure the visual appearance and branding of the management system.</p>
                            </div>
                        </div>
                        <div id="tab-auth-config" class="settings-pane hidden">
                            <div class="content-panel">
                                <h3>Authentication Control Panel</h3>
                                <div class="settings-row" style="margin-bottom: 25px;">
                                    <label class="switch-label">Enable Registration System</label>
                                    <input type="checkbox" id="enable-reg" checked>
                                </div>
                                <div class="settings-row" style="margin-bottom: 25px;">
                                    <label class="switch-label">Enable Login System</label>
                                    <input type="checkbox" id="enable-login" checked>
                                </div>
                                <div class="wshc-auth-form-group">
                                    <label>OTP Confirmation Email Message</label>
                                    <textarea id="otp-message" style="height: 120px;" placeholder="Your OTP code is: {otp}"></textarea>
                                </div>
                                <div class="wshc-auth-form-group">
                                    <label>Welcome Email Message</label>
                                    <textarea id="welcome-message" style="height: 120px;" placeholder="Welcome to our system!"></textarea>
                                </div>
                                <button id="save-auth-settings" class="wshc-auth-btn" style="width: auto;">Save Configurations</button>
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

                <!-- Membership Applications Section -->
                <div id="section-membership-apps" class="dashboard-section <?php echo $current_section === 'membership-apps' ? '' : 'hidden'; ?>">
                    <h1 class="section-title">MEMBERSHIP APPLICATIONS</h1>
                    <div id="membership-apps-container"></div>
                </div>

                <!-- Membership Directory Section -->
                <div id="section-membership-dir" class="dashboard-section <?php echo $current_section === 'membership-dir' ? '' : 'hidden'; ?>">
                    <h1 class="section-title">MEMBERSHIP DIRECTORY</h1>
                    <div id="membership-dir-container"></div>
                </div>
            <?php endif; ?>

            <!-- Visitor Information & Apply Section -->
            <div id="section-info-apply" class="dashboard-section <?php echo $current_section === 'info-apply' ? '' : 'hidden'; ?>">
                <h1 class="section-title">MEMBERSHIP APPLICATION WIZARD</h1>
                <div class="content-panel">
                    <div class="wizard-progress">
                        <div class="wizard-step active" data-step="1">1. Personal</div>
                        <div class="wizard-step" data-step="2">2. Academic</div>
                        <div class="wizard-step" data-step="3">3. Professional</div>
                        <div class="wizard-step" data-step="4">4. Credentials</div>
                        <div class="wizard-step" data-step="5">5. Research</div>
                    </div>

                    <form id="membership-application-wizard" class="wshc-wizard-form" enctype="multipart/form-data">
                        <!-- Stage 1: Personal Info -->
                        <div class="wizard-pane active" id="pane-1">
                            <h3>Personal Information</h3>
                            <div class="wshc-auth-form-group">
                                <label>Full Legal Name</label>
                                <input type="text" name="full_name" value="<?php echo esc_attr($current_user->display_name); ?>" required placeholder="Triple or quadruple official name">
                            </div>
                            <div class="wshc-auth-grid">
                                <div class="wshc-auth-form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="dob" required>
                                </div>
                                <div class="wshc-auth-form-group">
                                    <label>Gender</label>
                                    <select name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Nationality</label>
                                <select name="nationality" required>
                                    <option value="">Select Country</option>
                                    <option value="United States">United States</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Saudi Arabia">Saudi Arabia</option>
                                    <option value="Egypt">Egypt</option>
                                    <option value="UAE">United Arab Emirates</option>
                                    <!-- Add more as needed -->
                                </select>
                            </div>
                            <div class="wshc-auth-grid">
                                <div class="wshc-auth-form-group">
                                    <label>Email Address</label>
                                    <input type="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                                </div>
                                <div class="wshc-auth-form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="phone" placeholder="+123 456789" required>
                                </div>
                            </div>
                        </div>

                        <!-- Stage 2: Academic Background -->
                        <div class="wizard-pane hidden" id="pane-2">
                            <h3>Academic Background</h3>
                            <div class="wshc-auth-grid">
                                <div class="wshc-auth-form-group">
                                    <label>Highest Degree</label>
                                    <select name="degree" required>
                                        <option value="Ph.D.">Ph.D.</option>
                                        <option value="Master's">Master's</option>
                                        <option value="Bachelor's">Bachelor's</option>
                                        <option value="Higher Diploma">Higher Diploma</option>
                                    </select>
                                </div>
                                <div class="wshc-auth-form-group">
                                    <label>Graduation Year</label>
                                    <input type="number" name="grad_year" min="1950" max="2025" required>
                                </div>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Major/Field of Study</label>
                                <input type="text" name="major" placeholder="e.g. Sports Medicine, Kinesiology" required>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>University/Institution</label>
                                <input type="text" name="institution" required>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Degree Certificate (Upload Digital Copy)</label>
                                <input type="file" name="cert_file" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>

                        <!-- Stage 3: Professional Status -->
                        <div class="wizard-pane hidden" id="pane-3">
                            <h3>Professional Status</h3>
                            <div class="wshc-auth-grid">
                                <div class="wshc-auth-form-group">
                                    <label>Current Job Title</label>
                                    <input type="text" name="job_title" placeholder="e.g. University Lecturer" required>
                                </div>
                                <div class="wshc-auth-form-group">
                                    <label>Years of Experience</label>
                                    <input type="number" name="experience" min="0" required>
                                </div>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Current Employer/Organization</label>
                                <input type="text" name="employer" required>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>CV / Comprehensive Resume (Upload)</label>
                                <input type="file" name="cv_file" accept=".pdf,.doc,.docx">
                            </div>
                        </div>

                        <!-- Stage 4: Licensing & Credentials -->
                        <div class="wizard-pane hidden" id="pane-4">
                            <h3>Licensing & Credentials</h3>
                            <div class="wshc-auth-form-group">
                                <label>Professional License Number</label>
                                <input type="text" name="license_number" placeholder="Regulatory or ministry code">
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Specialized Certifications</label>
                                <textarea name="specialized_certs" placeholder="e.g. CPR, FIFA certifications, NSCA"></textarea>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Other Memberships</label>
                                <textarea name="other_memberships" placeholder="List active associations or syndicates"></textarea>
                            </div>
                        </div>

                        <!-- Stage 5: Research & Finance -->
                        <div class="wizard-pane hidden" id="pane-5">
                            <h3>Research & Finance</h3>
                            <div class="wshc-auth-form-group">
                                <label>Research & Publications</label>
                                <textarea name="research_publications" placeholder="Links and titles of scientific journal articles"></textarea>
                            </div>
                            <div class="wshc-auth-form-group">
                                <label>Core Areas of Interest</label>
                                <select name="interests[]" multiple style="height: 100px;">
                                    <option value="Athletic Injuries">Athletic Injuries</option>
                                    <option value="Sports Nutrition">Sports Nutrition</option>
                                    <option value="Exercise Physiology">Exercise Physiology</option>
                                    <option value="Sports Psychology">Sports Psychology</option>
                                </select>
                            </div>
                            <div class="payment-box" style="padding: 20px; background: #f9f9f9; border-radius: 8px; margin-top: 20px;">
                                <h4>Annual Subscription Payment</h4>
                                <p style="font-size: 13px; color: #666;">Secure electronic payment integration placeholder.</p>
                                <div class="wshc-auth-form-group">
                                    <input type="text" placeholder="Card Number (Placeholder)">
                                </div>
                            </div>
                        </div>

                        <div class="wizard-actions" style="margin-top: 30px; display: flex; gap: 15px;">
                            <button type="button" id="prev-step" class="wshc-auth-btn hidden" style="background: #666;">Previous</button>
                            <button type="button" id="next-step" class="wshc-auth-btn">Next Step</button>
                            <button type="submit" id="submit-wizard" class="wshc-auth-btn hidden">Complete Application</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- My Account Section -->
            <div id="section-my-account" class="dashboard-section <?php echo ($current_section === 'my-account' || (empty($current_section) && !current_user_can('administrator'))) ? '' : 'hidden'; ?>">
                <h1 class="section-title">MY ACCOUNT</h1>
                <div class="content-panel">
                    <div class="user-profile-summary">
                        <h3>Welcome back, <?php echo esc_html($current_user->display_name); ?></h3>
                        <p>Role: <span class="role-capsule rank-capsule"><?php echo esc_html($role_label); ?></span></p>

                        <?php
                        $mid = get_user_meta($current_user->ID, 'wshc_membership_id', true);
                        if ($mid) :
                            $expiry = get_user_meta($current_user->ID, 'wshc_membership_expiry', true);
                            $days_left = ceil((strtotime($expiry) - time()) / 86400);
                        ?>
                            <div class="membership-status-box" style="margin-top: 30px; padding: 25px; border: 1.5px solid #000; border-radius: 12px;">
                                <div style="font-weight: 800; font-size: 11px; text-transform: uppercase; color: #666; margin-bottom: 10px;">Membership Details</div>
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                                    <div>
                                        <div style="font-size: 20px; font-weight: 800;">#<?php echo esc_html($mid); ?></div>
                                        <div style="font-size: 10px; color: #888;">MEMBERSHIP ID</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 20px; font-weight: 800;"><?php echo date('M d, Y', strtotime($expiry)); ?></div>
                                        <div style="font-size: 10px; color: #888;">EXPIRATION DATE</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 20px; font-weight: 800; color: #d32f2f;"><?php echo max(0, $days_left); ?> Days</div>
                                        <div style="font-size: 10px; color: #888;">COUNTDOWN</div>
                                    </div>
                                </div>
                            </div>
                        <?php else : ?>
                            <p style="margin-top: 20px; color: #666;">You do not have an active membership. Please visit the Information section to apply.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

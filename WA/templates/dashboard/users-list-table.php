<div class="user-management-header">
    <h1 class="section-title">SYSTEM USERS MANAGEMENT</h1>
    <button id="add-user-btn" class="wshc-auth-btn"><span class="dashicons dashicons-plus"></span> Add New User</button>
</div>

<div class="user-management-controls">
    <div class="search-filter">
        <input type="text" id="user-search" placeholder="Search by name or email...">
        <select id="role-filter">
            <option value="">All Roles</option>
            <option value="administrator">Administrator</option>
            <option value="wshc_secretary_general">Secretary-General</option>
            <option value="wshc_regional_coordinator">Regional Coordinator</option>
            <option value="wshc_programs_manager">Programs Manager</option>
            <option value="wshc_scientific_reviewer">Scientific Reviewer</option>
            <option value="wshc_fellowship_member">Fellowship Member</option>
            <option value="wshc_practitioner_member">Practitioner Member</option>
            <option value="wshc_research_member">Research Member</option>
            <option value="wshc_member">Member</option>
            <option value="subscriber">Subscriber</option>
        </select>
        <select id="status-filter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>
</div>

<div class="content-panel no-padding">
<table class="wshc-table">
    <thead>
        <tr>
            <th>User Name</th>
            <th>Email Address</th>
            <th>User Role</th>
            <th>Registration Date</th>
            <th>Account Status</th>
            <th style="text-align: right;">Control Actions</th>
        </tr>
    </thead>
    <tbody id="user-table-body">
        <?php foreach ($users as $user) : 
            $roles = $user->roles;
            $role_label = !empty($roles) ? ucwords(str_replace(['_', 'wshc'], [' ', 'WSHC'], $roles[0])) : 'User';
            $suspended = get_user_meta($user->ID, 'wshc_suspended', true);
            $joined_date = date('M d, Y', strtotime($user->user_registered));
        ?>
            <tr>
                <td><strong><?php echo esc_html($user->user_login); ?></strong></td>
                <td><?php echo esc_html($user->user_email); ?></td>
                <td><span class="role-capsule"><?php echo esc_html($role_label); ?></span></td>
                <td><?php echo esc_html($joined_date); ?></td>
                <td>
                    <?php if ($suspended) : ?>
                        <span class="status-capsule suspended">Suspended</span>
                    <?php else : ?>
                        <span class="status-capsule active">Active</span>
                    <?php endif; ?>
                </td>
                <td class="table-actions" style="text-align: right;">
                    <button class="view-user action-btn" data-id="<?php echo $user->ID; ?>" title="View Details">
                        <span class="dashicons dashicons-visibility"></span>
                    </button>
                    <button class="edit-user action-btn" data-id="<?php echo $user->ID; ?>" title="Edit Account">
                        <span class="dashicons dashicons-edit"></span>
                    </button>
                    <button class="toggle-status action-btn" data-id="<?php echo $user->ID; ?>" title="<?php echo $suspended ? 'Reactivate' : 'Suspend'; ?>">
                        <span class="dashicons <?php echo $suspended ? 'dashicons-yes' : 'dashicons-warning'; ?>"></span>
                    </button>
                    <button class="delete-user action-btn" data-id="<?php echo $user->ID; ?>" title="Delete account">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($users)) : ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #999;">No users found matching your criteria.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<div class="pagination" id="user-pagination">
    <!-- Pagination buttons will be here -->
</div>

<!-- User Form Modal -->
<div id="user-modal" class="wshc-modal hidden">
    <div class="wshc-modal-content">
        <h2 id="modal-title">ADD NEW USER</h2>
        <form id="wshc-user-form">
            <input type="hidden" name="user_id" id="form-user-id">

            <div class="wshc-auth-grid">
                <div class="wshc-auth-form-group">
                    <input type="text" name="first_name" id="form-first-name" placeholder="First Name">
                </div>
                <div class="wshc-auth-form-group">
                    <input type="text" name="last_name" id="form-last-name" placeholder="Last Name">
                </div>
            </div>

            <div class="wshc-auth-grid">
                <div class="wshc-auth-form-group">
                    <input type="text" name="username" id="form-username" placeholder="Username" required minlength="4">
                </div>
                <div class="wshc-auth-form-group">
                    <input type="email" name="email" id="form-email" placeholder="Email Address" required>
                </div>
            </div>

            <div class="wshc-auth-grid">
                <div class="wshc-auth-form-group">
                    <input type="password" name="password" id="form-password" placeholder="Password (8-20 chars)" minlength="8" maxlength="20">
                </div>
                <div class="wshc-auth-form-group">
                    <select name="role" id="form-role" required>
                        <option value="" disabled selected>Select System Role</option>
                        <option value="subscriber">Subscriber</option>
                        <option value="wshc_member">Member</option>
                        <option value="wshc_research_member">Research Member</option>
                        <option value="wshc_practitioner_member">Practitioner Member</option>
                        <option value="wshc_fellowship_member">Fellowship Member</option>
                        <option value="wshc_scientific_reviewer">Scientific Reviewer</option>
                        <option value="wshc_programs_manager">Programs Manager</option>
                        <option value="wshc_regional_coordinator">Regional Coordinator</option>
                        <option value="wshc_secretary_general">Secretary-General</option>
                        <option value="administrator">Administrator</option>
                    </select>
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="wshc-auth-btn">Save User</button>
                <button type="button" class="wshc-auth-btn close-modal" style="background:#666;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- User Details Modal -->
<div id="user-details-modal" class="wshc-modal hidden">
    <div class="wshc-modal-content">
        <h2>USER DETAILS</h2>
        <div id="user-details-content">
            <!-- Details will be loaded here -->
        </div>
        <div class="modal-actions">
            <button type="button" class="wshc-auth-btn close-modal">Close Information</button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="delete-user-modal" class="wshc-modal hidden">
    <div class="wshc-modal-content">
        <h2>CONFIRM DELETION</h2>
        <p>Are you sure you want to permanently delete this user? This action cannot be undone.</p>
        <input type="hidden" id="delete-user-id">
        <div class="modal-actions">
            <button type="button" id="confirm-delete-btn" class="wshc-auth-btn" style="background: #d32f2f;">Delete Account</button>
            <button type="button" class="wshc-auth-btn close-modal" style="background: #666;">Cancel</button>
        </div>
    </div>
</div>

<!-- Confirm Status Toggle Modal -->
<div id="status-user-modal" class="wshc-modal hidden">
    <div class="wshc-modal-content">
        <h2 id="status-modal-title">UPDATE ACCOUNT STATUS</h2>
        <p id="status-modal-message"></p>
        <input type="hidden" id="status-user-id">

        <div id="suspension-advanced-fields" class="hidden">
            <div class="wshc-auth-form-group">
                <label>Reason for Suspension</label>
                <select id="suspension-reason">
                    <option value="Policy Violation">Policy Violation</option>
                    <option value="Spamming Activity">Spamming Activity</option>
                    <option value="Suspicious Login">Suspicious Login</option>
                    <option value="Unprofessional Behavior">Unprofessional Behavior</option>
                    <option value="Account Compromised">Account Compromised</option>
                    <option value="Duplicate Account">Duplicate Account</option>
                    <option value="Non-Payment">Non-Payment</option>
                    <option value="Requested by User">Requested by User</option>
                    <option value="Inactivity">Inactivity</option>
                    <option value="Under Investigation">Under Investigation</option>
                </select>
            </div>
            <div class="wshc-auth-form-group">
                <label>Suspension Duration (Days)</label>
                <input type="number" id="suspension-duration" placeholder="e.g. 30" min="1">
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" id="confirm-status-btn" class="wshc-auth-btn">Confirm Change</button>
            <button type="button" class="wshc-auth-btn close-modal" style="background: #666;">Cancel</button>
        </div>
    </div>
</div>

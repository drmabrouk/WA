<div class="user-management-controls">
    <div class="search-filter">
        <input type="text" id="user-search" placeholder="Search by name or email...">
        <select id="role-filter">
            <option value="">All Roles</option>
            <option value="wshc_administrator">Administrator</option>
            <option value="wshc_staff">Staff</option>
            <option value="wshc_member">Member</option>
        </select>
        <select id="status-filter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
        </select>
        <button id="add-user-btn" class="wshc-auth-btn" style="width: auto; padding: 10px 20px;">+ Add New User</button>
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
                        <span class="btn-icon">👁</span>
                    </button>
                    <button class="edit-user action-btn" data-id="<?php echo $user->ID; ?>" title="Edit Account">
                        <span class="btn-icon">✎</span>
                    </button>
                    <button class="toggle-status action-btn" data-id="<?php echo $user->ID; ?>" title="<?php echo $suspended ? 'Reactivate' : 'Suspend'; ?>">
                        <span class="btn-icon"><?php echo $suspended ? '✓' : '🚫'; ?></span>
                    </button>
                    <button class="delete-user action-btn" data-id="<?php echo $user->ID; ?>" title="Delete account">
                        <span class="btn-icon">🗑</span>
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
            <div class="wshc-auth-form-group">
                <label>Username</label>
                <input type="text" name="username" id="form-username" required>
            </div>
            <div class="wshc-auth-form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="form-email" required>
            </div>
            <div class="wshc-auth-form-group">
                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password" id="form-password">
            </div>
            <div class="wshc-auth-form-group">
                <label>System Role</label>
                <select name="role" id="form-role" required>
                    <option value="wshc_member">WSHC Member</option>
                    <option value="wshc_staff">WSHC Staff</option>
                    <option value="wshc_administrator">WSHC Administrator</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="submit" class="wshc-auth-btn">Save User</button>
                <button type="button" id="close-modal" class="wshc-auth-btn" style="background:#666;">Cancel</button>
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
            <button type="button" id="close-details-modal" class="wshc-auth-btn">Close Information</button>
        </div>
    </div>
</div>

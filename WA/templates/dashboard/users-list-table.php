<div class="user-management-controls">
    <div class="search-filter">
        <input type="text" id="user-search" placeholder="Search users...">
        <select id="role-filter">
            <option value="">All Roles</option>
            <option value="wshc_administrator">Administrator</option>
            <option value="wshc_staff">Staff</option>
            <option value="wshc_member">Member</option>
        </select>
        <button id="add-user-btn" class="wshc-auth-btn" style="width: auto; display: inline-block;">Add User</button>
    </div>
</div>

<table class="wshc-table">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="user-table-body">
        <?php foreach ($users as $user) : 
            $roles = $user->roles;
            $role_label = !empty($roles) ? ucwords(str_replace(['_', 'wshc'], [' ', 'WSHC'], $roles[0])) : 'User';
            $suspended = get_user_meta($user->ID, 'wshc_suspended', true);
        ?>
            <tr>
                <td><?php echo esc_html($user->user_login); ?></td>
                <td><?php echo esc_html($user->user_email); ?></td>
                <td><span class="role-capsule"><?php echo esc_html($role_label); ?></span></td>
                <td>
                    <?php if ($suspended) : ?>
                        <span class="status-capsule suspended">Suspended</span>
                    <?php else : ?>
                        <span class="status-capsule active">Active</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="edit-user" data-id="<?php echo $user->ID; ?>">Edit</button>
                    <button class="toggle-status" data-id="<?php echo $user->ID; ?>"><?php echo $suspended ? 'Reactivate' : 'Suspend'; ?></button>
                    <button class="delete-user" data-id="<?php echo $user->ID; ?>">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="pagination" id="user-pagination">
    <!-- Pagination buttons will be here -->
</div>

<!-- User Form Modal -->
<div id="user-modal" class="wshc-modal hidden">
    <div class="wshc-modal-content">
        <h2 id="modal-title">Add New User</h2>
        <form id="wshc-user-form">
            <input type="hidden" name="user_id" id="form-user-id">
            <div class="wshc-auth-form-group">
                <label>Username</label>
                <input type="text" name="username" id="form-username" required>
            </div>
            <div class="wshc-auth-form-group">
                <label>Email</label>
                <input type="email" name="email" id="form-email" required>
            </div>
            <div class="wshc-auth-form-group">
                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password" id="form-password">
            </div>
            <div class="wshc-auth-form-group">
                <label>Role</label>
                <select name="role" id="form-role" required>
                    <option value="wshc_member">Member</option>
                    <option value="wshc_staff">Staff</option>
                    <option value="wshc_administrator">Administrator</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="submit" class="wshc-auth-btn">Save User</button>
                <button type="button" id="close-modal" class="wshc-auth-btn" style="background:#666;">Cancel</button>
            </div>
        </form>
    </div>
</div>

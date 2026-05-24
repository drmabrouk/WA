jQuery(document).ready(function($) {
    // Sidebar Toggle
    $('#sidebar-toggle').on('click', function() {
        const sidebar = $('#wshc-sidebar');
        sidebar.toggleClass('collapsed');
    });

    // Initial Load
    const activeSection = $('.dashboard-section:not(.hidden)');
    if (activeSection.attr('id') === 'section-user-management') {
        loadUserManagement();
    }

    function loadUserManagement(paged = 1) {
        const search = $('#user-search').val();
        const role = $('#role-filter').val();
        const status = $('#status-filter').val();
        const container = $('#user-management-container');

        container.css('opacity', '0.5');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_list_users',
                nonce: wshc_dashboard_obj.nonce,
                paged: paged,
                search: search,
                role: role,
                status: status
            },
            success: function(response) {
                container.css('opacity', '1');
                if (response.success) {
                    container.html(response.data.html);
                    renderPagination(response.data.pages, paged);
                }
            },
            error: function() {
                container.css('opacity', '1');
            }
        });
    }

    function renderPagination(totalPages, current) {
        let html = '';
        if (totalPages <= 1) return $('#user-pagination').html('');

        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="page-btn ${i === current ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        $('#user-pagination').html(html);
    }

    $(document).on('click', '.page-btn', function() {
        loadUserManagement($(this).data('page'));
    });

    let searchTimer;
    $(document).on('input', '#user-search', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            loadUserManagement(1);
        }, 400);
    });

    $(document).on('change', '#role-filter, #status-filter', function() {
        loadUserManagement(1);
    });

    // Settings Tabs Switching
    $(document).on('click', '.settings-tab', function() {
        const tabId = $(this).data('tab');

        $('.settings-tab').removeClass('active');
        $(this).addClass('active');

        $('.settings-pane').addClass('hidden');
        $(`#tab-${tabId}`).removeClass('hidden');
    });

    $(document).on('click', '#export-data-btn', function() {
        const btn = $(this);
        btn.prop('disabled', true).text('EXPORTING...');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_export_data',
                nonce: wshc_dashboard_obj.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    console.log('Exported Data (Base64):', response.data.data);
                } else {
                    alert(response.data.message);
                }
                btn.prop('disabled', false).text('EXPORT SYSTEM DATA');
            }
        });
    });

    $(document).on('click', '#import-data-btn', function() {
        if (!confirm('Are you sure you want to import data? This may overwrite current settings.')) return;

        const btn = $(this);
        btn.prop('disabled', true).text('IMPORTING...');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_import_data',
                nonce: wshc_dashboard_obj.nonce
            },
            success: function(response) {
                alert(response.data.message);
                btn.prop('disabled', false).text('IMPORT DATA PACKAGE');
            }
        });
    });

    $(document).on('click', '.toggle-status', function() {
        const userId = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).css('opacity', '0.5');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_toggle_user_status',
                nonce: wshc_dashboard_obj.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    loadUserManagement();
                } else {
                    alert(response.data.message);
                    btn.prop('disabled', false).css('opacity', '1');
                }
            }
        });
    });

    $(document).on('click', '#add-user-btn', function() {
        $('#modal-title').text('ADD NEW USER');
        $('#wshc-user-form')[0].reset();
        $('#form-user-id').val('');
        $('#user-modal').removeClass('hidden').hide().fadeIn(300);
    });

    $(document).on('click', '.view-user', function() {
        const userId = $(this).data('id');
        const row = $(this).closest('tr');
        const username = row.find('td:eq(0)').text();
        const email = row.find('td:eq(1)').text();
        const role = row.find('td:eq(2)').text();
        const joined = row.find('td:eq(3)').text();
        const status = row.find('td:eq(4)').text();

        let html = `
            <div class="user-detail-row"><strong>Username</strong> ${username}</div>
            <div class="user-detail-row"><strong>Email</strong> ${email}</div>
            <div class="user-detail-row"><strong>Role</strong> ${role}</div>
            <div class="user-detail-row"><strong>Joined</strong> ${joined}</div>
            <div class="user-detail-row"><strong>Status</strong> ${status}</div>
        `;

        $('#user-details-content').html(html);
        $('#user-details-modal').removeClass('hidden').hide().fadeIn(300);
    });

    $(document).on('click', '#close-details-modal', function() {
        $('#user-details-modal').fadeOut(200, function() {
            $(this).addClass('hidden');
        });
    });

    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        const row = $(this).closest('tr');
        const username = row.find('td:eq(0)').text();
        const email = row.find('td:eq(1)').text();
        const roleText = row.find('.role-capsule').text();
        
        const roleMap = {
            'WSHC Member': 'wshc_member',
            'WSHC Staff': 'wshc_staff',
            'WSHC Administrator': 'wshc_administrator'
        };

        $('#modal-title').text('EDIT ACCOUNT');
        $('#form-user-id').val(userId);
        $('#form-username').val(username);
        $('#form-email').val(email);
        $('#form-role').val(roleMap[roleText] || 'wshc_member');
        $('#form-password').val('');
        $('#user-modal').removeClass('hidden').hide().fadeIn(300);
    });

    $(document).on('click', '#close-modal', function() {
        $('#user-modal').fadeOut(200, function() {
            $(this).addClass('hidden');
        });
    });

    $(document).on('submit', '#wshc-user-form', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const formData = $(this).serialize();

        btn.prop('disabled', true).text('SAVING...');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: formData + '&action=wshc_save_user&nonce=' + wshc_dashboard_obj.nonce,
            success: function(response) {
                if (response.success) {
                    $('#user-modal').addClass('hidden');
                    loadUserManagement();
                } else {
                    alert(response.data.message);
                }
                btn.prop('disabled', false).text('SAVE USER');
            }
        });
    });

    $(document).on('click', '.delete-user', function() {
        if (!confirm('Are you sure you want to delete this user?')) return;
        const userId = $(this).data('id');
        const btn = $(this);
        btn.prop('disabled', true).css('opacity', '0.5');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_delete_user',
                nonce: wshc_dashboard_obj.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    loadUserManagement();
                } else {
                    alert(response.data.message);
                    btn.prop('disabled', false).css('opacity', '1');
                }
            }
        });
    });
});

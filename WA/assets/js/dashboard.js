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
        const searchInput = $('#user-search');
        const roleFilter = $('#role-filter');
        const statusFilter = $('#status-filter');
        const container = $('#user-management-container');

        if (!container.length) return;

        const search = searchInput.length ? searchInput.val() : '';
        const role = roleFilter.length ? roleFilter.val() : '';
        const status = statusFilter.length ? statusFilter.val() : '';

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
                } else {
                    container.html(`<div class="wshc-message error">${response.data.message}</div>`);
                }
            },
            error: function() {
                container.css('opacity', '1').html('<div class="wshc-message error">FAILED TO LOAD USERS. PLEASE TRY AGAIN.</div>');
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
                } else {
                    alert(response.data.message);
                }
                btn.prop('disabled', false).text('EXPORT SYSTEM DATA');
            }
        });
    });

    $(document).on('click', '#save-auth-settings', function() {
        const btn = $(this);
        const data = {
            action: 'wshc_save_auth_settings',
            nonce: wshc_dashboard_obj.nonce,
            enable_reg: $('#enable-reg').is(':checked'),
            enable_login: $('#enable-login').is(':checked'),
            otp_message: $('#otp-message').val(),
            welcome_message: $('#welcome-message').val()
        };

        btn.prop('disabled', true).text('SAVING...');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                alert(response.data.message);
                btn.prop('disabled', false).text('SAVE CONFIGURATIONS');
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

    // Toggle Status Modal
    $(document).on('click', '.toggle-status', function() {
        const userId = $(this).data('id');
        const title = $(this).attr('title');
        $('#status-user-id').val(userId);
        $('#status-modal-message').text(`Are you sure you want to ${title.toLowerCase()}?`);
        $('#status-user-modal').removeClass('hidden').hide().fadeIn(200);
    });

    $('#confirm-status-btn').on('click', function() {
        const userId = $('#status-user-id').val();
        const btn = $(this);
        btn.prop('disabled', true).text('PROCESSING...');

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_toggle_user_status',
                nonce: wshc_dashboard_obj.nonce,
                user_id: userId
            },
            success: function(response) {
                btn.prop('disabled', false).text('Confirm Change');
                $('#status-user-modal').addClass('hidden');
                if (response.success) {
                    loadUserManagement();
                } else {
                    alert(response.data.message);
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
        const role = row.find('td:eq(2)').text();
        const joined = row.find('td:eq(3)').text();
        const status = row.find('td:eq(4)').text();

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_get_user_details',
                nonce: wshc_dashboard_obj.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    const u = response.data;
                    let html = `
                        <div class="user-detail-row"><strong>ID</strong> #${u.ID}</div>
                        <div class="user-detail-row"><strong>Name</strong> ${u.first_name} ${u.last_name}</div>
                        <div class="user-detail-row"><strong>Username</strong> ${u.user_login}</div>
                        <div class="user-detail-row"><strong>Email</strong> ${u.user_email}</div>
                        <div class="user-detail-row"><strong>Role</strong> ${role}</div>
                        <div class="user-detail-row"><strong>Joined</strong> ${joined}</div>
                        <div class="user-detail-row"><strong>Status</strong> ${status}</div>
                        <div style="margin-top: 20px;">
                            <button class="wshc-auth-btn edit-trigger" data-id="${u.ID}" style="background: #000; width: 100%; margin-bottom: 10px;">Edit Account Information</button>
                        </div>
                    `;
                    $('#user-details-content').html(html);
                    $('#user-details-modal').removeClass('hidden').hide().fadeIn(300);
                }
            }
        });
    });

    $(document).on('click', '#close-details-modal', function() {
        $('#user-details-modal').fadeOut(200, function() {
            $(this).addClass('hidden');
        });
    });

    $(document).on('click', '.edit-trigger', function() {
        $('#user-details-modal').addClass('hidden');
        const userId = $(this).data('id');
        triggerEditUser(userId);
    });

    $(document).on('click', '.edit-user', function() {
        triggerEditUser($(this).data('id'));
    });

    function triggerEditUser(userId) {
        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_get_user_details',
                nonce: wshc_dashboard_obj.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    const u = response.data;
                    $('#modal-title').text('EDIT ACCOUNT');
                    $('#form-user-id').val(userId);
                    $('#form-first-name').val(u.first_name);
                    $('#form-last-name').val(u.last_name);
                    $('#form-username').val(u.user_login);
                    $('#form-email').val(u.user_email);
                    $('#form-password').val('');
                    $('#user-modal').removeClass('hidden').hide().fadeIn(300);
                }
            }
        });
    }

    $(document).on('click', '.close-modal', function() {
        $(this).closest('.wshc-modal').fadeOut(200, function() {
            $(this).addClass('hidden');
        });
    });

    $('#close-modal').on('click', function() {
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
                btn.prop('disabled', false).text('SAVE USER');
                if (response.success) {
                    $('#user-modal').addClass('hidden');
                    loadUserManagement();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });

    // Delete User Modal
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        $('#delete-user-id').val(userId);
        $('#delete-user-modal').removeClass('hidden').hide().fadeIn(200);
    });

    $('#confirm-delete-btn').on('click', function() {
        const userId = $('#delete-user-id').val();
        const btn = $(this);
        btn.prop('disabled', true).text('DELETING...');
        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_delete_user',
                nonce: wshc_dashboard_obj.nonce,
                user_id: userId
            },
            success: function(response) {
                btn.prop('disabled', false).text('Delete User');
                $('#delete-user-modal').addClass('hidden');
                if (response.success) {
                    loadUserManagement();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});

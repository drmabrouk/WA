jQuery(document).ready(function($) {
    // Sidebar Toggle
    $('#sidebar-toggle').on('click', function() {
        $('#wshc-sidebar').toggleClass('collapsed');
    });

    // Navigation Switching
    $('.wshc-sidebar .nav-link').on('click', function(e) {
        e.preventDefault();
        const section = $(this).data('section');

        $('.wshc-sidebar .nav-link').removeClass('active');
        $(this).addClass('active');

        $('.dashboard-section').addClass('hidden');
        $(`#section-${section}`).removeClass('hidden');

        if (section === 'user-management') {
            loadUserManagement();
        }
    });

    function loadUserManagement(paged = 1) {
        const search = $('#user-search').val();
        const role = $('#role-filter').val();

        $.ajax({
            url: wshc_dashboard_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_list_users',
                nonce: wshc_dashboard_obj.nonce,
                paged: paged,
                search: search,
                role: role
            },
            success: function(response) {
                if (response.success) {
                    $('#user-management-container').html(response.data.html);
                    renderPagination(response.data.pages, paged);
                }
            }
        });
    }

    function renderPagination(totalPages, current) {
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="page-btn ${i === current ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        $('#user-pagination').html(html);
    }

    $(document).on('click', '.page-btn', function() {
        loadUserManagement($(this).data('page'));
    });

    $(document).on('input', '#user-search', function() {
        loadUserManagement(1);
    });

    $(document).on('change', '#role-filter', function() {
        loadUserManagement(1);
    });

    $(document).on('click', '.toggle-status', function() {
        const userId = $(this).data('id');
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
                }
            }
        });
    });

    $(document).on('click', '#add-user-btn', function() {
        $('#modal-title').text('Add New User');
        $('#wshc-user-form')[0].reset();
        $('#form-user-id').val('');
        $('#user-modal').removeClass('hidden');
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

        $('#modal-title').text('Edit User');
        $('#form-user-id').val(userId);
        $('#form-username').val(username);
        $('#form-email').val(email);
        $('#form-role').val(roleMap[roleText] || 'wshc_member');
        $('#form-password').val('');
        $('#user-modal').removeClass('hidden');
    });

    $(document).on('click', '#close-modal', function() {
        $('#user-modal').addClass('hidden');
    });

    $(document).on('submit', '#wshc-user-form', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

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
            }
        });
    });

    $(document).on('click', '.delete-user', function() {
        if (!confirm('Are you sure you want to delete this user?')) return;
        const userId = $(this).data('id');
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
                }
            }
        });
    });
});

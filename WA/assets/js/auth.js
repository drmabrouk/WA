jQuery(document).ready(function($) {
    const ajaxurl = wshc_auth_obj.ajaxurl;

    // Switch Forms
    $(document).on('click', '.switch-form', function(e) {
        e.preventDefault();
        const target = $(this).data('target');
        
        // If we are on login page, registration and forgot-password might not be in DOM yet
        // In this architecture, all forms should ideally be in one container or loaded via AJAX.
        // For simplicity in this demo, let's assume they are all in the same container.
        
        // But the shortcodes are on different pages.
        // Wait, the requirement says "All authentication forms must work inside the same centered container. Switching between forms must happen dynamically without page reload."
        // This implies they should all be in the same template or loaded dynamically.
        
        // Let's refine the Login template to include others but hidden.
    });

    function showMessage(wrapper, message, type) {
        const msgDiv = wrapper.find('.wshc-message');
        msgDiv.text(message).removeClass('hidden success error').addClass(type);
    }

    // Handle Login
    $(document).on('submit', '#wshc-login-form', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('div');
        const formData = $(this).serialize();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=wshc_login',
            beforeSend: function() {
                wrapper.find('.wshc-auth-btn').prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(wrapper, response.data.message, 'success');
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 1500);
                } else {
                    showMessage(wrapper, response.data.message, 'error');
                    wrapper.find('.wshc-auth-btn').prop('disabled', false).text('Sign In');
                }
            }
        });
    });

    // Handle Registration
    $(document).on('submit', '#wshc-registration-form', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('div');
        const formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=wshc_register',
            beforeSend: function() {
                wrapper.find('.wshc-auth-btn').prop('disabled', true).text('Registering...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(wrapper, response.data.message, 'success');
                    setTimeout(() => {
                        switchForm('login');
                    }, 1500);
                } else {
                    showMessage(wrapper, response.data.message, 'error');
                    wrapper.find('.wshc-auth-btn').prop('disabled', false).text('Register');
                }
            }
        });
    });

    // Handle Forgot Password
    $(document).on('submit', '#wshc-forgot-password-form', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('div');
        const formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=wshc_forgot_password',
            beforeSend: function() {
                wrapper.find('.wshc-auth-btn').prop('disabled', true).text('Requesting...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(wrapper, response.data.message, 'success');
                    $('#reset-user-id').val(response.data.user_id);
                    setTimeout(() => {
                        switchForm('reset_password');
                    }, 1500);
                } else {
                    showMessage(wrapper, response.data.message, 'error');
                    wrapper.find('.wshc-auth-btn').prop('disabled', false).text('Request OTP');
                }
            }
        });
    });

    // Handle Reset Password
    $(document).on('submit', '#wshc-reset-password-form', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('div');
        const formData = $(this).serialize();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=wshc_reset_password',
            beforeSend: function() {
                wrapper.find('.wshc-auth-btn').prop('disabled', true).text('Resetting...');
            },
            success: function(response) {
                if (response.success) {
                    showMessage(wrapper, response.data.message, 'success');
                    setTimeout(() => {
                        switchForm('login');
                    }, 1500);
                } else {
                    showMessage(wrapper, response.data.message, 'error');
                    wrapper.find('.wshc-auth-btn').prop('disabled', false).text('Reset Password');
                }
            }
        });
    });

    function switchForm(target) {
        $('.wshc-auth-container > div').addClass('hidden');
        $(`#wshc-${target}-form-wrapper`).removeClass('hidden');
        $('.wshc-message').addClass('hidden').text('');
        $('.wshc-auth-btn').prop('disabled', false);
        // Reset button texts
        $('#wshc-login-form .wshc-auth-btn').text('Sign In');
        $('#wshc-registration-form .wshc-auth-btn').text('Register');
        $('#wshc-forgot-password-form .wshc-auth-btn').text('Request OTP');
        $('#wshc-reset-password-form .wshc-auth-btn').text('Reset Password');
    }

    $(document).on('click', '.switch-form', function(e) {
        e.preventDefault();
        switchForm($(this).data('target'));
    });
});

$(function () {
    $('#forgotLink').on('click', function (e) {
        e.preventDefault();
        $('#loginCard').hide();
        $('#forgotCard').show();
    });
    $('#backToLoginLink').on('click', function (e) {
        e.preventDefault();
        $('#forgotCard').hide();
        $('#loginCard').show();
    });

    $('#requestResetBtn').on('click', function () {
        $.ajax({
            url: '/auth/password-reset/request',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ email: $('#forgotEmail').val() }),
        }).done(function (data) {
            $('#forgotMsg').text(data.message);
            $('#forgotStep2').show();
            if (data.token_demo) {
                $('#resetToken').val(data.token_demo);
            }
        });
    });

    $('#confirmResetBtn').on('click', function () {
        $.ajax({
            url: '/auth/password-reset/confirm',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ token: $('#resetToken').val(), password: $('#resetPassword').val() }),
        }).done(function (data) {
            $('#forgotMsg').text(data.message + ' Ya puedes iniciar sesión.');
        }).fail(function (xhr) {
            $('#forgotMsg').text((xhr.responseJSON && xhr.responseJSON.message) || 'Error');
        });
    });

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        $('#error').hide();

        $.ajax({
            url: '/auth/login',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email: $('#email').val(),
                password: $('#password').val(),
            }),
            success: function (data) {
                sessionStorage.setItem('csrfToken', data.csrfToken);
                sessionStorage.setItem('usuario', JSON.stringify(data.usuario));
                window.location.href = 'app.php';
            },
            error: function (xhr) {
                var msg = 'Credenciales inválidas';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#error').text(msg).show();
            },
        });
    });
});

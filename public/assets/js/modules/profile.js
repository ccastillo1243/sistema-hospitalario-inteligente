$(function () {
    apiRequest({ url: '/auth/me', method: 'GET' }).done(function (data) {
        $('#prf_nombre').val(data.usuario.nombre);
        $('#prf_apellido').val(data.usuario.apellido);
        $('#prf_email').val(data.usuario.email);
        $('#prf_roles').val(data.usuario.roles.join(', '));
    });

    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        $('#profileError').hide();
        $('#profileSuccess').hide();

        var payload = {
            nombre: $('#prf_nombre').val(),
            apellido: $('#prf_apellido').val(),
            email: $('#prf_email').val(),
        };
        var passwordNueva = $('#prf_password_nueva').val();
        if (passwordNueva) {
            payload.password_actual = $('#prf_password_actual').val();
            payload.password_nueva = passwordNueva;
        }

        apiRequest({ url: '/auth/profile', method: 'PUT', data: JSON.stringify(payload) }).done(function (data) {
            $('#profileSuccess').text(data.message).show();
            $('#prf_password_actual').val('');
            $('#prf_password_nueva').val('');
        }).fail(function (xhr) {
            $('#profileError').text((xhr.responseJSON && xhr.responseJSON.message) || 'Error al guardar').show();
        });
    });
});

$(function () {
    var allRoles = [];
    var editingId = null;

    function loadRoles(callback) {
        apiRequest({ url: '/admin/roles', method: 'GET' }).done(function (data) {
            allRoles = data.items;
            callback();
        });
    }

    function renderRoleCheckboxes(selectedNames) {
        selectedNames = selectedNames || [];
        var $wrap = $('#usr_roles').empty();
        allRoles.forEach(function (r) {
            var checked = selectedNames.indexOf(r.nombre) !== -1;
            var $label = $('<label style="display:flex; align-items:center; gap:6px; font-weight:400; text-transform:capitalize;">');
            $label.append(
                $('<input type="checkbox">').attr('value', r.id).prop('checked', checked).addClass('role-checkbox'),
                $('<span>').text(r.nombre)
            );
            $wrap.append($label);
        });
    }

    function loadUsers() {
        apiRequest({ url: '/admin/users', method: 'GET' }).done(function (data) {
            var $tbody = $('#usersTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="6">Sin usuarios registrados.</td></tr>');
                return;
            }
            data.items.forEach(function (u) {
                var bloqueado = u.bloqueado_hasta && new Date(u.bloqueado_hasta.replace(' ', 'T')) > new Date();
                var $actions = $('<td class="table-actions">');
                if (bloqueado) {
                    $('<button type="button" class="btn btn-sm btn-secondary" title="Desbloquear">Desbloquear</button>')
                        .on('click', function () { unlockUser(u); })
                        .appendTo($actions);
                }
                $('<button type="button" class="icon-btn" title="Editar">✎</button>')
                    .on('click', function () { openEdit(u); })
                    .appendTo($actions);
                $('<button type="button" class="icon-btn" title="Eliminar">🗑</button>')
                    .on('click', function () { removeUser(u); })
                    .appendTo($actions);

                var rolesHtml = u.roles
                    ? '<span class="badge badge-blue">' + $('<div>').text(u.roles).html() + '</span>'
                    : '<span class="badge badge-slate">sin rol</span>';
                var estadoHtml = !u.activo
                    ? '<span class="badge badge-red">inactivo</span>'
                    : (bloqueado ? '<span class="badge badge-amber">bloqueado</span>' : '<span class="badge badge-green">activo</span>');

                $tbody.append(
                    $('<tr>').append(
                        $('<td>').html('<strong>' + $('<div>').text(u.nombre + ' ' + u.apellido).html() + '</strong>'),
                        $('<td>').text(u.email),
                        $('<td>').html(rolesHtml),
                        $('<td>').html(estadoHtml),
                        $('<td>').text(u.ultimo_login || 'Nunca'),
                        $actions
                    )
                );
            });
        });
    }

    function openNew() {
        editingId = null;
        $('#userModalTitle').text('Nuevo usuario');
        $('#userModalError').hide();
        $('#userForm')[0].reset();
        $('#usr_id').val('');
        $('#usr_password').prop('required', true).attr('placeholder', 'Mínimo 6 caracteres (requerido)');
        $('#usr_activo_wrapper').hide();
        renderRoleCheckboxes([]);
        $('#userModal').addClass('open');
    }

    function openEdit(u) {
        editingId = u.id;
        $('#userModalTitle').text('Editar usuario');
        $('#userModalError').hide();
        $('#usr_id').val(u.id);
        $('#usr_nombre').val(u.nombre);
        $('#usr_apellido').val(u.apellido);
        $('#usr_email').val(u.email);
        $('#usr_password').val('').prop('required', false).attr('placeholder', 'Dejar en blanco para no cambiarla');
        $('#usr_activo_wrapper').show();
        $('#usr_activo').prop('checked', !!u.activo);
        renderRoleCheckboxes(u.roles ? u.roles.split(', ') : []);
        $('#userModal').addClass('open');
    }

    function closeModal() {
        $('#userModal').removeClass('open');
    }

    function removeUser(u) {
        if (!confirm('¿Eliminar al usuario ' + u.nombre + ' ' + u.apellido + '?')) return;
        apiRequest({ url: '/admin/users/' + u.id, method: 'DELETE' }).done(loadUsers).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo eliminar');
        });
    }

    function unlockUser(u) {
        apiRequest({ url: '/admin/users/' + u.id + '/unlock', method: 'POST' }).done(loadUsers).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo desbloquear');
        });
    }

    $('#newUserBtn').on('click', openNew);
    $('#cancelUserBtn, #cancelUserBtn2').on('click', closeModal);
    $('#userModal').on('click', function (e) { if (e.target === this) closeModal(); });

    $('#userForm').on('submit', function (e) {
        e.preventDefault();
        var roles = $('.role-checkbox:checked').map(function () { return $(this).val(); }).get();
        var payload = {
            nombre: $('#usr_nombre').val(),
            apellido: $('#usr_apellido').val(),
            email: $('#usr_email').val(),
            roles: roles,
        };
        var password = $('#usr_password').val();
        if (password) payload.password = password;
        if (editingId) payload.activo = $('#usr_activo').is(':checked') ? 1 : 0;

        var request = editingId
            ? apiRequest({ url: '/admin/users/' + editingId, method: 'PUT', data: JSON.stringify(payload) })
            : apiRequest({ url: '/admin/users', method: 'POST', data: JSON.stringify(payload) });

        request.done(function () {
            closeModal();
            loadUsers();
        }).fail(function (xhr) {
            $('#userModalError').text((xhr.responseJSON && xhr.responseJSON.message) || 'Error al guardar').show();
        });
    });

    loadRoles(function () {
        loadUsers();
    });
});

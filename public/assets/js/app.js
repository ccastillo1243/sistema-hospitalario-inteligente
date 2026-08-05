function csrfToken() {
    return sessionStorage.getItem('csrfToken') || '';
}

function apiRequest(options) {
    var defaults = {
        headers: { 'X-CSRF-Token': csrfToken() },
        contentType: 'application/json',
    };
    return $.ajax($.extend({}, defaults, options));
}

/**
 * Devuelve el HTML de un badge de color según el estado, para usar en
 * columnas "estado" de las tablas de todos los módulos.
 */
function statusBadge(estado) {
    if (!estado) return '';
    var map = {
        pendiente: 'amber',
        programada: 'blue',
        confirmada: 'blue',
        en_proceso: 'violet',
        en_espera: 'amber',
        parcial: 'amber',
        libre: 'green',
        completada: 'green',
        completado: 'green',
        pagada: 'green',
        atendido: 'green',
        activo: 'green',
        ocupada: 'red',
        cancelada: 'red',
        vencido: 'red',
    };
    var color = map[estado] || 'slate';
    var texto = String(estado).replace(/_/g, ' ');
    return '<span class="badge badge-' + color + '">' + texto + '</span>';
}

$(function () {
    $('#logoutBtn').on('click', function () {
        apiRequest({ url: '/auth/logout', method: 'POST' }).always(function () {
            sessionStorage.clear();
            window.location.href = 'login.php';
        });
    });

    $('#sidebarToggle').on('click', function () {
        $('#sidebar').toggleClass('open');
    });
    $(document).on('click', function (e) {
        var $sidebar = $('#sidebar');
        if ($sidebar.hasClass('open') && !$(e.target).closest('#sidebar, #sidebarToggle').length) {
            $sidebar.removeClass('open');
        }
    });
});

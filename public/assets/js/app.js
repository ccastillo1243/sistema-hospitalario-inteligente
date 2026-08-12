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

/**
 * Llena un <select> con las opciones que devuelve un endpoint de la API.
 * labelFn recibe cada item y devuelve el texto a mostrar; valueKey es la
 * propiedad que se usa como value (por defecto "id"). Devuelve la promesa
 * de la petición por si el que llama necesita encadenar algo después.
 */
function populateSelect(selector, endpoint, labelFn, options) {
    options = options || {};
    var valueKey = options.valueKey || 'id';
    var placeholder = options.placeholder || 'Selecciona…';
    var selected = options.selected;

    var $el = $(selector);
    $el.empty();
    $el.append($('<option>').val('').text(placeholder));

    return apiRequest({ url: endpoint, method: 'GET' }).done(function (data) {
        (data.items || []).forEach(function (item) {
            var $opt = $('<option>').val(item[valueKey]).text(labelFn(item));
            $el.append($opt);
        });
        if (selected !== undefined && selected !== null) {
            $el.val(String(selected));
        }
    });
}

/**
 * Filtra en vivo las filas de una tabla según el texto de un input de
 * búsqueda (usado por las páginas a medida que no usan CrudModule).
 */
function attachTableSearch(inputSelector, tbodySelector) {
    $(inputSelector).on('input', function () {
        var q = $(this).val().toLowerCase();
        $(tbodySelector).find('tr').each(function () {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(q) !== -1);
        });
    });
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

    // Panel de notificaciones
    if ($('#notifBtn').length) {
        function loadNotifications() {
            apiRequest({ url: '/notifications', method: 'GET' }).done(function (data) {
                var $badge = $('#notifBadge');
                if (data.noLeidas > 0) {
                    $badge.text(data.noLeidas > 9 ? '9+' : data.noLeidas).show();
                } else {
                    $badge.hide();
                }

                var $list = $('#notifList').empty();
                if (!data.items.length) {
                    $list.append('<div style="padding:20px; text-align:center; color:var(--slate-400); font-size:12.5px;">Sin notificaciones.</div>');
                    return;
                }
                data.items.forEach(function (n) {
                    var $item = $('<div>').css({
                        padding: '10px 14px',
                        borderBottom: '1px solid var(--slate-100)',
                        background: n.leida ? '#fff' : 'var(--blue-50)',
                        cursor: n.leida ? 'default' : 'pointer',
                    });
                    $item.append($('<div>').css({ fontWeight: 600, fontSize: '12.5px' }).text(n.titulo));
                    $item.append($('<div>').css({ fontSize: '12px', color: 'var(--slate-500)', marginTop: '2px' }).text(n.mensaje));
                    $item.append($('<div>').css({ fontSize: '11px', color: 'var(--slate-400)', marginTop: '4px' }).text(n.creado_en));
                    if (!n.leida) {
                        $item.on('click', function () {
                            apiRequest({ url: '/notifications/' + n.id + '/read', method: 'PUT' }).done(loadNotifications);
                        });
                    }
                    $list.append($item);
                });
            });
        }

        $('#notifBtn').on('click', function (e) {
            e.stopPropagation();
            $('#notifPanel').toggle();
        });
        $('#notifMarkAll').on('click', function (e) {
            e.stopPropagation();
            apiRequest({ url: '/notifications/read-all', method: 'POST' }).done(loadNotifications);
        });
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#notifPanel, #notifBtn').length) {
                $('#notifPanel').hide();
            }
        });

        loadNotifications();
        setInterval(loadNotifications, 60000);
    }
});

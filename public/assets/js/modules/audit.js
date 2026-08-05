$(function () {
    var tablasCargadas = false;

    function accionBadge(accion) {
        var map = { create: 'green', update: 'blue', delete: 'red' };
        var texto = { create: 'crear', update: 'editar', delete: 'eliminar' }[accion] || accion;
        return '<span class="badge badge-' + (map[accion] || 'slate') + '">' + texto + '</span>';
    }

    function load() {
        var q = $('#auditSearch').val() || '';
        var tabla = $('#auditTabla').val() || '';
        var accion = $('#auditAccion').val() || '';

        apiRequest({
            url: '/admin/audit?q=' + encodeURIComponent(q) + '&tabla=' + encodeURIComponent(tabla) + '&accion=' + encodeURIComponent(accion),
            method: 'GET',
        }).done(function (data) {
            if (!tablasCargadas) {
                var $select = $('#auditTabla');
                data.tablas.forEach(function (t) {
                    $select.append($('<option>').val(t).text(t));
                });
                tablasCargadas = true;
            }

            var $tbody = $('#auditTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="6">Sin registros que coincidan.</td></tr>');
                return;
            }
            data.items.forEach(function (a) {
                var usuario = a.usuario_nombre ? (a.usuario_nombre + ' ' + a.usuario_apellido) : 'Sistema';
                var detalle = '';
                if (a.datos_json) {
                    try {
                        detalle = JSON.stringify(JSON.parse(a.datos_json));
                        if (detalle.length > 80) detalle = detalle.substring(0, 80) + '…';
                    } catch (e) { detalle = ''; }
                }
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(a.creado_en),
                        $('<td>').text(usuario),
                        $('<td>').text(a.tabla),
                        $('<td>').text(a.registro_id),
                        $('<td>').html(accionBadge(a.accion)),
                        $('<td>').css({ fontSize: '11.5px', color: 'var(--slate-500)' }).text(detalle)
                    )
                );
            });
        });
    }

    var searchTimer = null;
    $('#auditSearch').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(load, 300);
    });
    $('#auditTabla, #auditAccion').on('change', load);

    load();
});

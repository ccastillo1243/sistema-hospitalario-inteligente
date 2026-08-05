/**
 * CrudModule: monta un listado paginado + modal de alta/edición vía AJAX,
 * reutilizable para cualquier módulo (endpoint + columns + fields).
 */
var crudModuleCounter = 0;

function CrudModule(config) {
    var instanceId = 'crud' + (++crudModuleCounter);
    var endpoint = config.endpoint;
    var columns = config.columns;
    var fields = config.fields;
    var $table = $(config.tableSelector);
    var $tbody = $table.find('tbody');
    var editingId = null;

    var $modalOverlay = $(
        '<div class="modal-overlay" id="' + instanceId + 'Overlay">' +
            '<div class="modal">' +
                '<div class="modal-header">' +
                    '<h3 class="modal-title">Registro</h3>' +
                    '<button type="button" class="icon-btn modal-close">&times;</button>' +
                '</div>' +
                '<div class="modal-error error-msg" style="display:none;"></div>' +
                '<form class="modal-form"></form>' +
            '</div>' +
        '</div>'
    ).appendTo('body');
    var $modalTitle = $modalOverlay.find('.modal-title');
    var $form = $modalOverlay.find('.modal-form');
    var $modalError = $modalOverlay.find('.modal-error');

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function renderRow(item) {
        var $tr = $('<tr>');
        columns.forEach(function (col) {
            var html = col.render ? col.render(item) : escapeHtml(item[col.key]);
            $tr.append($('<td>').html(html));
        });
        var $actions = $('<td>').addClass('table-actions');
        $('<button type="button" class="icon-btn" title="Editar">✎</button>')
            .on('click', function () { openModal(item); })
            .appendTo($actions);
        $('<button type="button" class="icon-btn" title="Eliminar">🗑</button>')
            .on('click', function () { removeItem(item); })
            .appendTo($actions);
        $tr.append($actions);
        $tbody.append($tr);
    }

    function load() {
        $tbody.html('<tr><td class="table-empty" colspan="' + (columns.length + 1) + '">Cargando…</td></tr>');
        apiRequest({ url: endpoint + '?pageSize=100', method: 'GET' }).done(function (data) {
            $tbody.empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="' + (columns.length + 1) + '">Sin registros todavía.</td></tr>');
                return;
            }
            data.items.forEach(renderRow);
        });
    }

    function buildForm(item) {
        $form.empty();
        fields.forEach(function (field) {
            var value = item ? (item[field.name] ?? '') : '';
            var $wrapper = $('<div class="field">');
            $wrapper.append($('<label>').attr('for', 'f_' + field.name).text(field.label));
            $wrapper.append(
                $('<input>')
                    .attr('type', field.type || 'text')
                    .attr('id', 'f_' + field.name)
                    .attr('name', field.name)
                    .prop('required', !!field.required)
                    .val(value)
            );
            $form.append($wrapper);
        });

        var $actions = $('<div class="form-actions">');
        $('<button type="submit" class="btn">').text(item ? 'Guardar cambios' : 'Crear').appendTo($actions);
        $('<button type="button" class="btn btn-secondary">')
            .text('Cancelar')
            .on('click', closeModal)
            .appendTo($actions);
        $form.append($actions);
    }

    function openModal(item) {
        editingId = item ? item[config.idField || 'id'] : null;
        $modalTitle.text(item ? 'Editar registro' : 'Nuevo registro');
        $modalError.hide();
        buildForm(item);
        $modalOverlay.addClass('open');
    }

    function closeModal() {
        $modalOverlay.removeClass('open');
    }

    function removeItem(item) {
        var id = item[config.idField || 'id'];
        if (!confirm('¿Eliminar este registro? Esta acción no se puede deshacer.')) {
            return;
        }
        apiRequest({ url: endpoint + '/' + id, method: 'DELETE' }).done(load).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'No se pudo eliminar el registro');
        });
    }

    $modalOverlay.on('click', function (e) {
        if (e.target === $modalOverlay.get(0)) closeModal();
    });
    $modalOverlay.find('.modal-close').on('click', closeModal);

    $form.on('submit', function (e) {
        e.preventDefault();
        var data = {};
        fields.forEach(function (field) {
            data[field.name] = $('#f_' + field.name).val();
        });

        var request = editingId
            ? apiRequest({ url: endpoint + '/' + editingId, method: 'PUT', data: JSON.stringify(data) })
            : apiRequest({ url: endpoint, method: 'POST', data: JSON.stringify(data) });

        request.done(function () {
            closeModal();
            load();
        }).fail(function (xhr) {
            $modalError.text((xhr.responseJSON && xhr.responseJSON.message) || 'Error al guardar').show();
        });
    });

    if (config.newButtonSelector) {
        $(config.newButtonSelector).on('click', function () { openModal(null); });
    }

    load();
}

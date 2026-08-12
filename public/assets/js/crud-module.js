/**
 * CrudModule: monta un listado con búsqueda, paginación y modal de
 * alta/edición vía AJAX, reutilizable para cualquier módulo.
 */
var crudModuleCounter = 0;
var PAGE_SIZE = 8;

function CrudModule(config) {
    var instanceId = 'crud' + (++crudModuleCounter);
    var endpoint = config.endpoint;
    var columns = config.columns;
    var fields = config.fields;
    var $table = $(config.tableSelector);
    var $tbody = $table.find('tbody');
    var editingId = null;
    var allItems = [];
    var currentPage = 1;
    var searchTimer = null;

    // Barra de búsqueda, inyectada justo antes de la tabla
    var $searchBar = $(
        '<div style="margin-bottom:12px; max-width:320px;">' +
            '<input type="search" class="crud-search" placeholder="Buscar…" style="width:100%; padding:8px 11px; border:1px solid var(--slate-300); border-radius:6px; font-size:13.5px;">' +
        '</div>'
    ).insertBefore($table.closest('.table-wrap'));
    var $searchInput = $searchBar.find('.crud-search');

    // Paginación, inyectada justo después de la tabla
    var $pager = $(
        '<div style="display:flex; align-items:center; justify-content:space-between; margin-top:10px; font-size:12.5px; color:var(--slate-500);">' +
            '<span class="crud-pager-info"></span>' +
            '<div style="display:flex; gap:6px;">' +
                '<button type="button" class="btn btn-secondary btn-sm crud-prev">Anterior</button>' +
                '<button type="button" class="btn btn-secondary btn-sm crud-next">Siguiente</button>' +
            '</div>' +
        '</div>'
    ).insertAfter($table.closest('.table-wrap'));

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

    function renderPage() {
        $tbody.empty();
        var totalPages = Math.max(1, Math.ceil(allItems.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;

        if (!allItems.length) {
            $tbody.append('<tr><td class="table-empty" colspan="' + (columns.length + 1) + '">Sin registros que coincidan.</td></tr>');
            $pager.find('.crud-pager-info').text('0 resultados');
            $pager.find('.crud-prev, .crud-next').prop('disabled', true);
            return;
        }

        var start = (currentPage - 1) * PAGE_SIZE;
        var pageItems = allItems.slice(start, start + PAGE_SIZE);
        pageItems.forEach(renderRow);

        $pager.find('.crud-pager-info').text(
            allItems.length + ' resultado' + (allItems.length === 1 ? '' : 's') + ' — página ' + currentPage + ' de ' + totalPages
        );
        $pager.find('.crud-prev').prop('disabled', currentPage <= 1);
        $pager.find('.crud-next').prop('disabled', currentPage >= totalPages);
    }

    function load() {
        $tbody.html('<tr><td class="table-empty" colspan="' + (columns.length + 1) + '">Cargando…</td></tr>');
        var q = $searchInput.val() || '';
        apiRequest({ url: endpoint + '?pageSize=100&q=' + encodeURIComponent(q), method: 'GET' }).done(function (data) {
            allItems = data.items;
            currentPage = 1;
            renderPage();
        });
    }

    function buildForm(item) {
        $form.empty();
        fields.forEach(function (field) {
            var value = item ? (item[field.name] ?? '') : '';
            var $wrapper = $('<div class="field">');
            $wrapper.append($('<label>').attr('for', 'f_' + field.name).text(field.label));

            if (field.type === 'select') {
                var $select = $('<select>')
                    .attr('id', 'f_' + field.name)
                    .attr('name', field.name)
                    .prop('required', !!field.required);
                $wrapper.append($select);
                $form.append($wrapper);

                if (field.options) {
                    // Opciones estáticas (ej. estados fijos)
                    $select.append($('<option>').val('').text(field.placeholder || 'Selecciona…'));
                    field.options.forEach(function (opt) {
                        $select.append($('<option>').val(opt.value).text(opt.label));
                    });
                    if (value !== '') $select.val(String(value));
                } else {
                    populateSelect($select, field.optionsEndpoint, field.optionsLabel, {
                        valueKey: field.optionsValue || 'id',
                        placeholder: field.placeholder || 'Selecciona…',
                        selected: value,
                    });
                }
                return;
            }

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

    $searchInput.on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(load, 300);
    });

    $pager.find('.crud-prev').on('click', function () {
        if (currentPage > 1) { currentPage--; renderPage(); }
    });
    $pager.find('.crud-next').on('click', function () {
        var totalPages = Math.max(1, Math.ceil(allItems.length / PAGE_SIZE));
        if (currentPage < totalPages) { currentPage++; renderPage(); }
    });

    if (config.newButtonSelector) {
        $(config.newButtonSelector).on('click', function () { openModal(null); });
    }

    load();
}

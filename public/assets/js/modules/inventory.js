$(function () {
    function loadItems() {
        apiRequest({ url: '/inventory/items', method: 'GET' }).done(function (data) {
            var $tbody = $('#itemsTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="6">Sin artículos registrados.</td></tr>');
                return;
            }
            data.items.forEach(function (i) {
                var $actions = $('<td>');
                $('<button class="btn btn-sm btn-secondary">Movimiento</button>').on('click', function () {
                    $('#mov_articulo_id').val(i.id);
                    $('#movArticuloId').text(i.nombre);
                    $('#movementModal').addClass('open');
                }).appendTo($actions);
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(i.id),
                        $('<td>').text(i.nombre),
                        $('<td>').text(i.categoria || ''),
                        $('<td>').text(i.almacen_nombre),
                        $('<td>').text(i.cantidad),
                        $actions
                    )
                );
            });
        });
    }

    $('#cancelMovementBtn, #cancelMovementBtn2').on('click', function () { $('#movementModal').removeClass('open'); });
    $('.modal-overlay').on('click', function (e) { if (e.target === this) $(this).removeClass('open'); });

    $('#movementForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/inventory/movements',
            method: 'POST',
            data: JSON.stringify({
                articulo_id: $('#mov_articulo_id').val(),
                tipo: $('#mov_tipo').val(),
                cantidad: $('#mov_cantidad').val(),
                motivo: $('#mov_motivo').val(),
            }),
        }).done(function () {
            $('#movementModal').removeClass('open');
            $('#movementForm')[0].reset();
            loadItems();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el movimiento');
        });
    });

    attachTableSearch('#itemsSearch', '#itemsTable tbody');

    loadItems();
});

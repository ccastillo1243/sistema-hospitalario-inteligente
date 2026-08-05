$(function () {
    function loadOrders() {
        apiRequest({ url: '/lab/orders', method: 'GET' }).done(function (data) {
            var $tbody = $('#ordersTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="6">Sin órdenes registradas.</td></tr>');
                return;
            }
            data.items.forEach(function (o) {
                var $actions = $('<td>');
                $('<button class="btn btn-sm btn-secondary">Muestras</button>').on('click', function () { openSamples(o.id); }).appendTo($actions);
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(o.id),
                        $('<td>').text(o.paciente_nombre + ' ' + o.paciente_apellido),
                        $('<td>').text(o.medico_nombre + ' ' + o.medico_apellido),
                        $('<td>').html(statusBadge(o.estado)),
                        $('<td>').text(o.creado_en),
                        $actions
                    )
                );
            });
        });
    }

    function loadSamples(ordenId) {
        apiRequest({ url: '/lab/samples?orden_id=' + ordenId, method: 'GET' }).done(function (data) {
            var $tbody = $('#samplesTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="3">Sin muestras aún.</td></tr>');
                return;
            }
            data.items.forEach(function (m) {
                var $actions = $('<td>');
                $('<button class="btn btn-sm btn-secondary">Registrar resultado</button>').on('click', function () { openResult(m.id); }).appendTo($actions);
                $tbody.append(
                    $('<tr>').append($('<td>').text(m.codigo_barras), $('<td>').text(m.tipo_muestra), $actions)
                );
            });
        });
    }

    function openSamples(ordenId) {
        $('#sam_orden_id').val(ordenId);
        $('#sampleOrderId').text(ordenId);
        $('#samplesPanel').show();
        $('html, body').animate({ scrollTop: $('#samplesPanel').offset().top - 90 }, 200);
        loadSamples(ordenId);
    }

    function openResult(muestraId) {
        $('#res_muestra_id').val(muestraId);
        $('#resultSampleId').text(muestraId);
        $('#resultModal').addClass('open');
    }

    $('#newOrderBtn').on('click', function () { $('#orderModal').addClass('open'); });
    $('#cancelOrderBtn, #cancelOrderBtn2').on('click', function () { $('#orderModal').removeClass('open'); });
    $('#closeSamplesBtn').on('click', function () { $('#samplesPanel').hide(); });
    $('#cancelResultBtn, #cancelResultBtn2').on('click', function () { $('#resultModal').removeClass('open'); });
    $('.modal-overlay').on('click', function (e) { if (e.target === this) $(this).removeClass('open'); });

    $('#orderForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/lab/orders',
            method: 'POST',
            data: JSON.stringify({ paciente_id: $('#ord_paciente_id').val(), medico_id: $('#ord_medico_id').val() }),
        }).done(function () {
            $('#orderModal').removeClass('open');
            $('#orderForm')[0].reset();
            loadOrders();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al crear la orden');
        });
    });

    $('#sampleForm').on('submit', function (e) {
        e.preventDefault();
        var ordenId = $('#sam_orden_id').val();
        apiRequest({
            url: '/lab/samples',
            method: 'POST',
            data: JSON.stringify({
                orden_id: ordenId,
                tipo_examen_id: $('#sam_tipo_examen_id').val(),
                tipo_muestra: $('#sam_tipo_muestra').val(),
            }),
        }).done(function () {
            $('#sam_tipo_examen_id').val('');
            $('#sam_tipo_muestra').val('');
            loadSamples(ordenId);
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar la muestra');
        });
    });

    $('#resultForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/lab/results',
            method: 'POST',
            data: JSON.stringify({
                muestra_id: $('#res_muestra_id').val(),
                parametro_id: $('#res_parametro_id').val(),
                valor: $('#res_valor').val(),
            }),
        }).done(function () {
            $('#resultModal').removeClass('open');
            $('#resultForm')[0].reset();
            loadOrders();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el resultado');
        });
    });

    loadOrders();
});

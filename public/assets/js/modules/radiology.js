$(function () {
    function loadOrders() {
        apiRequest({ url: '/radiology/orders', method: 'GET' }).done(function (data) {
            var $tbody = $('#ordersTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="6">Sin órdenes registradas.</td></tr>');
                return;
            }
            data.items.forEach(function (o) {
                var $actions = $('<td>');
                if (!o.tiene_estudio) {
                    $('<button class="btn btn-sm btn-secondary">Registrar estudio</button>').on('click', function () {
                        $('#est_orden_id').val(o.id);
                        $('#studyOrderId').text(o.id);
                        $('#studyModal').addClass('open');
                    }).appendTo($actions);
                } else if (o.estado !== 'completada') {
                    $('<button class="btn btn-sm btn-secondary">Registrar informe</button>').on('click', function () {
                        $('#inf_estudio_id').val(o.estudio_id);
                        $('#reportStudyId').text(o.estudio_id);
                        $('#reportModal').addClass('open');
                    }).appendTo($actions);
                } else {
                    $actions.html(statusBadge('completada'));
                }
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(o.id),
                        $('<td>').text(o.paciente_nombre + ' ' + o.paciente_apellido),
                        $('<td>').text(o.medico_nombre + ' ' + o.medico_apellido),
                        $('<td>').text(o.tipo_estudio),
                        $('<td>').html(statusBadge(o.estado)),
                        $actions
                    )
                );
            });
        });
    }

    $('#newOrderBtn').on('click', function () { $('#orderModal').addClass('open'); });
    $('#cancelOrderBtn, #cancelOrderBtn2').on('click', function () { $('#orderModal').removeClass('open'); });
    $('#cancelStudyBtn, #cancelStudyBtn2').on('click', function () { $('#studyModal').removeClass('open'); });
    $('#cancelReportBtn, #cancelReportBtn2').on('click', function () { $('#reportModal').removeClass('open'); });
    $('.modal-overlay').on('click', function (e) { if (e.target === this) $(this).removeClass('open'); });

    $('#orderForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/radiology/orders',
            method: 'POST',
            data: JSON.stringify({
                paciente_id: $('#ord_paciente_id').val(),
                medico_id: $('#ord_medico_id').val(),
                tipo_estudio_id: $('#ord_tipo_estudio_id').val(),
            }),
        }).done(function () {
            $('#orderModal').removeClass('open');
            $('#orderForm')[0].reset();
            loadOrders();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al crear la orden');
        });
    });

    $('#studyForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/radiology/studies',
            method: 'POST',
            data: JSON.stringify({
                orden_id: $('#est_orden_id').val(),
                realizado_por: $('#est_realizado_por').val(),
                observaciones: $('#est_observaciones').val(),
            }),
        }).done(function () {
            $('#studyModal').removeClass('open');
            $('#studyForm')[0].reset();
            loadOrders();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el estudio');
        });
    });

    $('#reportForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/radiology/reports',
            method: 'POST',
            data: JSON.stringify({
                estudio_id: $('#inf_estudio_id').val(),
                radiologo_id: $('#inf_radiologo_id').val(),
                contenido: $('#inf_contenido').val(),
            }),
        }).done(function () {
            $('#reportModal').removeClass('open');
            $('#reportForm')[0].reset();
            loadOrders();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el informe');
        });
    });

    attachTableSearch('#ordersSearch', '#ordersTable tbody');

    loadOrders();
});

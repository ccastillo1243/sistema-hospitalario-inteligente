$(function () {
    var medicamentosById = {};

    function loadStock() {
        apiRequest({ url: '/pharmacy/medications/stock', method: 'GET' }).done(function (data) {
            var $tbody = $('#stockTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="2">Sin medicamentos registrados.</td></tr>');
                return;
            }
            data.items.forEach(function (m) {
                var low = Number(m.stock_total) < 20;
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(m.nombre),
                        $('<td>').html(m.stock_total + ' ' + (low ? statusBadge('vencido') : statusBadge('activo')))
                    )
                );
            });
        });
    }

    function loadPrescriptions() {
        apiRequest({ url: '/pharmacy/prescriptions', method: 'GET' }).done(function (data) {
            var $tbody = $('#prescriptionsTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="5">Sin recetas registradas.</td></tr>');
                return;
            }
            data.items.forEach(function (r) {
                var $actions = $('<td>');
                $('<button class="btn btn-sm btn-secondary">Ítems</button>').on('click', function () { openItems(r.id); }).appendTo($actions);
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(r.id),
                        $('<td>').text(r.paciente_nombre + ' ' + r.paciente_apellido),
                        $('<td>').text(r.medico_nombre + ' ' + r.medico_apellido),
                        $('<td>').text(r.creado_en),
                        $actions
                    )
                );
            });
        });
    }

    function loadItems(recetaId) {
        apiRequest({ url: '/pharmacy/prescription-items?receta_id=' + recetaId, method: 'GET' }).done(function (data) {
            var $tbody = $('#itemsTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="4">Sin ítems aún.</td></tr>');
                return;
            }
            data.items.forEach(function (it) {
                var $actions = $('<td>');
                $('<button class="btn btn-sm">Dispensar</button>').on('click', function () { dispense(it.id, it.cantidad); }).appendTo($actions);
                var nombreMed = medicamentosById[it.medicamento_id] || ('#' + it.medicamento_id);
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(nombreMed),
                        $('<td>').text(it.cantidad),
                        $('<td>').text(it.indicaciones || ''),
                        $actions
                    )
                );
            });
        });
    }

    function openItems(recetaId) {
        $('#item_receta_id').val(recetaId);
        $('#itemsRecetaId').text(recetaId);
        populateSelect('#item_medicamento_id', '/pharmacy/medications?pageSize=100',
            function (m) { return m.nombre + ' — ' + m.presentacion; },
            { placeholder: 'Selecciona un medicamento…' }
        ).done(function (data) {
            (data.items || []).forEach(function (m) { medicamentosById[m.id] = m.nombre; });
            loadItems(recetaId);
        });
        $('#itemsPanel').show();
        $('html, body').animate({ scrollTop: $('#itemsPanel').offset().top - 90 }, 200);
    }

    function dispense(recetaItemId, cantidad) {
        if (!confirm('¿Dispensar ' + cantidad + ' unidades?')) return;
        apiRequest({
            url: '/pharmacy/dispense',
            method: 'POST',
            data: JSON.stringify({ receta_item_id: recetaItemId, cantidad: cantidad }),
        }).done(function () {
            loadStock();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al dispensar');
        });
    }

    function openPrescriptionModal() {
        populateSelect('#rec_paciente_id', '/patients?pageSize=100',
            function (p) { return p.nombre + ' ' + p.apellido + ' — ' + p.documento_identidad; },
            { placeholder: 'Selecciona un paciente…' });
        populateSelect('#rec_medico_id', '/staff/doctors',
            function (m) { return m.nombre + ' ' + m.apellido; },
            { placeholder: 'Selecciona un médico…' });
        $('#prescriptionModal').addClass('open');
    }

    $('#newPrescriptionBtn').on('click', openPrescriptionModal);
    $('#cancelPrescriptionBtn, #cancelPrescriptionBtn2').on('click', function () { $('#prescriptionModal').removeClass('open'); });
    $('#closeItemsBtn').on('click', function () { $('#itemsPanel').hide(); });
    $('.modal-overlay').on('click', function (e) { if (e.target === this) $(this).removeClass('open'); });

    $('#prescriptionForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/pharmacy/prescriptions',
            method: 'POST',
            data: JSON.stringify({ paciente_id: $('#rec_paciente_id').val(), medico_id: $('#rec_medico_id').val() }),
        }).done(function () {
            $('#prescriptionModal').removeClass('open');
            $('#prescriptionForm')[0].reset();
            loadPrescriptions();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al crear la receta');
        });
    });

    $('#itemForm').on('submit', function (e) {
        e.preventDefault();
        var recetaId = $('#item_receta_id').val();
        apiRequest({
            url: '/pharmacy/prescription-items',
            method: 'POST',
            data: JSON.stringify({
                receta_id: recetaId,
                medicamento_id: $('#item_medicamento_id').val(),
                cantidad: $('#item_cantidad').val(),
                indicaciones: $('#item_indicaciones').val(),
            }),
        }).done(function () {
            $('#item_medicamento_id').val('');
            $('#item_cantidad').val('');
            $('#item_indicaciones').val('');
            loadItems(recetaId);
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al agregar el ítem');
        });
    });

    attachTableSearch('#prescriptionsSearch', '#prescriptionsTable tbody');

    loadStock();
    loadPrescriptions();
});

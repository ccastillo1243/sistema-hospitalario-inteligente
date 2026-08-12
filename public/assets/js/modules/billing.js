$(function () {
    function loadInvoices() {
        apiRequest({ url: '/billing/invoices', method: 'GET' }).done(function (data) {
            var $tbody = $('#invoicesTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="8">Sin facturas registradas.</td></tr>');
                return;
            }
            data.items.forEach(function (f) {
                var $actions = $('<td>');
                if (f.estado !== 'pagada') {
                    $('<button class="btn btn-sm btn-secondary">Registrar pago</button>').on('click', function () {
                        $('#pay_factura_id').val(f.id);
                        $('#payInvoiceId').text(f.id);
                        populateSelect('#pay_metodo_id', '/billing/payment-methods',
                            function (m) { return m.nombre; },
                            { placeholder: 'Selecciona un método de pago…' });
                        $('#paymentModal').addClass('open');
                    }).appendTo($actions);
                } else {
                    $actions.html(statusBadge('pagada'));
                }
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(f.id),
                        $('<td>').text(f.paciente_nombre + ' ' + f.paciente_apellido),
                        $('<td>').text(f.subtotal),
                        $('<td>').text(f.impuestos),
                        $('<td>').text(f.total),
                        $('<td>').text(f.pagado),
                        $('<td>').html(statusBadge(f.estado)),
                        $actions
                    )
                );
            });
        });
    }

    function openInvoiceModal() {
        populateSelect('#inv_paciente_id', '/patients?pageSize=100',
            function (p) { return p.nombre + ' ' + p.apellido + ' — ' + p.documento_identidad; },
            { placeholder: 'Selecciona un paciente…' });
        populateSelect('#inv_servicio1_id', '/billing/services',
            function (s) { return s.nombre + ' — $' + s.precio; },
            { placeholder: 'Selecciona un servicio…' });
        populateSelect('#inv_servicio2_id', '/billing/services',
            function (s) { return s.nombre + ' — $' + s.precio; },
            { placeholder: 'Ninguno' });
        $('#invoiceModal').addClass('open');
    }

    $('#newInvoiceBtn').on('click', openInvoiceModal);
    $('#cancelInvoiceBtn, #cancelInvoiceBtn2').on('click', function () { $('#invoiceModal').removeClass('open'); });
    $('#cancelPaymentBtn, #cancelPaymentBtn2').on('click', function () { $('#paymentModal').removeClass('open'); });
    $('.modal-overlay').on('click', function (e) { if (e.target === this) $(this).removeClass('open'); });

    $('#invoiceForm').on('submit', function (e) {
        e.preventDefault();
        var items = [{ servicio_id: $('#inv_servicio1_id').val(), cantidad: $('#inv_servicio1_cant').val() }];
        if ($('#inv_servicio2_id').val()) {
            items.push({ servicio_id: $('#inv_servicio2_id').val(), cantidad: $('#inv_servicio2_cant').val() || 1 });
        }

        apiRequest({
            url: '/billing/invoices',
            method: 'POST',
            data: JSON.stringify({ paciente_id: $('#inv_paciente_id').val(), items: items }),
        }).done(function () {
            $('#invoiceModal').removeClass('open');
            $('#invoiceForm')[0].reset();
            loadInvoices();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al crear la factura');
        });
    });

    $('#paymentForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/billing/payments',
            method: 'POST',
            data: JSON.stringify({
                factura_id: $('#pay_factura_id').val(),
                metodo_pago_id: $('#pay_metodo_id').val(),
                monto: $('#pay_monto').val(),
            }),
        }).done(function () {
            $('#paymentModal').removeClass('open');
            $('#paymentForm')[0].reset();
            loadInvoices();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el pago');
        });
    });

    attachTableSearch('#invoicesSearch', '#invoicesTable tbody');

    loadInvoices();
});

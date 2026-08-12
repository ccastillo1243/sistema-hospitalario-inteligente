$(function () {
    function loadBeds() {
        apiRequest({ url: '/beds?pageSize=100', method: 'GET' }).done(function (data) {
            var $tbody = $('#bedsTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="4">Sin camas registradas.</td></tr>');
                return;
            }
            data.items.forEach(function (b) {
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(b.id),
                        $('<td>').text(b.habitacion_id),
                        $('<td>').text(b.codigo),
                        $('<td>').html(statusBadge(b.estado))
                    )
                );
            });
        });
    }

    function loadAdmissions() {
        apiRequest({ url: '/hospitalization/admissions', method: 'GET' }).done(function (data) {
            var $tbody = $('#admissionsTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="8">Sin ingresos activos.</td></tr>');
                return;
            }
            data.items.forEach(function (i) {
                var $actions = $('<td>');
                if (!i.tiene_alta) {
                    $('<button class="btn btn-sm btn-secondary">Dar de alta</button>').on('click', function () {
                        $('#dis_ingreso_id').val(i.id);
                        $('#dischargeModal').addClass('open');
                    }).appendTo($actions);
                } else {
                    $actions.html(statusBadge('completada'));
                }
                $tbody.append(
                    $('<tr>').append(
                        $('<td>').text(i.paciente_nombre + ' ' + i.paciente_apellido),
                        $('<td>').text(i.cama_codigo),
                        $('<td>').text(i.habitacion_numero),
                        $('<td>').text(i.medico_nombre + ' ' + i.medico_apellido),
                        $('<td>').text(i.motivo),
                        $('<td>').text(i.ingresado_en),
                        $('<td>').html(i.tiene_alta ? statusBadge('completada') : statusBadge('en_espera')),
                        $actions
                    )
                );
            });
        });
    }

    function openAdmissionModal() {
        populateSelect('#adm_paciente_id', '/patients?pageSize=100',
            function (p) { return p.nombre + ' ' + p.apellido + ' — ' + p.documento_identidad; },
            { placeholder: 'Selecciona un paciente…' });
        populateSelect('#adm_medico_id', '/staff/doctors',
            function (m) { return m.nombre + ' ' + m.apellido; },
            { placeholder: 'Selecciona un médico…' });
        populateSelect('#adm_cama_id', '/beds?estado=libre&pageSize=100',
            function (b) { return 'Cama ' + b.codigo + ' (habitación ' + b.habitacion_id + ')'; },
            { placeholder: 'Selecciona una cama libre…' });
        $('#admissionModal').addClass('open');
    }

    $('#newAdmissionBtn').on('click', openAdmissionModal);
    $('#cancelAdmissionBtn, #cancelAdmissionBtn2').on('click', function () { $('#admissionModal').removeClass('open'); });
    $('#cancelDischargeBtn, #cancelDischargeBtn2').on('click', function () { $('#dischargeModal').removeClass('open'); });
    $('.modal-overlay').on('click', function (e) {
        if (e.target === this) $(this).removeClass('open');
    });

    $('#admissionForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/hospitalization/admissions',
            method: 'POST',
            data: JSON.stringify({
                paciente_id: $('#adm_paciente_id').val(),
                cama_id: $('#adm_cama_id').val(),
                medico_id: $('#adm_medico_id').val(),
                motivo: $('#adm_motivo').val(),
            }),
        }).done(function () {
            $('#admissionModal').removeClass('open');
            $('#admissionForm')[0].reset();
            loadBeds();
            loadAdmissions();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el ingreso');
        });
    });

    $('#dischargeForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/hospitalization/discharges',
            method: 'POST',
            data: JSON.stringify({
                ingreso_id: $('#dis_ingreso_id').val(),
                resumen: $('#dis_resumen').val(),
            }),
        }).done(function () {
            $('#dischargeModal').removeClass('open');
            $('#dischargeForm')[0].reset();
            loadBeds();
            loadAdmissions();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el alta');
        });
    });

    attachTableSearch('#admissionsSearch', '#admissionsTable tbody');

    loadBeds();
    loadAdmissions();
});

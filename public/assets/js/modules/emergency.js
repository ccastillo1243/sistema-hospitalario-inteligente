$(function () {
    function loadCases() {
        apiRequest({ url: '/emergency/cases', method: 'GET' }).done(function (data) {
            var $tbody = $('#casesTable tbody').empty();
            if (!data.items.length) {
                $tbody.append('<tr><td class="table-empty" colspan="6">Sin casos registrados.</td></tr>');
                return;
            }
            data.items.forEach(function (c) {
                var $actions = $('<td>');
                if (c.estado !== 'atendido') {
                    $('<button class="btn btn-sm btn-secondary">Atender</button>').on('click', function () {
                        $('#atn_caso_id').val(c.id);
                        $('#attendCaseId').text(c.id);
                        $('#attendModal').addClass('open');
                    }).appendTo($actions);
                } else {
                    $actions.html(statusBadge('atendido'));
                }
                var $row = $('<tr>').append(
                    $('<td>').text(c.paciente_nombre + ' ' + c.paciente_apellido),
                    $('<td>').text(c.nivel_triage + ' (P' + c.prioridad + ')'),
                    $('<td>').text(c.motivo),
                    $('<td>').text(c.llegada_en),
                    $('<td>').html(statusBadge(c.estado)),
                    $actions
                );
                if (c.prioridad <= 2 && c.estado !== 'atendido') {
                    $row.addClass('row-urgent');
                }
                $tbody.append($row);
            });
        });
    }

    $('#newCaseBtn').on('click', function () { $('#caseModal').addClass('open'); });
    $('#cancelCaseBtn, #cancelCaseBtn2').on('click', function () { $('#caseModal').removeClass('open'); });
    $('#cancelAttendBtn, #cancelAttendBtn2').on('click', function () { $('#attendModal').removeClass('open'); });
    $('.modal-overlay').on('click', function (e) { if (e.target === this) $(this).removeClass('open'); });

    $('#caseForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/emergency/cases',
            method: 'POST',
            data: JSON.stringify({
                paciente_id: $('#cas_paciente_id').val(),
                nivel_triage_id: $('#cas_nivel_id').val(),
                motivo: $('#cas_motivo').val(),
            }),
        }).done(function () {
            $('#caseModal').removeClass('open');
            $('#caseForm')[0].reset();
            loadCases();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar el caso');
        });
    });

    $('#attendForm').on('submit', function (e) {
        e.preventDefault();
        apiRequest({
            url: '/emergency/attend',
            method: 'POST',
            data: JSON.stringify({
                caso_id: $('#atn_caso_id').val(),
                medico_id: $('#atn_medico_id').val(),
                notas: $('#atn_notas').val(),
            }),
        }).done(function () {
            $('#attendModal').removeClass('open');
            $('#attendForm')[0].reset();
            loadCases();
        }).fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Error al registrar la atención');
        });
    });

    attachTableSearch('#casesSearch', '#casesTable tbody');

    loadCases();
});

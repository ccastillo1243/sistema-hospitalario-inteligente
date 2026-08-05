$(function () {
    CrudModule({
        endpoint: '/appointments',
        tableSelector: '#appointmentsTable',
        newButtonSelector: '#newAppointmentBtn',
        columns: [
            { key: 'fecha_hora' },
            { key: 'paciente', render: function (i) { return i.paciente_nombre + ' ' + i.paciente_apellido; } },
            { key: 'medico', render: function (i) { return i.medico_nombre + ' ' + i.medico_apellido; } },
            { key: 'tipo_cita' },
            { key: 'estado', render: function (i) { return statusBadge(i.estado); } },
        ],
        fields: [
            { name: 'paciente_id', label: 'ID paciente', required: true },
            { name: 'medico_id', label: 'ID médico', required: true },
            { name: 'tipo_cita_id', label: 'ID tipo de cita', required: true },
            { name: 'fecha_hora', label: 'Fecha y hora', type: 'datetime-local', required: true },
            { name: 'motivo', label: 'Motivo' },
            { name: 'estado', label: 'Estado (programada/confirmada/completada/cancelada)' },
        ],
    });
});

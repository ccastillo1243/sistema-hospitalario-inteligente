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
            {
                name: 'paciente_id', label: 'Paciente', required: true, type: 'select',
                optionsEndpoint: '/patients?pageSize=100',
                optionsLabel: function (p) { return p.nombre + ' ' + p.apellido + ' — ' + p.documento_identidad; },
            },
            {
                name: 'medico_id', label: 'Médico', required: true, type: 'select',
                optionsEndpoint: '/staff/doctors',
                optionsLabel: function (m) { return m.nombre + ' ' + m.apellido + (m.especialidades ? ' (' + m.especialidades + ')' : ''); },
            },
            {
                name: 'tipo_cita_id', label: 'Tipo de cita', required: true, type: 'select',
                optionsEndpoint: '/appointment-types',
                optionsLabel: function (t) { return t.nombre; },
            },
            { name: 'fecha_hora', label: 'Fecha y hora', type: 'datetime-local', required: true },
            { name: 'motivo', label: 'Motivo' },
            {
                name: 'estado', label: 'Estado', type: 'select',
                options: [
                    { value: 'programada', label: 'Programada' },
                    { value: 'confirmada', label: 'Confirmada' },
                    { value: 'completada', label: 'Completada' },
                    { value: 'cancelada', label: 'Cancelada' },
                ],
            },
        ],
    });
});

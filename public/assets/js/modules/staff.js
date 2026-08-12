$(function () {
    CrudModule({
        endpoint: '/staff/doctors',
        tableSelector: '#doctorsTable',
        newButtonSelector: '#newDoctorBtn',
        columns: [
            { key: 'nombre' },
            { key: 'apellido' },
            { key: 'cedula_profesional' },
            { key: 'especialidades' },
        ],
        fields: [
            { name: 'nombre', label: 'Nombre', required: true },
            { name: 'apellido', label: 'Apellido', required: true },
            { name: 'cedula_profesional', label: 'Cédula profesional' },
            { name: 'telefono', label: 'Teléfono' },
            {
                name: 'especialidad_id', label: 'Especialidad (solo al crear)', type: 'select',
                optionsEndpoint: '/specialties',
                optionsLabel: function (e) { return e.nombre; },
            },
        ],
    });

    CrudModule({
        endpoint: '/staff/nurses',
        tableSelector: '#nursesTable',
        newButtonSelector: '#newNurseBtn',
        columns: [
            { key: 'nombre' },
            { key: 'apellido' },
            { key: 'cedula_profesional' },
        ],
        fields: [
            { name: 'nombre', label: 'Nombre', required: true },
            { name: 'apellido', label: 'Apellido', required: true },
            { name: 'cedula_profesional', label: 'Cédula profesional' },
            { name: 'telefono', label: 'Teléfono' },
        ],
    });
});

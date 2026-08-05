$(function () {
    CrudModule({
        endpoint: '/patients',
        tableSelector: '#patientsTable',
        newButtonSelector: '#newPatientBtn',
        columns: [
            { key: 'numero_expediente' },
            { key: 'nombre' },
            { key: 'apellido' },
            { key: 'documento_identidad' },
            { key: 'genero' },
        ],
        fields: [
            { name: 'numero_expediente', label: 'Número de expediente', required: true },
            { name: 'nombre', label: 'Nombre', required: true },
            { name: 'apellido', label: 'Apellido', required: true },
            { name: 'fecha_nacimiento', label: 'Fecha de nacimiento', type: 'date', required: true },
            { name: 'genero', label: 'Género', required: true },
            { name: 'documento_identidad', label: 'Documento de identidad', required: true },
            { name: 'telefono', label: 'Teléfono' },
            { name: 'email', label: 'Email', type: 'email' },
        ],
    });
});

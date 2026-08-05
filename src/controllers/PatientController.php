<?php

class PatientController extends CrudController
{
    protected static string $model = Paciente::class;
    protected static string $auditName = 'pacientes';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'];
    protected static array $rolesEscritura = ['admin', 'recepcion'];
    protected static array $requiredOnCreate = [
        'numero_expediente', 'nombre', 'apellido', 'fecha_nacimiento', 'genero', 'documento_identidad',
    ];

    public static function medicalRecord(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesLectura);

        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM expedientes_clinicos WHERE paciente_id = ?');
        $stmt->execute([(int) $params['id']]);
        $expediente = $stmt->fetch();

        if (!$expediente) {
            Response::error('Expediente clínico no encontrado', 404);
        }

        Response::json($expediente);
    }
}

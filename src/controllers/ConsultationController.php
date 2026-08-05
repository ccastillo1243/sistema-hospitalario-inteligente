<?php

class ConsultationController extends CrudController
{
    protected static string $model = Consulta::class;
    protected static string $auditName = 'consultas';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria'];
    protected static array $rolesEscritura = ['admin', 'medico'];
    protected static array $requiredOnCreate = ['paciente_id', 'medico_id', 'fecha'];
    protected static array $filterable = ['paciente_id', 'medico_id', 'cita_id'];
}

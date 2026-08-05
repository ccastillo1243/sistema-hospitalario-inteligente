<?php

class AvailabilityController extends CrudController
{
    protected static string $model = DisponibilidadMedico::class;
    protected static string $auditName = 'disponibilidad_medico';
    protected static array $rolesLectura = ['admin', 'medico', 'recepcion'];
    protected static array $rolesEscritura = ['admin', 'medico'];
    protected static array $requiredOnCreate = ['medico_id', 'dia_semana', 'hora_inicio', 'hora_fin'];
    protected static array $filterable = ['medico_id'];
}

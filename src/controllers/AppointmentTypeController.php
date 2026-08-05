<?php

class AppointmentTypeController extends CrudController
{
    protected static string $model = TipoCita::class;
    protected static string $auditName = 'tipos_cita';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['nombre', 'duracion_minutos'];
}

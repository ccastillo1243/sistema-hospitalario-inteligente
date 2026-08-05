<?php

class RoomTypeController extends CrudController
{
    protected static string $model = TipoHabitacion::class;
    protected static string $auditName = 'tipos_habitacion';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['nombre'];
}

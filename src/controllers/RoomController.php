<?php

class RoomController extends CrudController
{
    protected static string $model = Habitacion::class;
    protected static string $auditName = 'habitaciones';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['tipo_habitacion_id', 'numero'];
}

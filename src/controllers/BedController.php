<?php

class BedController extends CrudController
{
    protected static string $model = Cama::class;
    protected static string $auditName = 'camas';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['habitacion_id', 'codigo'];
    protected static array $filterable = ['habitacion_id', 'estado'];
}

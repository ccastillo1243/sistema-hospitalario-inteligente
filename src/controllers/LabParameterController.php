<?php

class LabParameterController extends CrudController
{
    protected static string $model = ParametroExamen::class;
    protected static string $auditName = 'parametros_examen';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'laboratorio'];
    protected static array $rolesEscritura = ['admin', 'laboratorio'];
    protected static array $requiredOnCreate = ['tipo_examen_id', 'nombre'];
    protected static array $filterable = ['tipo_examen_id'];
}

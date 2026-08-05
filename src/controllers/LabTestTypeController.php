<?php

class LabTestTypeController extends CrudController
{
    protected static string $model = TipoExamenLaboratorio::class;
    protected static string $auditName = 'tipos_examen_laboratorio';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'laboratorio'];
    protected static array $rolesEscritura = ['admin', 'laboratorio'];
    protected static array $requiredOnCreate = ['nombre'];
}

<?php

class SpecialtyController extends CrudController
{
    protected static string $model = Especialidad::class;
    protected static string $auditName = 'especialidades';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['nombre'];
}

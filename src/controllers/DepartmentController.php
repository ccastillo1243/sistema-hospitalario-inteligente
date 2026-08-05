<?php

class DepartmentController extends CrudController
{
    protected static string $model = Departamento::class;
    protected static string $auditName = 'departamentos';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['nombre'];
}

<?php

class DiagnosisController extends CrudController
{
    protected static string $model = Diagnostico::class;
    protected static string $auditName = 'diagnosticos';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria'];
    protected static array $rolesEscritura = ['admin', 'medico'];
    protected static array $requiredOnCreate = ['consulta_id', 'descripcion'];
    protected static array $filterable = ['consulta_id'];
}

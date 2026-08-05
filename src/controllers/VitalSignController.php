<?php

class VitalSignController extends CrudController
{
    protected static string $model = SignoVital::class;
    protected static string $auditName = 'signos_vitales';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria'];
    protected static array $rolesEscritura = ['admin', 'medico', 'enfermeria'];
    protected static array $requiredOnCreate = ['paciente_id'];
    protected static array $filterable = ['paciente_id', 'consulta_id'];
}

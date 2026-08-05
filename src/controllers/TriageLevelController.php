<?php

class TriageLevelController extends CrudController
{
    protected static string $model = NivelTriage::class;
    protected static string $auditName = 'niveles_triage';
    protected static array $rolesLectura = ['admin', 'medico', 'enfermeria', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['nombre', 'prioridad'];
}

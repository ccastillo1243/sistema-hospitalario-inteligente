<?php

class RadiologyTestTypeController extends CrudController
{
    protected static string $model = TipoEstudioRadiologico::class;
    protected static string $auditName = 'tipos_estudio_radiologico';
    protected static array $rolesLectura = ['admin', 'medico', 'radiologia'];
    protected static array $rolesEscritura = ['admin', 'radiologia'];
    protected static array $requiredOnCreate = ['nombre'];
}

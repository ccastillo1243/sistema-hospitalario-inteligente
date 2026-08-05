<?php

class MedicationCategoryController extends CrudController
{
    protected static string $model = CategoriaMedicamento::class;
    protected static string $auditName = 'categorias_medicamento';
    protected static array $rolesLectura = ['admin', 'medico', 'farmacia'];
    protected static array $rolesEscritura = ['admin', 'farmacia'];
    protected static array $requiredOnCreate = ['nombre'];
}

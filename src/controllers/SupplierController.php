<?php

class SupplierController extends CrudController
{
    protected static string $model = Proveedor::class;
    protected static string $auditName = 'proveedores';
    protected static array $rolesLectura = ['admin', 'farmacia'];
    protected static array $rolesEscritura = ['admin', 'farmacia'];
    protected static array $requiredOnCreate = ['nombre'];
}

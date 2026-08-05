<?php

class BillableServiceController extends CrudController
{
    protected static string $model = ServicioFacturable::class;
    protected static string $auditName = 'servicios_facturables';
    protected static array $rolesLectura = ['admin', 'facturacion', 'recepcion'];
    protected static array $rolesEscritura = ['admin', 'facturacion'];
    protected static array $requiredOnCreate = ['nombre', 'precio', 'modulo_origen'];
}

<?php

class PaymentMethodController extends CrudController
{
    protected static string $model = MetodoPago::class;
    protected static string $auditName = 'metodos_pago';
    protected static array $rolesLectura = ['admin', 'facturacion', 'recepcion'];
    protected static array $rolesEscritura = ['admin'];
    protected static array $requiredOnCreate = ['nombre'];
}

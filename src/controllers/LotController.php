<?php

class LotController extends CrudController
{
    protected static string $model = LoteInventario::class;
    protected static string $auditName = 'lotes_inventario';
    protected static array $rolesLectura = ['admin', 'farmacia'];
    protected static array $rolesEscritura = ['admin', 'farmacia'];
    protected static array $requiredOnCreate = ['medicamento_id', 'proveedor_id', 'numero_lote', 'cantidad', 'vencimiento'];
    protected static array $filterable = ['medicamento_id'];
}

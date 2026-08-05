<?php

class LoteInventario extends Model
{
    protected static string $table = 'lotes_inventario';
    protected static array $fillable = ['medicamento_id', 'proveedor_id', 'numero_lote', 'cantidad', 'vencimiento'];
}

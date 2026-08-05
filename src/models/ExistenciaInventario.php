<?php

class ExistenciaInventario extends Model
{
    protected static string $table = 'existencias_inventario';
    protected static array $fillable = ['articulo_id', 'cantidad'];
}

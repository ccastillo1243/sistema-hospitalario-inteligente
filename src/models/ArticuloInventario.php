<?php

class ArticuloInventario extends Model
{
    protected static string $table = 'articulos_inventario';
    protected static array $fillable = ['almacen_id', 'nombre', 'categoria', 'unidad_medida'];
}

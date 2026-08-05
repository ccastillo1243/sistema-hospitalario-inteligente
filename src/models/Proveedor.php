<?php

class Proveedor extends Model
{
    protected static string $table = 'proveedores';
    protected static array $fillable = ['nombre', 'telefono'];
}

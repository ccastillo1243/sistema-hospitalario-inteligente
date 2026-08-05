<?php

class Almacen extends Model
{
    protected static string $table = 'almacenes';
    protected static array $fillable = ['nombre', 'ubicacion'];
}

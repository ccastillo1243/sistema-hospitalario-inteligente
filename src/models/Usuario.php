<?php

class Usuario extends Model
{
    protected static string $table = 'usuarios';
    protected static bool $softDelete = true;
    protected static array $fillable = ['nombre', 'apellido', 'email', 'activo'];
}

<?php

class ServicioFacturable extends Model
{
    protected static string $table = 'servicios_facturables';
    protected static array $fillable = ['nombre', 'precio', 'modulo_origen'];
}

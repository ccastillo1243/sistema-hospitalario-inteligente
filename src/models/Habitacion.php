<?php

class Habitacion extends Model
{
    protected static string $table = 'habitaciones';
    protected static array $fillable = ['tipo_habitacion_id', 'numero', 'piso'];
}

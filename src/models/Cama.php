<?php

class Cama extends Model
{
    protected static string $table = 'camas';
    protected static array $fillable = ['habitacion_id', 'codigo', 'estado'];
}

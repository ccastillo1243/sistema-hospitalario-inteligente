<?php

class MuestraLaboratorio extends Model
{
    protected static string $table = 'muestras_laboratorio';
    protected static array $fillable = ['orden_id', 'tipo_examen_id', 'codigo_barras', 'tipo_muestra'];
}

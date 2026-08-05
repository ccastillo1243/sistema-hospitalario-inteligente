<?php

class ParametroExamen extends Model
{
    protected static string $table = 'parametros_examen';
    protected static array $fillable = ['tipo_examen_id', 'nombre', 'unidad', 'valor_referencia_minimo', 'valor_referencia_maximo'];
}

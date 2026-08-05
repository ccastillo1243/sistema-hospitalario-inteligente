<?php

class ResultadoLaboratorio extends Model
{
    protected static string $table = 'resultados_laboratorio';
    protected static array $fillable = ['muestra_id', 'parametro_id', 'valor'];
}

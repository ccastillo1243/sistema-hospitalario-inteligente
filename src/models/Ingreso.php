<?php

class Ingreso extends Model
{
    protected static string $table = 'ingresos';
    protected static array $fillable = ['paciente_id', 'cama_id', 'medico_id', 'motivo'];
}

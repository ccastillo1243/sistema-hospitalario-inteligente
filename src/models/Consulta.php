<?php

class Consulta extends Model
{
    protected static string $table = 'consultas';
    protected static array $fillable = ['cita_id', 'paciente_id', 'medico_id', 'motivo', 'notas', 'fecha'];
    protected static bool $softDelete = false;
}

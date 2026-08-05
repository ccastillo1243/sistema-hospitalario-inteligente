<?php

class Cita extends Model
{
    protected static string $table = 'citas';
    protected static array $fillable = ['paciente_id', 'medico_id', 'tipo_cita_id', 'fecha_hora', 'estado', 'motivo'];
}

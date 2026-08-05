<?php

class DisponibilidadMedico extends Model
{
    protected static string $table = 'disponibilidad_medico';
    protected static array $fillable = ['medico_id', 'dia_semana', 'hora_inicio', 'hora_fin'];
}

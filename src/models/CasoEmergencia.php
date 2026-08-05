<?php

class CasoEmergencia extends Model
{
    protected static string $table = 'casos_emergencia';
    protected static array $fillable = ['paciente_id', 'nivel_triage_id', 'motivo', 'estado'];
}

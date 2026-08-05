<?php

class RondaEnfermeria extends Model
{
    protected static string $table = 'rondas_enfermeria';
    protected static array $fillable = ['ingreso_id', 'enfermero_id', 'notas'];
}

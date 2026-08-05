<?php

class SignoVital extends Model
{
    protected static string $table = 'signos_vitales';
    protected static array $fillable = [
        'consulta_id', 'paciente_id', 'presion_arterial', 'frecuencia_cardiaca', 'temperatura', 'saturacion_o2',
    ];
}

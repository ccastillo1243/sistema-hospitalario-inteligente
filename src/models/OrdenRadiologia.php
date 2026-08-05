<?php

class OrdenRadiologia extends Model
{
    protected static string $table = 'ordenes_radiologia';
    protected static array $fillable = ['paciente_id', 'medico_id', 'tipo_estudio_id', 'estado'];
}

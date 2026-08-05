<?php

class OrdenLaboratorio extends Model
{
    protected static string $table = 'ordenes_laboratorio';
    protected static array $fillable = ['paciente_id', 'medico_id', 'estado'];
}

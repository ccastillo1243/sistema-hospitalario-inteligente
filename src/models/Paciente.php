<?php

class Paciente extends Model
{
    protected static string $table = 'pacientes';
    protected static bool $softDelete = true;
    protected static array $searchable = ['nombre', 'apellido', 'numero_expediente', 'documento_identidad'];
    protected static array $fillable = [
        'numero_expediente', 'nombre', 'apellido', 'fecha_nacimiento', 'genero',
        'documento_identidad', 'telefono', 'email', 'direccion', 'tipo_sangre',
    ];
}

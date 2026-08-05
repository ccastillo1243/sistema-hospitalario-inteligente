<?php

class Receta extends Model
{
    protected static string $table = 'recetas';
    protected static array $fillable = ['paciente_id', 'medico_id'];
}

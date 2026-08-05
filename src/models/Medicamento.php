<?php

class Medicamento extends Model
{
    protected static string $table = 'medicamentos';
    protected static array $fillable = ['categoria_id', 'nombre', 'presentacion'];
}

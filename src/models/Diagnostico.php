<?php

class Diagnostico extends Model
{
    protected static string $table = 'diagnosticos';
    protected static array $fillable = ['consulta_id', 'codigo_cie10', 'descripcion', 'tipo'];
}

<?php

class Alta extends Model
{
    protected static string $table = 'altas';
    protected static array $fillable = ['ingreso_id', 'resumen', 'tipo'];
}

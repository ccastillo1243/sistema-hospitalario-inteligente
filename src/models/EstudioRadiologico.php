<?php

class EstudioRadiologico extends Model
{
    protected static string $table = 'estudios_radiologicos';
    protected static array $fillable = ['orden_id', 'realizado_por', 'observaciones'];
}

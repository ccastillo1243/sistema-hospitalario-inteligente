<?php

class TipoCita extends Model
{
    protected static string $table = 'tipos_cita';
    protected static array $fillable = ['nombre', 'duracion_minutos'];
}

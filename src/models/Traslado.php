<?php

class Traslado extends Model
{
    protected static string $table = 'traslados';
    protected static array $fillable = ['ingreso_id', 'cama_destino_id', 'motivo'];
}

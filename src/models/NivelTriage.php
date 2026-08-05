<?php

class NivelTriage extends Model
{
    protected static string $table = 'niveles_triage';
    protected static array $fillable = ['nombre', 'prioridad'];
}

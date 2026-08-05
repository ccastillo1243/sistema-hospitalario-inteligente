<?php

class InformeRadiologico extends Model
{
    protected static string $table = 'informes_radiologicos';
    protected static array $fillable = ['estudio_id', 'radiologo_id', 'contenido'];
}

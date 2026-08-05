<?php

class Notificacion extends Model
{
    protected static string $table = 'notificaciones';
    protected static array $fillable = ['usuario_id', 'titulo', 'mensaje', 'leida'];
}

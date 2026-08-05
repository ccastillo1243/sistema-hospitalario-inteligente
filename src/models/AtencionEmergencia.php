<?php

class AtencionEmergencia extends Model
{
    protected static string $table = 'atenciones_emergencia';
    protected static array $fillable = ['caso_id', 'medico_id', 'notas'];
}

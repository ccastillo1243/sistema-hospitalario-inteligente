<?php

class Factura extends Model
{
    protected static string $table = 'facturas';
    protected static array $fillable = ['paciente_id', 'estado', 'subtotal', 'impuestos', 'total'];
}

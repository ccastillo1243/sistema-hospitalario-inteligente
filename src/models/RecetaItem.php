<?php

class RecetaItem extends Model
{
    protected static string $table = 'receta_items';
    protected static array $fillable = ['receta_id', 'medicamento_id', 'cantidad', 'indicaciones'];
}

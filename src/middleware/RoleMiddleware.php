<?php

class RoleMiddleware
{
    public static function handle(array $rolesPermitidos): void
    {
        if (!Auth::hasRole($rolesPermitidos)) {
            Response::error('No tienes permiso para acceder a este módulo', 403);
        }
    }
}

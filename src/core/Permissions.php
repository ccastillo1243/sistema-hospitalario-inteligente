<?php

/**
 * Mapa de qué roles pueden ver cada módulo, usado tanto para filtrar la
 * barra de navegación como para bloquear el acceso directo por URL.
 * Debe reflejar los mismos "rolesLectura" definidos en cada Controller.
 */
class Permissions
{
    private const MODULE_ROLES = [
        'inicio' => null, // null = todos los roles autenticados
        'dashboard' => null,
        'patients' => ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'],
        'staff' => ['admin', 'medico', 'enfermeria', 'recepcion', 'farmacia', 'laboratorio', 'radiologia', 'facturacion'],
        'appointments' => ['admin', 'medico', 'enfermeria', 'recepcion'],
        'hospitalization' => ['admin', 'medico', 'enfermeria', 'recepcion'],
        'laboratory' => ['admin', 'medico', 'enfermeria', 'laboratorio'],
        'pharmacy' => ['admin', 'medico', 'farmacia', 'enfermeria'],
        'inventory' => ['admin', 'farmacia', 'enfermeria'],
        'radiology' => ['admin', 'medico', 'radiologia'],
        'billing' => ['admin', 'facturacion', 'recepcion'],
        'emergency' => ['admin', 'medico', 'enfermeria', 'recepcion'],
        'users' => ['admin'],
        'audit' => ['admin'],
    ];

    public static function canAccess(string $moduleKey, array $userRoles): bool
    {
        $allowed = self::MODULE_ROLES[$moduleKey] ?? null;
        if ($allowed === null) {
            return true;
        }
        return count(array_intersect($allowed, $userRoles)) > 0;
    }
}

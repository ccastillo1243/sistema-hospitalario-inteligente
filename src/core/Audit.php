<?php

class Audit
{
    public static function log(string $tabla, string $registroId, string $accion, ?array $datos): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO bitacora_auditoria (usuario_id, tabla, registro_id, accion, datos_json) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            Auth::id(),
            $tabla,
            $registroId,
            $accion,
            $datos !== null ? json_encode($datos, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}

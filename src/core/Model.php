<?php

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static bool $softDelete = false;

    public static function all(int $page = 1, int $pageSize = 20, array $filters = []): array
    {
        $pdo = Database::connection();
        $page = max(1, $page);
        $pageSize = min(100, max(1, $pageSize));
        $offset = ($page - 1) * $pageSize;

        [$whereSql, $params] = static::buildWhere($filters);

        $countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM " . static::$table . " $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetch()['total'];

        $stmt = $pdo->prepare(
            "SELECT * FROM " . static::$table . " $whereSql ORDER BY " . static::$primaryKey . " DESC LIMIT $pageSize OFFSET $offset"
        );
        $stmt->execute($params);

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $where = static::$primaryKey . ' = ?';
        if (static::$softDelete) {
            $where .= ' AND eliminado_en IS NULL';
        }
        $stmt = $pdo->prepare("SELECT * FROM " . static::$table . " WHERE $where");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): array
    {
        $data = static::onlyFillable($data);
        $pdo = Database::connection();
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $stmt = $pdo->prepare(
            'INSERT INTO ' . static::$table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')'
        );
        $stmt->execute(array_values($data));

        return static::find((int) $pdo->lastInsertId());
    }

    public static function update(int $id, array $data): ?array
    {
        $data = static::onlyFillable($data);
        if (empty($data)) {
            return static::find($id);
        }

        $pdo = Database::connection();
        $set = implode(',', array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $pdo->prepare('UPDATE ' . static::$table . " SET $set WHERE " . static::$primaryKey . ' = ?');
        $stmt->execute([...array_values($data), $id]);

        return static::find($id);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::connection();
        if (static::$softDelete) {
            $pdo->prepare('UPDATE ' . static::$table . ' SET eliminado_en = NOW() WHERE ' . static::$primaryKey . ' = ?')
                ->execute([$id]);
        } else {
            $pdo->prepare('DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?')
                ->execute([$id]);
        }
    }

    protected static function onlyFillable(array $data): array
    {
        if (empty(static::$fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip(static::$fillable));
    }

    protected static function buildWhere(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (static::$softDelete) {
            $conditions[] = 'eliminado_en IS NULL';
        }

        foreach ($filters as $column => $value) {
            $conditions[] = "$column = ?";
            $params[] = $value;
        }

        $sql = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$sql, $params];
    }

    public static function table(): string
    {
        return static::$table;
    }
}

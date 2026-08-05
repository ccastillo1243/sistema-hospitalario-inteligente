<?php

abstract class CrudController
{
    /** @var class-string<Model> */
    protected static string $model;
    protected static string $auditName;
    protected static array $rolesLectura = [];
    protected static array $rolesEscritura = [];
    protected static array $requiredOnCreate = [];
    protected static array $filterable = [];

    public static function index(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesLectura);

        $page = (int) $request->query('page', 1);
        $pageSize = (int) $request->query('pageSize', 20);

        $filters = [];
        foreach (static::$filterable as $column) {
            $value = $request->query($column);
            if ($value !== null && $value !== '') {
                $filters[$column] = $value;
            }
        }

        Response::json(static::$model::all($page, $pageSize, $filters));
    }

    public static function show(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesLectura);

        $item = static::$model::find((int) $params['id']);
        if (!$item) {
            Response::error('Recurso no encontrado', 404);
        }
        Response::json($item);
    }

    public static function store(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesEscritura);
        static::requireCsrf($request);

        $data = $request->all();
        foreach (static::$requiredOnCreate as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                Response::error("El campo '$field' es requerido", 400);
            }
        }

        $item = static::$model::create($data);
        Audit::log(static::$auditName, (string) $item[array_key_first($item)], 'create', $item);

        Response::json($item, 201);
    }

    public static function update(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesEscritura);
        static::requireCsrf($request);

        $id = (int) $params['id'];
        if (!static::$model::find($id)) {
            Response::error('Recurso no encontrado', 404);
        }

        $item = static::$model::update($id, $request->all());
        Audit::log(static::$auditName, (string) $id, 'update', $item);

        Response::json($item);
    }

    public static function destroy(array $params, Request $request): void
    {
        AuthMiddleware::handle();
        RoleMiddleware::handle(static::$rolesEscritura);
        static::requireCsrf($request);

        $id = (int) $params['id'];
        if (!static::$model::find($id)) {
            Response::error('Recurso no encontrado', 404);
        }

        static::$model::delete($id);
        Audit::log(static::$auditName, (string) $id, 'delete', null);

        Response::json(['message' => 'Eliminado correctamente']);
    }

    protected static function requireCsrf(Request $request): void
    {
        if (!Csrf::validate($request)) {
            Response::error('Token CSRF inválido', 403);
        }
    }
}

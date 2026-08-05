<?php

class Request
{
    private array $body;

    public function __construct()
    {
        $raw = file_get_contents('php://input');
        $this->body = [];
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->body = $decoded;
            }
        }
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? ($_POST[$key] ?? $default);
    }

    public function all(): array
    {
        return array_merge($_POST, $this->body);
    }

    public function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$key] ?? null;
    }
}

<?php

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            Response::error('No autorizado', 401);
        }
    }
}

<?php

class Csrf
{
    public static function validate(Request $request): bool
    {
        $token = $request->header('X-CSRF-Token');
        return is_string($token) && hash_equals(Auth::csrfToken(), $token);
    }
}

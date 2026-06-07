<?php

class CSRF
{
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(CSRF::generateToken(), ENT_QUOTES, 'UTF-8')
        . '">';
}

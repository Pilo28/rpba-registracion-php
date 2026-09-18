<?php

declare(strict_types=1);

namespace App;

final class Auth
{
    public static function attempt(string $user, string $password): bool
    {
        $cfg = config();
        if ($cfg['admin_user'] === '' || $cfg['admin_password_hash'] === '') {
            return false;
        }

        if (!hash_equals($cfg['admin_user'], $user)) {
            return false;
        }

        return password_verify($password, $cfg['admin_password_hash']);
    }

    public static function login(string $user): void
    {
        Session::start();
        session_regenerate_id(true);
        $_SESSION['admin_user'] = $user;
    }

    public static function logout(): void
    {
        Session::start();
        unset($_SESSION['admin_user']);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        Session::start();
        return isset($_SESSION['admin_user']);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /admin/login');
            exit;
        }
    }
}

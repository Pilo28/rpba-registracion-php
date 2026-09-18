<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth;
use App\Csrf;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            header('Location: /admin');
            exit;
        }

        $this->render(null);
    }

    public function login(): void
    {
        $user = trim((string) ($_POST['user'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!Csrf::verify($_POST['csrf'] ?? null)) {
            $this->render('Sesión expirada, intentá de nuevo.');
            return;
        }

        if (!Auth::attempt($user, $password)) {
            $this->render('Usuario o contraseña incorrectos.');
            return;
        }

        Auth::login($user);
        header('Location: /admin');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /admin/login');
        exit;
    }

    private function render(?string $error): void
    {
        require __DIR__ . '/../../../views/admin/login.php';
    }
}

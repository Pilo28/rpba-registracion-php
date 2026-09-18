<?php

declare(strict_types=1);

/**
 * Carga .env (parser propio, sin dependencias) y expone la config como array.
 * Las variables ya definidas en el entorno del proceso tienen prioridad sobre el archivo.
 */
function load_env(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (getenv($key) === false) {
            putenv("$key=$value");
        }
    }
}

load_env(__DIR__ . '/.env');

function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

return [
    'db' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '5432'),
        'name' => env('DB_NAME', 'rpba_registracion'),
        'user' => env('DB_USER', 'postgres'),
        'password' => env('DB_PASSWORD', ''),
    ],
    'gemini_api_key' => env('GEMINI_API_KEY', ''),
    'site_url' => env('SITE_URL', ''),
    'admin_user' => env('ADMIN_USER', ''),
    'admin_password_hash' => env('ADMIN_PASSWORD_HASH', ''),
];

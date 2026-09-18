<?php

declare(strict_types=1);

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return rtrim($path, '/') ?: '/';
}

function nav_active(string $path): string
{
    return current_path() === rtrim($path, '/') ? 'is-active' : '';
}

/** @return array<string, mixed> */
function config(): array
{
    return $GLOBALS['__config'];
}

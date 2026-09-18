<?php

declare(strict_types=1);

namespace App;

final class AdminRender
{
    /** @param array<string, mixed> $data Variables disponibles en la vista. */
    public static function view(string $view, array $data, string $title): void
    {
        extract($data);

        ob_start();
        require __DIR__ . "/../views/admin/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . '/../views/admin/layout.php';
    }
}

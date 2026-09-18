<?php

declare(strict_types=1);

namespace App;

final class Render
{
    /**
     * @param array<string, mixed> $data     Variables disponibles en la vista (extraídas al scope local).
     * @param array{title: string, description: string, canonical: ?string} $seo
     */
    public static function view(string $view, array $data, array $seo): void
    {
        extract($data);

        ob_start();
        require __DIR__ . "/../views/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }
}

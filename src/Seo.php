<?php

declare(strict_types=1);

namespace App;

/**
 * Equivalente a SeoService (frontend/src/app/core/seo/seo.service.ts).
 * Como PHP ya renderiza en el servidor, alcanza con imprimir directo en <head>.
 */
final class Seo
{
    private const SITE_NAME = 'Registración';
    private const DESC_MAX = 160;

    public static function page(string $title, string $description, ?string $canonicalPath = null): array
    {
        $desc = mb_strlen($description) > self::DESC_MAX
            ? mb_substr($description, 0, self::DESC_MAX - 1) . '…'
            : $description;

        $siteUrl = config()['site_url'];
        $canonical = null;
        if ($canonicalPath !== null && $siteUrl !== '') {
            $canonical = rtrim($siteUrl, '/') . $canonicalPath;
        }

        return [
            'title' => "$title | " . self::SITE_NAME,
            'description' => $desc,
            'canonical' => $canonical,
        ];
    }

    public static function chapter(string $chapterTitle, string $description, ?string $codigoActo, string $slug): array
    {
        $fullTitle = $codigoActo !== null
            ? "$chapterTitle (Código $codigoActo)"
            : $chapterTitle;

        return self::page($fullTitle, $description, "/manual/$slug");
    }

    public static function home(): array
    {
        $desc = 'Manual de Registración. Guía práctica para inscriptores sobre calificación y registro de actos.';
        $siteUrl = config()['site_url'];

        return [
            'title' => self::SITE_NAME,
            'description' => $desc,
            'canonical' => $siteUrl !== '' ? rtrim($siteUrl, '/') . '/' : null,
        ];
    }
}

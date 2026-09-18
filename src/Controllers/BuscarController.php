<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Render;
use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;
use App\Seo;

final class BuscarController
{
    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $manual = new ManualRepository();
        $categorias = new CategoriaRepository();

        $resultados = $query !== '' ? $manual->buscar($query) : null;

        $estado = 'vacio';
        if ($query !== '') {
            $estado = ($resultados === null || count($resultados) === 0) ? 'sin-resultados' : 'con-resultados';
        }

        $categoriasPorId = [];
        foreach ($categorias->all() as $cat) {
            $categoriasPorId[$cat['id']] = $cat['titulo'];
        }

        Render::view('buscar', [
            'query' => $query,
            'resultados' => $resultados,
            'estado' => $estado,
            'categoriasPorId' => $categoriasPorId,
        ], Seo::page(
            'Buscador — Manual de Registración',
            'Buscá términos en el Manual de Registración: tipos de actos, documentos requeridos, códigos de acto y más.',
            '/buscar',
        ));
    }

    /**
     * Puerto directo de BuscarPageComponent.getSnippet(): recorta un fragmento
     * alrededor de la primera ocurrencia del término y lo resalta con <mark>,
     * escapando el HTML antes de envolver el término (mismo cuidado anti-XSS).
     */
    public static function snippet(array $ch, string $query): string
    {
        $q = trim($query);
        if ($q === '') {
            return '';
        }

        $fuentes = array_merge([$ch['descripcion']], $ch['documentos']);
        $texto = $ch['descripcion'];
        foreach ($fuentes as $fuente) {
            if (mb_stripos($fuente, $q) !== false) {
                $texto = $fuente;
                break;
            }
        }

        $idx = mb_stripos($texto, $q);
        if ($idx === false) {
            $fragmento = mb_substr($texto, 0, 160) . (mb_strlen($texto) > 160 ? '...' : '');
        } else {
            $start = max(0, $idx - 80);
            $end = min(mb_strlen($texto), $idx + mb_strlen($q) + 120);
            $fragmento = ($start > 0 ? '...' : '')
                . mb_substr($texto, $start, $end - $start)
                . ($end < mb_strlen($texto) ? '...' : '');
        }

        $escaped = htmlspecialchars($fragmento, ENT_QUOTES);
        $qPattern = preg_quote(htmlspecialchars($q, ENT_QUOTES), '/');

        return preg_replace("/($qPattern)/iu", '<mark>$1</mark>', $escaped);
    }
}

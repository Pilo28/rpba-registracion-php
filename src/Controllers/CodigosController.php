<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Render;
use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;
use App\Seo;

final class CodigosController
{
    public function index(): void
    {
        $manual = new ManualRepository();
        $categorias = new CategoriaRepository();

        $categoriasPorId = [];
        foreach ($categorias->all() as $cat) {
            $categoriasPorId[$cat['id']] = $cat['titulo'];
        }

        $todos = array_values(array_filter($manual->all(), fn ($c) => $c['codigoActo'] !== null));

        usort($todos, function ($a, $b) {
            $na = filter_var($a['codigoActo'], FILTER_VALIDATE_INT);
            $nb = filter_var($b['codigoActo'], FILTER_VALIDATE_INT);
            if ($na !== false && $nb !== false) {
                return $na <=> $nb;
            }
            return strcmp($a['codigoActo'], $b['codigoActo']);
        });

        Render::view('codigos', [
            'codigos' => $todos,
            'total' => count($todos),
            'categoriasPorId' => $categoriasPorId,
        ], Seo::page(
            'Índice por Código de Acto',
            'Tabla completa de actos registrales del Manual de Registración ordenados por código de acto. Filtrá por número o nombre para ir directamente al capítulo.',
            '/codigos',
        ));
    }
}

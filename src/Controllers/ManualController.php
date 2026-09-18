<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Render;
use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;
use App\Seo;

final class ManualController
{
    public function index(): void
    {
        $manual = new ManualRepository();
        $categorias = new CategoriaRepository();

        $categoriaActiva = $_GET['categoria'] ?? null;
        $todasCategorias = $categorias->all();
        $todosCapitulos = $manual->all();

        $categoriaActivaInfo = $categoriaActiva !== null ? $categorias->find($categoriaActiva) : null;

        $capitulosPorCategoria = [];
        foreach ($todasCategorias as $cat) {
            $capitulosPorCategoria[$cat['id']] = $manual->byCategoria($cat['id']);
        }

        $capitulosFiltrados = $categoriaActiva !== null
            ? ($capitulosPorCategoria[$categoriaActiva] ?? [])
            : $todosCapitulos;

        $seo = $categoriaActivaInfo
            ? Seo::page($categoriaActivaInfo['titulo'], $categoriaActivaInfo['descripcion'], '/manual')
            : Seo::page(
                'Índice del Manual',
                'Índice completo de los 65 capítulos del Manual de Registración, organizados por categorías: dominio, hipoteca, embargos, propiedad horizontal y más.',
                '/manual',
            );

        Render::view('manual-index', [
            'categorias' => $todasCategorias,
            'todos' => $todosCapitulos,
            'categoriaActiva' => $categoriaActiva,
            'categoriaActivaInfo' => $categoriaActivaInfo,
            'capitulosFiltrados' => $capitulosFiltrados,
            'capitulosPorCategoria' => $capitulosPorCategoria,
        ], $seo);
    }

    public function show(string $slug): void
    {
        $manual = new ManualRepository();
        $categorias = new CategoriaRepository();

        $chapter = $manual->findBySlug($slug);

        if ($chapter === null) {
            Render::view('chapter', [
                'chapter' => null,
                'slug' => $slug,
                'catInfo' => null,
                'prevNext' => ['prev' => null, 'next' => null],
            ], Seo::page('Capítulo no encontrado', 'El capítulo solicitado no existe en el Manual de Registración.'));
            return;
        }

        $catInfo = $categorias->find($chapter['categoria']);
        $prevNext = $manual->prevNext($slug);

        Render::view('chapter', [
            'chapter' => $chapter,
            'slug' => $slug,
            'catInfo' => $catInfo,
            'prevNext' => $prevNext,
        ], Seo::chapter($chapter['titulo'], $chapter['descripcion'], $chapter['codigoActo'], $chapter['slug']));
    }
}

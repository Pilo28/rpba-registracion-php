<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Render;
use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;
use App\Seo;

final class HomeController
{
    private const CATEGORIA_META = [
        'dominio' => ['icon' => 'D', 'color' => '#1a3a6b'],
        'hipoteca' => ['icon' => 'H', 'color' => '#2b5777'],
        'usufructo' => ['icon' => 'U', 'color' => '#276749'],
        'propiedad-horizontal' => ['icon' => 'PH', 'color' => '#553c9a'],
        'cautelares' => ['icon' => 'C', 'color' => '#7b341e'],
        'vivienda' => ['icon' => 'V', 'color' => '#2c5f8a'],
        'fideicomiso' => ['icon' => 'F', 'color' => '#2d3748'],
        'ley24374' => ['icon' => 'L', 'color' => '#744210'],
        'testimonios' => ['icon' => 'T', 'color' => '#4a5568'],
        'inscripciones' => ['icon' => 'I', 'color' => '#2c5282'],
        'anotaciones' => ['icon' => 'A', 'color' => '#5f3a6e'],
        'general' => ['icon' => 'G', 'color' => '#1a202c'],
    ];

    private const DESTACADOS = [
        'compraventa',
        'hipoteca-constitucion',
        'hipoteca-cancelacion',
        'embargo',
        'usufructo-constitucion',
        'ph-afectacion',
        'donacion',
        'declaratoria-herederos-notarial',
    ];

    public function index(): void
    {
        $manual = new ManualRepository();
        $categorias = new CategoriaRepository();

        $todasCategorias = array_filter($categorias->all(), fn ($c) => $c['id'] !== 'general');
        $todosCapitulos = $manual->all();
        $porCategoria = [];
        foreach ($todasCategorias as $cat) {
            $porCategoria[$cat['id']] = count(array_filter($todosCapitulos, fn ($c) => $c['categoria'] === $cat['id']));
        }

        $destacados = array_map(
            fn (string $slug) => $manual->findBySlug($slug),
            self::DESTACADOS,
        );

        Render::view('home', [
            'categorias' => array_values($todasCategorias),
            'totalCapitulos' => count($todosCapitulos),
            'porCategoria' => $porCategoria,
            'categoriaMeta' => self::CATEGORIA_META,
            'destacados' => array_filter($destacados),
        ], Seo::home());
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Render;
use App\Seo;

final class AsistenteController
{
    public function index(): void
    {
        $seo = Seo::page(
            'Asistente IA',
            'Consultá dudas sobre el Manual de Registración con nuestro asistente de inteligencia artificial. Obtendrás respuestas sobre requisitos documentales y trámites registrales.',
        );
        // No indexar el chat — contenido dinámico sin valor SEO
        $seo['robots'] = 'noindex, nofollow';

        Render::view('asistente', [], $seo);
    }
}

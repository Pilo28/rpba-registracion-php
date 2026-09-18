<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Render;
use App\Seo;

final class NotFoundController
{
    public function show(): void
    {
        Render::view('404', [], Seo::page('Página no encontrada', ''));
    }
}

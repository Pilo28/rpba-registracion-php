<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\AdminRender;
use App\Auth;
use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;

final class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();

        $totalCapitulos = count((new ManualRepository())->all());
        $totalCategorias = count((new CategoriaRepository())->all());

        AdminRender::view('dashboard', [
            'totalCapitulos' => $totalCapitulos,
            'totalCategorias' => $totalCategorias,
        ], 'Inicio');
    }
}

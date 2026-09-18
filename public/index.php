<?php

declare(strict_types=1);

// Con el servidor embebido (`php -S`), este script actúa de router: si la URL
// pide un archivo real (assets, api/asistente.php), lo servimos tal cual.
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $file = __DIR__ . $requestPath;
    if ($requestPath !== '/' && is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../bootstrap.php';

use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\CapitulosController;
use App\Controllers\Admin\CategoriasController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\AsistenteController;
use App\Controllers\BuscarController;
use App\Controllers\CodigosController;
use App\Controllers\HomeController;
use App\Controllers\ManualController;
use App\Router;

$router = new Router();

$router->get('/', fn () => (new HomeController())->index());
$router->get('/manual', fn () => (new ManualController())->index());
$router->get('/manual/{slug}', fn (array $p) => (new ManualController())->show($p['slug']));
$router->get('/buscar', fn () => (new BuscarController())->index());
$router->get('/codigos', fn () => (new CodigosController())->index());
$router->get('/asistente', fn () => (new AsistenteController())->index());

// Panel de administración
$router->get('/admin/login', fn () => (new AuthController())->showLogin());
$router->post('/admin/login', fn () => (new AuthController())->login());
$router->post('/admin/logout', fn () => (new AuthController())->logout());
$router->get('/admin', fn () => (new DashboardController())->index());

$router->get('/admin/capitulos', fn () => (new CapitulosController())->index());
$router->get('/admin/capitulos/nuevo', fn () => (new CapitulosController())->create());
$router->post('/admin/capitulos', fn () => (new CapitulosController())->store());
$router->get('/admin/capitulos/{id}/editar', fn (array $p) => (new CapitulosController())->edit($p));
$router->post('/admin/capitulos/{id}', fn (array $p) => (new CapitulosController())->update($p));
$router->post('/admin/capitulos/{id}/eliminar', fn (array $p) => (new CapitulosController())->destroy($p));

$router->get('/admin/categorias', fn () => (new CategoriasController())->index());
$router->get('/admin/categorias/nueva', fn () => (new CategoriasController())->create());
$router->post('/admin/categorias', fn () => (new CategoriasController())->store());
$router->get('/admin/categorias/{id}/editar', fn (array $p) => (new CategoriasController())->edit($p));
$router->post('/admin/categorias/{id}', fn (array $p) => (new CategoriasController())->update($p));
$router->post('/admin/categorias/{id}/eliminar', fn (array $p) => (new CategoriasController())->destroy($p));

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

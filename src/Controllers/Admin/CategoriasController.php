<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\AdminRender;
use App\Auth;
use App\Csrf;
use App\Repositories\CategoriaRepository;

final class CategoriasController
{
    public function index(): void
    {
        Auth::requireLogin();

        AdminRender::view('categorias-index', [
            'categorias' => (new CategoriaRepository())->all(),
        ], 'Categorías');
    }

    public function create(): void
    {
        Auth::requireLogin();
        $categorias = new CategoriaRepository();

        AdminRender::view('categoria-form', [
            'categoria' => ['id' => '', 'titulo' => '', 'descripcion' => '', 'orden' => $categorias->nextOrden()],
            'errors' => [],
            'isEdit' => false,
            'ordenesUsados' => $categorias->all(),
        ], 'Nueva categoría');
    }

    public function store(): void
    {
        Auth::requireLogin();
        $this->save(null);
    }

    public function edit(array $params): void
    {
        Auth::requireLogin();
        $categorias = new CategoriaRepository();
        $categoria = $categorias->find($params['id']);

        if ($categoria === null) {
            http_response_code(404);
            echo 'Categoría no encontrada.';
            return;
        }

        AdminRender::view('categoria-form', [
            'categoria' => $categoria,
            'errors' => [],
            'isEdit' => true,
            'formActionId' => $categoria['id'],
            'ordenesUsados' => $categorias->all(),
        ], 'Editar categoría');
    }

    public function update(array $params): void
    {
        Auth::requireLogin();
        $this->save($params['id']);
    }

    public function destroy(array $params): void
    {
        Auth::requireLogin();

        if (Csrf::verify($_POST['csrf'] ?? null)) {
            try {
                (new CategoriaRepository())->delete($params['id']);
            } catch (\RuntimeException $e) {
                $_SESSION['admin_flash_error'] = $e->getMessage();
            }
        }

        header('Location: /admin/categorias');
        exit;
    }

    private function save(?string $existingId): void
    {
        $categorias = new CategoriaRepository();

        $isNew = $existingId === null;
        $id = trim((string) ($_POST['id'] ?? ''));
        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $ordenRaw = (string) ($_POST['orden'] ?? '');

        // Para "conflicto conmigo mismo": en alta no hay "yo mismo" (cualquier
        // coincidencia es un duplicado real); en edición es el id viejo (el
        // que la fila tiene hoy en la base).
        $selfId = $isNew ? null : $existingId;

        $errors = [];
        if (!Csrf::verify($_POST['csrf'] ?? null)) {
            $errors[] = 'Sesión expirada, volvé a intentar.';
        }
        if ($id === '' || !preg_match('/^[a-z0-9-]+$/', $id)) {
            $errors[] = 'El id debe usar solo minúsculas, números y guiones (ej: "propiedad-horizontal").';
        } else {
            $existing = $categorias->find($id);
            if ($existing !== null && $existing['id'] !== $selfId) {
                $errors[] = "Ya existe una categoría con id \"$id\".";
            }
        }
        if ($titulo === '') {
            $errors[] = 'El título es obligatorio.';
        }
        if ($descripcion === '') {
            $errors[] = 'La descripción es obligatoria.';
        }

        if (!ctype_digit($ordenRaw)) {
            $errors[] = 'El orden debe ser un número.';
            $orden = $categorias->nextOrden();
        } else {
            $orden = (int) $ordenRaw;
            $conflictId = $categorias->ordenTakenBy($orden);
            // Quedarse con el mismo orden que ya tenía la propia categoría no es un conflicto.
            if ($conflictId !== null && $conflictId !== $selfId) {
                $conflictCat = $categorias->find($conflictId);
                $errors[] = "El orden $orden ya lo usa \"{$conflictCat['titulo']}\". Elegí otro número (el siguiente libre es {$categorias->nextOrden()}).";
            }
        }

        if ($errors !== []) {
            AdminRender::view('categoria-form', [
                'categoria' => ['id' => $id, 'titulo' => $titulo, 'descripcion' => $descripcion, 'orden' => $orden],
                'errors' => $errors,
                'isEdit' => !$isNew,
                'formActionId' => $existingId,
                'ordenesUsados' => $categorias->all(),
            ], $isNew ? 'Nueva categoría' : 'Editar categoría');
            return;
        }

        if ($isNew) {
            $categorias->create($id, $titulo, $descripcion, $orden);
        } else {
            $categorias->update($existingId, $id, $titulo, $descripcion, $orden);
        }

        header('Location: /admin/categorias');
        exit;
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\AdminRender;
use App\Auth;
use App\Csrf;
use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;

final class CapitulosController
{
    private const CLASIFICACIONES = ['Notarial', 'Judicial', 'Administrativo'];

    public function index(): void
    {
        Auth::requireLogin();

        $categoriasPorId = [];
        foreach ((new CategoriaRepository())->all() as $cat) {
            $categoriasPorId[$cat['id']] = $cat['titulo'];
        }

        AdminRender::view('capitulos-index', [
            'capitulos' => (new ManualRepository())->all(),
            'categoriasPorId' => $categoriasPorId,
        ], 'Capítulos');
    }

    public function create(): void
    {
        Auth::requireLogin();

        $this->renderForm(null, [], false);
    }

    public function store(): void
    {
        Auth::requireLogin();
        $this->save(null);
    }

    public function edit(array $params): void
    {
        Auth::requireLogin();
        $capitulo = (new ManualRepository())->findById((int) $params['id']);

        if ($capitulo === null) {
            http_response_code(404);
            echo 'Capítulo no encontrado.';
            return;
        }

        $this->renderForm($capitulo, [], true);
    }

    public function update(array $params): void
    {
        Auth::requireLogin();
        $this->save((int) $params['id']);
    }

    public function destroy(array $params): void
    {
        Auth::requireLogin();

        if (Csrf::verify($_POST['csrf'] ?? null)) {
            (new ManualRepository())->delete((int) $params['id']);
        }

        header('Location: /admin/capitulos');
        exit;
    }

    private function save(?int $id): void
    {
        $manual = new ManualRepository();
        $isNew = $id === null;

        $data = [
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'titulo' => trim((string) ($_POST['titulo'] ?? '')),
            'codigoActo' => trim((string) ($_POST['codigo_acto'] ?? '')),
            'categoria' => (string) ($_POST['categoria'] ?? ''),
            'pagina' => (string) ($_POST['pagina'] ?? ''),
            'clasificacionIngreso' => array_values(array_intersect((array) ($_POST['clasificacion'] ?? []), self::CLASIFICACIONES)),
            'art2Ley17801' => trim((string) ($_POST['art2_ley17801'] ?? '')),
            'rubrosFolioReal' => $this->linesToArray((string) ($_POST['rubros_folio_real'] ?? '')),
            'requiereCertDominio' => isset($_POST['requiere_cert_dominio']),
            'requiereInscProvisional' => isset($_POST['requiere_insc_provisional']),
            'requiereCertCatastral' => $this->parseTriState((string) ($_POST['requiere_cert_catastral'] ?? 'nd')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'documentos' => $this->linesToArray((string) ($_POST['documentos'] ?? '')),
            'modeloAsiento' => trim((string) ($_POST['modelo_asiento'] ?? '')),
            'observaciones' => trim((string) ($_POST['observaciones'] ?? '')),
        ];

        $errors = $this->validate($data, $isNew, $id);

        if (!Csrf::verify($_POST['csrf'] ?? null)) {
            $errors[] = 'Sesión expirada, volvé a intentar.';
        }

        if ($errors !== []) {
            $data['pagina'] = $data['pagina'] !== '' ? (int) $data['pagina'] : '';
            $this->renderForm($data, $errors, !$isNew);
            return;
        }

        $data['pagina'] = (int) $data['pagina'];

        if ($isNew) {
            $manual->create($data);
        } else {
            $manual->update($id, $data);
        }

        header('Location: /admin/capitulos');
        exit;
    }

    /** @param array<string, mixed> $data */
    private function validate(array $data, bool $isNew, ?int $id): array
    {
        $errors = [];
        $manual = new ManualRepository();

        if ($data['slug'] === '' || !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $errors[] = 'El slug debe usar solo minúsculas, números y guiones.';
        } else {
            $existing = $manual->findBySlug($data['slug']);
            if ($existing !== null && ($isNew || $existing['id'] !== $id)) {
                $errors[] = "Ya existe un capítulo con slug \"{$data['slug']}\".";
            }
        }
        if ($data['titulo'] === '') {
            $errors[] = 'El título es obligatorio.';
        }
        if ($data['categoria'] === '' || (new CategoriaRepository())->find($data['categoria']) === null) {
            $errors[] = 'Elegí una categoría válida.';
        }
        if ($data['pagina'] === '' || !ctype_digit($data['pagina'])) {
            $errors[] = 'La página debe ser un número.';
        }
        if ($data['art2Ley17801'] === '') {
            $errors[] = 'Completá "Art. 2 Ley 17.801".';
        }
        if ($data['rubrosFolioReal'] === []) {
            $errors[] = 'Ingresá al menos un rubro del folio real.';
        }
        if ($data['descripcion'] === '') {
            $errors[] = 'La descripción es obligatoria.';
        }
        if ($data['documentos'] === []) {
            $errors[] = 'Ingresá al menos un documento requerido.';
        }
        if ($data['modeloAsiento'] === '') {
            $errors[] = 'El modelo de asiento es obligatorio.';
        }

        return $errors;
    }

    /** @param array<string, mixed>|null $capitulo */
    private function renderForm(?array $capitulo, array $errors, bool $isEdit): void
    {
        AdminRender::view('capitulo-form', [
            'capitulo' => $capitulo,
            'errors' => $errors,
            'isEdit' => $isEdit,
            'categorias' => (new CategoriaRepository())->all(),
            'clasificacionesDisponibles' => self::CLASIFICACIONES,
        ], $isEdit ? 'Editar capítulo' : 'Nuevo capítulo');
    }

    /** @return string[] */
    private function linesToArray(string $raw): array
    {
        $lines = array_map('trim', explode("\n", str_replace("\r", '', $raw)));
        return array_values(array_filter($lines, fn (string $l) => $l !== ''));
    }

    private function parseTriState(string $value): ?bool
    {
        return match ($value) {
            'si' => true,
            'no' => false,
            default => null,
        };
    }
}

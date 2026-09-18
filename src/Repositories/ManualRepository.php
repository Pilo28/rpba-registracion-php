<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use App\PgArray;
use PDO;

/**
 * Equivalente a ManualService (frontend/src/app/features/manual/manual.service.ts).
 */
final class ManualRepository
{
    private const COLUMNS = 'id, slug, titulo, codigo_acto, categoria_id, pagina,
        clasificacion_ingreso, art2_ley17801, rubros_folio_real,
        requiere_cert_dominio, requiere_insc_provisional, requiere_cert_catastral,
        descripcion, documentos, modelo_asiento, observaciones';

    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $stmt = Database::connection()->query(
            'SELECT ' . self::COLUMNS . ' FROM capitulos ORDER BY orden',
        );
        return array_map([$this, 'mapRow'], $stmt->fetchAll());
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT ' . self::COLUMNS . ' FROM capitulos WHERE slug = :slug',
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->mapRow($row);
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT ' . self::COLUMNS . ' FROM capitulos WHERE id = :id',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->mapRow($row);
    }

    /** @return array<int, array<string, mixed>> */
    public function byCategoria(string $categoriaId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT ' . self::COLUMNS . ' FROM capitulos WHERE categoria_id = :cat ORDER BY orden',
        );
        $stmt->execute(['cat' => $categoriaId]);
        return array_map([$this, 'mapRow'], $stmt->fetchAll());
    }

    /**
     * Replica ManualService.buscar(): substring case-insensitive sobre
     * título, código de acto, descripción y cada documento.
     * @return array<int, array<string, mixed>>
     */
    public function buscar(string $query): array
    {
        $q = trim($query);
        if ($q === '') {
            return [];
        }

        $stmt = Database::connection()->prepare(
            'SELECT ' . self::COLUMNS . ' FROM capitulos
             WHERE titulo ILIKE :q
                OR codigo_acto ILIKE :q
                OR descripcion ILIKE :q
                OR EXISTS (SELECT 1 FROM unnest(documentos) d WHERE d ILIKE :q)
             ORDER BY orden',
        );
        $stmt->execute(['q' => '%' . $q . '%']);
        return array_map([$this, 'mapRow'], $stmt->fetchAll());
    }

    /** @return array{prev: ?array<string, mixed>, next: ?array<string, mixed>} */
    public function prevNext(string $slug): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT orden FROM capitulos WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $orden = $stmt->fetchColumn();

        if ($orden === false) {
            return ['prev' => null, 'next' => null];
        }

        $prevStmt = $pdo->prepare(
            'SELECT ' . self::COLUMNS . ' FROM capitulos WHERE orden < :o ORDER BY orden DESC LIMIT 1',
        );
        $prevStmt->execute(['o' => $orden]);
        $prevRow = $prevStmt->fetch();

        $nextStmt = $pdo->prepare(
            'SELECT ' . self::COLUMNS . ' FROM capitulos WHERE orden > :o ORDER BY orden ASC LIMIT 1',
        );
        $nextStmt->execute(['o' => $orden]);
        $nextRow = $nextStmt->fetch();

        return [
            'prev' => $prevRow === false ? null : $this->mapRow($prevRow),
            'next' => $nextRow === false ? null : $this->mapRow($nextRow),
        ];
    }

    /**
     * @param array<string, mixed> $data Claves con la misma forma que mapRow() (camelCase).
     */
    public function create(array $data): int
    {
        $pdo = Database::connection();
        $orden = (int) $pdo->query('SELECT COALESCE(MAX(orden), -1) + 1 FROM capitulos')->fetchColumn();

        $stmt = $pdo->prepare(
            'INSERT INTO capitulos (
                slug, titulo, codigo_acto, categoria_id, pagina,
                clasificacion_ingreso, art2_ley17801, rubros_folio_real,
                requiere_cert_dominio, requiere_insc_provisional, requiere_cert_catastral,
                descripcion, documentos, modelo_asiento, observaciones, orden
            ) VALUES (
                :slug, :titulo, :codigo_acto, :categoria_id, :pagina,
                :clasificacion_ingreso, :art2_ley17801, :rubros_folio_real,
                :requiere_cert_dominio, :requiere_insc_provisional, :requiere_cert_catastral,
                :descripcion, :documentos, :modelo_asiento, :observaciones, :orden
            ) RETURNING id',
        );
        $stmt->execute($this->bindParams($data) + ['orden' => $orden]);

        return (int) $stmt->fetchColumn();
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE capitulos SET
                slug = :slug, titulo = :titulo, codigo_acto = :codigo_acto,
                categoria_id = :categoria_id, pagina = :pagina,
                clasificacion_ingreso = :clasificacion_ingreso, art2_ley17801 = :art2_ley17801,
                rubros_folio_real = :rubros_folio_real,
                requiere_cert_dominio = :requiere_cert_dominio,
                requiere_insc_provisional = :requiere_insc_provisional,
                requiere_cert_catastral = :requiere_cert_catastral,
                descripcion = :descripcion, documentos = :documentos,
                modelo_asiento = :modelo_asiento, observaciones = :observaciones
             WHERE id = :id',
        );
        $stmt->execute($this->bindParams($data) + ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM capitulos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @param array<string, mixed> $data */
    private function bindParams(array $data): array
    {
        return [
            'slug' => $data['slug'],
            'titulo' => $data['titulo'],
            'codigo_acto' => $data['codigoActo'] !== '' ? $data['codigoActo'] : null,
            'categoria_id' => $data['categoria'],
            'pagina' => $data['pagina'],
            'clasificacion_ingreso' => PgArray::encode($data['clasificacionIngreso']),
            'art2_ley17801' => $data['art2Ley17801'],
            'rubros_folio_real' => PgArray::encode($data['rubrosFolioReal']),
            'requiere_cert_dominio' => $data['requiereCertDominio'] ? 't' : 'f',
            'requiere_insc_provisional' => $data['requiereInscProvisional'] ? 't' : 'f',
            'requiere_cert_catastral' => $data['requiereCertCatastral'] === null
                ? null
                : ($data['requiereCertCatastral'] ? 't' : 'f'),
            'descripcion' => $data['descripcion'],
            'documentos' => PgArray::encode($data['documentos']),
            'modelo_asiento' => $data['modeloAsiento'],
            'observaciones' => $data['observaciones'] !== '' ? $data['observaciones'] : null,
        ];
    }

    /** @param array<string, mixed> $row */
    private function mapRow(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'slug' => $row['slug'],
            'titulo' => $row['titulo'],
            'codigoActo' => $row['codigo_acto'],
            'categoria' => $row['categoria_id'],
            'pagina' => (int) $row['pagina'],
            'clasificacionIngreso' => PgArray::decode($row['clasificacion_ingreso']),
            'art2Ley17801' => $row['art2_ley17801'],
            'rubrosFolioReal' => PgArray::decode($row['rubros_folio_real']),
            'requiereCertDominio' => $this->toBool($row['requiere_cert_dominio']),
            'requiereInscProvisional' => $this->toBool($row['requiere_insc_provisional']),
            'requiereCertCatastral' => $row['requiere_cert_catastral'] === null
                ? null
                : $this->toBool($row['requiere_cert_catastral']),
            'descripcion' => $row['descripcion'],
            'documentos' => PgArray::decode($row['documentos']),
            'modeloAsiento' => $row['modelo_asiento'],
            'observaciones' => $row['observaciones'],
        ];
    }

    private function toBool(mixed $value): bool
    {
        return $value === true || $value === 't' || $value === '1' || $value === 1;
    }
}

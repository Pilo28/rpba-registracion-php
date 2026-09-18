<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;

final class CategoriaRepository
{
    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        $stmt = Database::connection()->query(
            'SELECT id, titulo, descripcion, orden FROM categorias ORDER BY orden',
        );
        return $stmt->fetchAll();
    }

    public function find(string $id): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, titulo, descripcion, orden FROM categorias WHERE id = :id',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function create(string $id, string $titulo, string $descripcion, int $orden): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO categorias (id, titulo, descripcion, orden) VALUES (:id, :titulo, :descripcion, :orden)',
        );
        $stmt->execute(['id' => $id, 'titulo' => $titulo, 'descripcion' => $descripcion, 'orden' => $orden]);
    }

    /**
     * Si $newId difiere de $oldId, renombra el id de la categoría. La FK de
     * capitulos.categoria_id tiene ON UPDATE CASCADE, así que Postgres
     * reasigna solo todos los capítulos que apuntaban al id viejo.
     */
    public function update(string $oldId, string $newId, string $titulo, string $descripcion, int $orden): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE categorias SET id = :newId, titulo = :titulo, descripcion = :descripcion, orden = :orden WHERE id = :oldId',
        );
        $stmt->execute([
            'newId' => $newId,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'orden' => $orden,
            'oldId' => $oldId,
        ]);
    }

    /** @throws \RuntimeException si la categoría tiene capítulos asociados */
    public function delete(string $id): void
    {
        try {
            $stmt = Database::connection()->prepare('DELETE FROM categorias WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23503') {
                throw new \RuntimeException('No se puede borrar: todavía tiene capítulos asociados.');
            }
            throw $e;
        }
    }

    public function nextOrden(): int
    {
        return (int) Database::connection()
            ->query('SELECT COALESCE(MAX(orden), -1) + 1 FROM categorias')
            ->fetchColumn();
    }

    /** @return string|null El id de la categoría que ya usa ese orden, o null si está libre. */
    public function ordenTakenBy(int $orden): ?string
    {
        $stmt = Database::connection()->prepare('SELECT id FROM categorias WHERE orden = :orden');
        $stmt->execute(['orden' => $orden]);
        $id = $stmt->fetchColumn();
        return $id === false ? null : $id;
    }
}

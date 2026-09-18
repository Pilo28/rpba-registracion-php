<?php

declare(strict_types=1);

// Puebla la base de datos desde database/seed/manual-data.json (generado por
// tools/export-manual-data.mjs). Idempotente: trunca antes de insertar.
//
// Uso: php php/database/seed.php

require __DIR__ . '/../bootstrap.php';

use App\Database;
use App\PgArray;

$jsonPath = __DIR__ . '/seed/manual-data.json';
if (!is_file($jsonPath)) {
    fwrite(STDERR, "No existe $jsonPath. Corré primero: npx tsx php/tools/export-manual-data.mjs\n");
    exit(1);
}

$data = json_decode(file_get_contents($jsonPath), true, flags: JSON_THROW_ON_ERROR);

$pdo = Database::connection();
$pdo->beginTransaction();

try {
    $pdo->exec('TRUNCATE capitulos, categorias RESTART IDENTITY CASCADE');

    $insertCategoria = $pdo->prepare(
        'INSERT INTO categorias (id, titulo, descripcion, orden) VALUES (:id, :titulo, :descripcion, :orden)',
    );
    foreach ($data['categorias'] as $cat) {
        $insertCategoria->execute([
            'id' => $cat['id'],
            'titulo' => $cat['titulo'],
            'descripcion' => $cat['descripcion'],
            'orden' => $cat['orden'],
        ]);
    }

    $insertCapitulo = $pdo->prepare(
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
        )',
    );

    foreach ($data['capitulos'] as $ch) {
        $insertCapitulo->execute([
            'slug' => $ch['slug'],
            'titulo' => $ch['titulo'],
            'codigo_acto' => $ch['codigoActo'],
            'categoria_id' => $ch['categoria'],
            'pagina' => $ch['pagina'],
            'clasificacion_ingreso' => PgArray::encode($ch['clasificacionIngreso']),
            'art2_ley17801' => $ch['art2Ley17801'],
            'rubros_folio_real' => PgArray::encode($ch['rubrosFolioReal']),
            'requiere_cert_dominio' => $ch['requiereCertDominio'] ? 't' : 'f',
            'requiere_insc_provisional' => $ch['requiereInscProvisional'] ? 't' : 'f',
            'requiere_cert_catastral' => $ch['requiereCertCatastral'] === null
                ? null
                : ($ch['requiereCertCatastral'] ? 't' : 'f'),
            'descripcion' => $ch['descripcion'],
            'documentos' => PgArray::encode($ch['documentos']),
            'modelo_asiento' => $ch['modeloAsiento'],
            'observaciones' => $ch['observaciones'] ?? null,
            'orden' => $ch['orden'],
        ]);
    }

    $pdo->commit();

    printf(
        "Seed OK: %d categorías, %d capítulos.\n",
        count($data['categorias']),
        count($data['capitulos']),
    );
} catch (\Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, 'Error al poblar la base: ' . $e->getMessage() . "\n");
    exit(1);
}

// Script de migración de una sola vez: exporta los datos hardcodeados en
// frontend/src/data/{manual-chapters,types}.ts a un JSON que después
// php/database/seed.php usa para poblar Postgres.
//
// Uso (desde la raíz del repo, no requiere tocar frontend/):
//   npx tsx php/tools/export-manual-data.mjs

import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

import { MANUAL_CHAPTERS } from '../../frontend/src/data/manual-chapters.ts';
import { CATEGORIAS } from '../../frontend/src/data/types.ts';

const __dirname = dirname(fileURLToPath(import.meta.url));

const categorias = CATEGORIAS.map((c, orden) => ({ ...c, orden }));
const capitulos = MANUAL_CHAPTERS.map((c, orden) => ({ ...c, orden }));

const outPath = resolve(__dirname, '../database/seed/manual-data.json');
writeFileSync(outPath, JSON.stringify({ categorias, capitulos }, null, 2));

console.log(`Exportado: ${categorias.length} categorías, ${capitulos.length} capítulos -> ${outPath}`);

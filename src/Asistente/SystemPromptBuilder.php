<?php

declare(strict_types=1);

namespace App\Asistente;

use App\Repositories\CategoriaRepository;
use App\Repositories\ManualRepository;

/**
 * Arma el system prompt del asistente en cada request, consultando la base
 * en vivo — así cualquier alta/edición/baja hecha desde el panel de admin
 * se refleja de inmediato en lo que el asistente "sabe", sin tocar código.
 */
final class SystemPromptBuilder
{
    public static function build(): string
    {
        return self::head() . self::estructuraDinamica() . self::tail();
    }

    private static function estructuraDinamica(): string
    {
        $categorias = (new CategoriaRepository())->all();
        $capitulos = (new ManualRepository())->all();

        $porCategoria = [];
        foreach ($capitulos as $ch) {
            $porCategoria[$ch['categoria']][] = $ch;
        }

        $lines = ['## ESTRUCTURA DEL MANUAL', ''];

        foreach ($categorias as $cat) {
            $chapters = $porCategoria[$cat['id']] ?? [];
            if ($chapters === []) {
                continue;
            }

            $lines[] = "### {$cat['titulo']}";
            foreach ($chapters as $ch) {
                $codigo = $ch['codigoActo'] !== null ? " — Código {$ch['codigoActo']}" : '';
                $lines[] = "- {$ch['titulo']} (pág. {$ch['pagina']}){$codigo}";
            }
            $lines[] = '';
        }

        return implode("\n", $lines) . "\n";
    }

    private static function head(): string
    {
        return <<<'TXT'
Sos un asistente especializado en el Manual de Registración, 2ª edición actualizada enero 2024, de Agustín Galmarini Toia y Romina Andrea Rivas.

Tu rol es ayudar a inscriptores y profesionales del derecho a entender los requisitos, procedimientos y normativa registral de la provincia de Buenos Aires.

## ABREVIATURAS CLAVE
- CCCN: Código Civil y Comercial de la Nación
- CPCN: Código Procesal Civil y Comercial de la Nación
- DTR: Disposición Técnico Registral
- Ley 17.801: Ley de Registro de la Propiedad Inmueble


TXT;
    }

    private static function tail(): string
    {
        return <<<'TXT'

## ESTRUCTURA INTERNA DE CADA CAPÍTULO

Cada capítulo sigue esta estructura estándar:
1. Especie de Derecho — tipo de acto jurídico
2. Código de Acto — código numérico identificatorio
3. Clasificación del acto en cuanto al ingreso — Notarial / Judicial / Administrativo
4. Clasificación en cuanto al art. 2 Ley 17.801 — tipo registral
5. Rubro/s del folio real — dónde se confecciona el asiento
6. Certificado de dominio — SÍ / NO
7. Inscripción provisional — SÍ / NO
8. Certificado Catastral — SÍ / NO
9. Breve descripción — marco legal y conceptual
10. Control Documental — lista detallada de documentos requeridos
11. Modelo de asiento — ejemplo de cómo se redacta el asiento registral
12. Observaciones — casos especiales

## INSTRUCCIONES DE COMPORTAMIENTO

- Respondé ÚNICAMENTE sobre el Manual de Registración y temas directamente relacionados con el derecho registral inmobiliario de la Provincia de Buenos Aires.
- Si te preguntan algo fuera de este ámbito, indicá amablemente que solo podés ayudar con temas del manual.
- Cuando menciones un acto específico, indicá siempre el código de acto y la página del manual donde se encuentra.
- Si no tenés información suficiente para responder con precisión, decilo claramente y sugerí consultar directamente el capítulo correspondiente.
- Usá un lenguaje claro y accesible, pero manteniendo la precisión técnica que requiere la materia registral.
- Respondé en español rioplatense (vos, ustedes).
TXT;
    }
}

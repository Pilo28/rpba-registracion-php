<?php

declare(strict_types=1);

namespace App;

/**
 * Conversión entre arrays PHP y la representación textual de arrays de Postgres
 * (columnas text[]), ida y vuelta: '{"a","b, c"}' <-> ['a', 'b, c'].
 */
final class PgArray
{
    /** @return string[] */
    public static function decode(?string $raw): array
    {
        if ($raw === null || $raw === '{}') {
            return [];
        }

        $inner = substr($raw, 1, -1);
        $result = [];
        $current = '';
        $inQuotes = false;
        $len = strlen($inner);

        for ($i = 0; $i < $len; $i++) {
            $char = $inner[$i];

            if ($inQuotes) {
                if ($char === '\\' && $i + 1 < $len) {
                    $current .= $inner[++$i];
                } elseif ($char === '"') {
                    $inQuotes = false;
                } else {
                    $current .= $char;
                }
                continue;
            }

            if ($char === '"') {
                $inQuotes = true;
            } elseif ($char === ',') {
                $result[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if ($current !== '' || count($result) > 0) {
            $result[] = $current;
        }

        return $result;
    }

    /** @param string[] $values */
    public static function encode(array $values): string
    {
        $escaped = array_map(
            fn (string $v) => '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $v) . '"',
            $values,
        );

        return '{' . implode(',', $escaped) . '}';
    }
}

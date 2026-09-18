<?php

declare(strict_types=1);

namespace App\Services;

use App\Asistente\SystemPromptBuilder;

final class GeminiService
{
    private const MAX_INPUT_CHARS = 2000;
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent';

    /**
     * Reenvía el historial de mensajes a Gemini y transmite la respuesta SSE
     * tal como llega, chunk a chunk, directo al stdout de la respuesta HTTP actual.
     *
     * @param array<int, array{role: 'user'|'assistant', content: string}> $history
     */
    public function streamChat(array $history): void
    {
        $apiKey = config()['gemini_api_key'];

        if ($apiKey === '') {
            $this->emitError('El asistente no está configurado: falta GEMINI_API_KEY en el servidor.');
            return;
        }

        $contents = array_map(
            fn (array $m) => [
                'role' => $m['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => mb_substr((string) $m['content'], 0, self::MAX_INPUT_CHARS)]],
            ],
            $history,
        );

        $systemPrompt = SystemPromptBuilder::build();

        $payload = json_encode([
            'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents' => $contents,
            'generationConfig' => ['maxOutputTokens' => 1024, 'temperature' => 0.3],
        ]);

        $url = self::ENDPOINT . '?alt=sse&key=' . urlencode($apiKey);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_WRITEFUNCTION => function (\CurlHandle $handle, string $chunk): int {
                echo $chunk;
                flush();
                return strlen($chunk);
            },
        ]);

        $ok = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if ($ok === false || $status >= 400) {
            $this->emitError($this->buildErrorMessage((int) $status, $error));
        }
    }

    private function buildErrorMessage(int $status, string $curlError): string
    {
        if ($status === 400) {
            return 'Solicitud inválida. Verificá el formato del mensaje.';
        }
        if ($status === 401 || $status === 403) {
            return 'API key inválida o no autorizada. Verificá la configuración del servidor.';
        }
        if ($status === 429) {
            return 'Límite de solicitudes alcanzado. Esperá unos segundos e intentá de nuevo.';
        }
        if ($status === 503) {
            return 'El servicio de IA está temporalmente sobrecargado. Intentá de nuevo en unos momentos.';
        }
        if ($status >= 500) {
            return "Error del servidor de IA ($status). Intentá de nuevo más tarde.";
        }
        return $curlError !== ''
            ? "Error al comunicarse con el asistente: $curlError"
            : "Error al comunicarse con el asistente ($status).";
    }

    private function emitError(string $message): void
    {
        echo 'event: error' . "\n";
        echo 'data: ' . json_encode(['error' => $message]) . "\n\n";
        flush();
    }
}

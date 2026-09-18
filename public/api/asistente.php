<?php

declare(strict_types=1);

require __DIR__ . '/../../bootstrap.php';

use App\Services\GeminiService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$body = json_decode(file_get_contents('php://input') ?: '[]', true);
$history = is_array($body['history'] ?? null) ? $body['history'] : [];

$sanitized = [];
foreach ($history as $msg) {
    if (!isset($msg['role'], $msg['content'])) {
        continue;
    }
    $role = $msg['role'] === 'assistant' ? 'assistant' : 'user';
    $sanitized[] = ['role' => $role, 'content' => (string) $msg['content']];
}

(new GeminiService())->streamChat($sanitized);

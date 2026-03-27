<?php

declare(strict_types=1);

function startSessionIfNeeded(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function sendJson(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getJsonBody(): array
{
    $raw = file_get_contents('php://input');

    if ($raw === false || $raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

function requireFields(array $data, array $fields): array
{
    $errors = [];

    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
            $errors[$field] = 'هذا الحقل مطلوب';
        }
    }

    return $errors;
}

function currentUser(): ?array
{
    startSessionIfNeeded();

    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function requireAdmin(): array
{
    $user = currentUser();

    if (!$user || ($user['role'] ?? '') !== 'admin') {
        sendJson(['message' => 'غير مصرح لك بتنفيذ هذا الإجراء'], 401);
    }

    return $user;
}

function readRequestData(): array
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
        return $_POST;
    }

    return getJsonBody();
}

function jsonInputValue(array $data, string $key, mixed $default = null): mixed
{
    return array_key_exists($key, $data) ? $data[$key] : $default;
}

function passwordMatches(string $plainPassword, string $storedValue): bool
{
    return password_verify($plainPassword, $storedValue) || hash_equals($storedValue, $plainPassword);
}

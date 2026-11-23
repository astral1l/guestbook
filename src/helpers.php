<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url, int $status = 303): never
{
    header('Location: ' . $url, true, $status);
    exit;
}

function render(string $view, array $data = [], int $status = 200): void
{
    http_response_code($status);

    extract($data);
    ob_start();
    require dirname(__DIR__) . '/views/' . $view . '.php';
    $content = ob_get_clean();

    require dirname(__DIR__) . '/views/layout.php';
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $flash;
}

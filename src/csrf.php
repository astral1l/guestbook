<?php

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_check(): void
{
    $token = $_POST['csrf'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        render('error', [
            'title' => 'Session expired',
            'message' => 'Go back, reload the page and try again.',
        ], 419);
        exit;
    }
}

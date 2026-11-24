<?php

function is_admin(): bool
{
    return !empty($_SESSION['admin']);
}

function login(string $login, string $password): bool
{
    $admin = config()['admin'] ?? [];

    if (!hash_equals((string) ($admin['login'] ?? ''), $login)) {
        return false;
    }

    if (!password_verify($password, (string) ($admin['password_hash'] ?? ''))) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin'] = true;

    return true;
}

function logout(): void
{
    unset($_SESSION['admin']);
    session_regenerate_id(true);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('/admin');
    }
}

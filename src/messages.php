<?php

const MESSAGES_PER_PAGE = 10;

function count_messages(): int
{
    return (int) db()->query('SELECT COUNT(*) FROM messages')->fetchColumn();
}

function page_count(int $total): int
{
    return max(1, (int) ceil($total / MESSAGES_PER_PAGE));
}

function page_offset(int $page): int
{
    return ($page - 1) * MESSAGES_PER_PAGE;
}

function all_messages(int $page): array
{
    $stmt = db()->prepare('SELECT id, name, body, created_at FROM messages ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, MESSAGES_PER_PAGE, PDO::PARAM_INT);
    $stmt->bindValue(2, page_offset($page), PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

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

function page_number($value): int
{
    $page = filter_var($value, FILTER_VALIDATE_INT);

    return $page !== false && $page > 0 ? $page : 1;
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

function validate_message(array $input): array
{
    $data = [
        'name' => trim(is_string($input['name'] ?? null) ? $input['name'] : ''),
        'email' => is_string($input['email'] ?? null) ? $input['email'] : '',
        'body' => trim(is_string($input['body'] ?? null) ? $input['body'] : ''),
    ];

    $errors = [];

    if ($data['name'] === '') {
        $errors['name'] = 'Enter your name.';
    } elseif (strlen($data['name']) < 2 || strlen($data['name']) > 50) {
        $errors['name'] = 'Name must be 2 to 50 characters.';
    }

    if ($data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($data['body'] === '') {
        $errors['body'] = 'Enter your message.';
    } elseif (strlen($data['body']) < 3 || strlen($data['body']) > 1000) {
        $errors['body'] = 'Message must be 3 to 1000 characters.';
    }

    return [$data, $errors];
}

function create_message(array $data): void
{
    $stmt = db()->prepare('INSERT INTO messages (name, email, body, created_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $data['name'],
        $data['email'] !== '' ? $data['email'] : null,
        $data['body'],
        date('Y-m-d H:i:s'),
    ]);
}

function delete_message(int $id): void
{
    $stmt = db()->prepare('DELETE FROM messages WHERE id = ?');
    $stmt->execute([$id]);
}

<?php

return [
    'db' => __DIR__ . '/storage/guestbook.sqlite',
    'timezone' => 'UTC',
    'debug' => true,

    'admin' => [
        'login' => 'admin',
        // password: admin123, generate your own hash for anything real
        'password_hash' => '$2y$12$VKeSZr/4ftkWH6rM5X25ieT0j0RnazuztN4twf0NYJG0Dxuhe3xjq',
    ],
];

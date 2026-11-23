<?php

function db(bool $create = false): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $path = config()['db'];

        if (!$create && !is_file($path)) {
            throw new RuntimeException('Database is not initialized. Run: php bin/init-db.php');
        }

        $pdo = new PDO('sqlite:' . $path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    return $pdo;
}

<?php

require dirname(__DIR__) . '/src/bootstrap.php';

$schema = file_get_contents(dirname(__DIR__) . '/database/schema.sql');

if ($schema === false) {
    fwrite(STDERR, "Could not read database/schema.sql\n");
    exit(1);
}

db(true)->exec($schema);

echo 'Database is ready: ' . config()['db'] . PHP_EOL;

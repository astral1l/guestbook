<?php

if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (is_file($file)) {
        return false;
    }
}

require dirname(__DIR__) . '/src/bootstrap.php';

route('GET', '/', function () {
    $page = (int) ($_GET['page'] ?? 1);
    $pages = page_count(count_messages());
    $page = min($page, $pages);

    render('home', [
        'title' => 'Guestbook',
        'messages' => all_messages($page),
        'page' => $page,
        'pages' => $pages,
        'errors' => [],
        'old' => ['name' => '', 'email' => '', 'body' => ''],
    ]);
});

route('POST', '/messages', function () {
    [$data, $errors] = validate_message($_POST);

    if ($errors) {
        render('home', [
            'title' => 'Guestbook',
            'messages' => all_messages(1),
            'page' => 1,
            'pages' => page_count(count_messages()),
            'errors' => $errors,
            'old' => $data,
        ]);

        return;
    }

    create_message($data);
    flash('Thanks, your message has been added.');
    redirect('/');
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

dispatch($method, $path);

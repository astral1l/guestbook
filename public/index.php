<?php

if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (is_file($file)) {
        return false;
    }
}

require dirname(__DIR__) . '/src/bootstrap.php';

route('GET', '/', function () {
    $page = page_number($_GET['page'] ?? 1);
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

route('GET', '/admin', function () {
    if (!is_admin()) {
        render('admin/login', [
            'title' => 'Admin login',
            'error' => null,
            'old' => ['login' => ''],
        ]);

        return;
    }

    $page = page_number($_GET['page'] ?? 1);
    $pages = page_count(count_messages());
    $page = min($page, $pages);

    render('admin/messages', [
        'title' => 'Messages',
        'messages' => all_messages($page),
        'page' => $page,
        'pages' => $pages,
        'pagerBase' => '/admin',
    ]);
});

route('POST', '/admin/login', function () {
    $login = trim(is_string($_POST['login'] ?? null) ? $_POST['login'] : '');
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if (login($login, $password)) {
        redirect('/admin');
    }

    render('admin/login', [
        'title' => 'Admin login',
        'error' => 'Wrong login or password.',
        'old' => ['login' => $login],
    ], 422);
});

route('GET', '/admin/logout', function () {
    logout();
    redirect('/admin');
});

route('POST', '/admin/messages/{id}/delete', function (array $params) {
    require_admin();

    delete_message((int) $params['id']);

    flash('Message deleted.');
    redirect('/admin');
});

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

dispatch($method, $path);

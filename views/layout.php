<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Guestbook') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<main class="page">
    <header class="top">
        <a class="brand" href="/">Guestbook</a>

        <?php if (is_admin()): ?>
            <nav>
                <a href="/admin">Admin</a>
            </nav>
        <?php endif; ?>
    </header>

    <?php $flash = take_flash(); ?>
    <?php if ($flash): ?>
        <p class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></p>
    <?php endif; ?>

    <?= $content ?>
</main>
</body>
</html>

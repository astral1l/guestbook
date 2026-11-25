<h1>Admin login</h1>

<?php if ($error): ?>
    <p class="flash error"><?= e($error) ?></p>
<?php endif; ?>

<form method="post" action="/admin/login" class="form login">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="field">
        <label for="login">Login</label>
        <input type="text" id="login" name="login" value="<?= e($old['login'] ?? '') ?>" autocomplete="username">
    </div>

    <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" autocomplete="current-password">
    </div>

    <button type="submit">Log in</button>
</form>

<form method="post" action="/messages" class="form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="hp" aria-hidden="true">
        <label for="website">Website</label>
        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="field">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= e($old['name'] ?? '') ?>" maxlength="50" autocomplete="name">
        <?php if (isset($errors['name'])): ?>
            <p class="error"><?= e($errors['name']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="email">Email <span class="hint">optional, never shown</span></label>
        <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" autocomplete="email">
        <?php if (isset($errors['email'])): ?>
            <p class="error"><?= e($errors['email']) ?></p>
        <?php endif; ?>
    </div>

    <div class="field">
        <label for="body">Message</label>
        <textarea id="body" name="body" rows="5" maxlength="1000"><?= e($old['body'] ?? '') ?></textarea>
        <?php if (isset($errors['body'])): ?>
            <p class="error"><?= e($errors['body']) ?></p>
        <?php endif; ?>
    </div>

    <?php if (isset($errors['form'])): ?>
        <p class="flash error"><?= e($errors['form']) ?></p>
    <?php endif; ?>

    <button type="submit">Send message</button>
</form>

<?php if (empty($messages)): ?>
    <p class="empty">No messages yet. Be the first to write one.</p>
<?php else: ?>
    <ul class="messages">
        <?php foreach ($messages as $message): ?>
            <?php $created = strtotime($message['created_at']); ?>
            <li class="message">
                <div class="meta">
                    <strong><?= e($message['name']) ?></strong>
                    <time datetime="<?= e(date('c', $created)) ?>"><?= e(date('M j, Y H:i', $created)) ?></time>
                </div>
                <p class="text"><?= nl2br(e($message['body']), false) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($pages > 1): ?>
    <nav class="pager">
        <?php if ($page > 1): ?>
            <a href="<?= e($pagerBase ?? '/') ?>?page=<?= $page - 1 ?>">Newer</a>
        <?php endif; ?>

        <span>Page <?= $page ?> of <?= $pages ?></span>

        <?php if ($page < $pages): ?>
            <a href="<?= e($pagerBase ?? '/') ?>?page=<?= $page + 1 ?>">Older</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

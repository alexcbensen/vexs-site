<?php
$pageTitle = 'Changelog - Neopolitan';
require_once __DIR__ . '/includes/config.php';

$news = [];
if (file_exists(NEWS_PATH)) {
    $news = json_decode(file_get_contents(NEWS_PATH), true) ?: [];
}

require __DIR__ . '/includes/header.php';
?>

<section class="changelog-page">
    <h1>Changelog</h1>
    <p class="muted">Server updates and changes</p>

    <?php if (empty($news)): ?>
    <p class="muted">Nothing yet.</p>
    <?php else: ?>
    <div class="changelog-feed">
        <?php foreach ($news as $entry): ?>
        <div class="changelog-entry">
            <span class="changelog-date"><?= htmlspecialchars($entry['date'] ?? '') ?></span>
            <div class="changelog-content">
                <h3><?= htmlspecialchars($entry['title'] ?? '') ?></h3>
                <div class="changelog-body"><?= $entry['body'] ?? '' ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
$pageTitle = 'Vote - Neopolitan';
require __DIR__ . '/includes/header.php';

$sites = [
    ['name' => 'Planet Minecraft', 'url' => '#', 'desc' => 'Community hub with server listings and projects'],
    ['name' => 'Minecraft Server List', 'url' => '#', 'desc' => 'One of the largest MC server directories'],
    ['name' => 'TopMinecraftServers', 'url' => '#', 'desc' => 'Top-ranked server voting platform'],
    ['name' => 'MinecraftServers.org', 'url' => '#', 'desc' => 'Popular server discovery site'],
    ['name' => 'Minecraft.Buzz', 'url' => '#', 'desc' => 'Server listing and voting community'],
];
?>

<section class="voting-page">
    <h1>Vote for Us</h1>
    <p class="muted">Voting helps new players find the server. Click each site to cast your vote!</p>

    <div class="voting-grid">
        <?php foreach ($sites as $i => $site): ?>
        <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank" rel="noopener" class="vote-card" data-vote="<?= $i ?>">
            <span class="vote-number"><?= $i + 1 ?></span>
            <div class="vote-info">
                <h3><?= htmlspecialchars($site['name']) ?></h3>
                <p><?= htmlspecialchars($site['desc']) ?></p>
            </div>
            <span class="vote-arrow">-></span>
        </a>
        <?php endforeach; ?>
    </div>

    <p class="muted vote-note">Links are placeholders — update them once you've listed the server on each site.</p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

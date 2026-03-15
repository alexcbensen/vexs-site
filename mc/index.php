<?php
$pageTitle = 'Neopolitan - Minecraft Server';
require_once __DIR__ . '/includes/mc-status.php';
$status = getServerStatus();
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <img src="/assets/img/server-icon.png" alt="Server Icon" class="hero-icon">
    <h1><?= SITE_NAME ?></h1>
    <p class="tagline"><?= SITE_TAGLINE ?></p>
</section>

<section class="status-card">
    <div class="status-grid">
        <div class="status-item">
            <span class="status-indicator <?= $status['online'] ? 'online' : 'offline' ?>"></span>
            <span><?= $status['online'] ? 'Online' : 'Offline' ?></span>
        </div>
        <div class="status-item">
            <span class="label">Players</span>
            <span class="value"><?= $status['players_online'] ?>/<?= $status['players_max'] ?></span>
        </div>
        <div class="status-item">
            <span class="label">Version</span>
            <span class="value"><?= htmlspecialchars($status['version'] ?: MC_VERSION) ?></span>
        </div>
        <div class="status-item">
            <span class="label">Address</span>
            <span class="value copy-ip" data-ip="<?= MC_SERVER_DISPLAY ?>"><?= MC_SERVER_DISPLAY ?></span>
        </div>
    </div>
    <?php if ($status['online'] && $status['players_online'] > 0 && !empty($status['player_list'])): ?>
    <div class="online-players">
        <h3>Online Now</h3>
        <div class="player-chips">
            <?php foreach ($status['player_list'] as $name): ?>
            <span class="chip"><?= htmlspecialchars($name) ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<section class="about">
    <h2>About</h2>
    <p>Neopolitan has been running since 2020 — six years of railways, community mines, dragon fights, panda sanctuaries, and questionable minecart-boat experiments. Over 100 players have called it home. Hard difficulty, no shortcuts.</p>
    <div class="feature-grid">
        <div class="feature">
            <h3>Survival</h3>
            <p>Hard difficulty, PVP enabled, no cheats. Earn everything you build.</p>
        </div>
        <div class="feature">
            <h3>Protected</h3>
            <p>GriefPrevention claims keep your builds safe. CoreProtect tracks every block.</p>
        </div>
        <div class="feature">
            <h3>Crossplay</h3>
            <p>Java and Bedrock players welcome via Geyser + Floodgate.</p>
        </div>
    </div>
</section>

<section class="player-chart-section">
    <h2>Player Activity</h2>
    <div class="chart-container">
        <canvas id="playerChart"></canvas>
        <p class="chart-empty muted" hidden>No data yet — chart populates over time.</p>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script>
fetch('/api/player-log.php')
    .then(r => r.json())
    .then(data => {
        if (!data.length) {
            document.querySelector('.chart-empty').hidden = false;
            document.getElementById('playerChart').style.display = 'none';
            return;
        }
        new Chart(document.getElementById('playerChart'), {
            type: 'line',
            data: {
                datasets: [{
                    label: 'Players Online',
                    data: data.map(d => ({ x: d.time * 1000, y: d.players })),
                    borderColor: '#2dd4bf',
                    backgroundColor: 'rgba(45, 212, 191, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 0,
                    pointHitRadius: 8,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#161b22',
                        borderColor: '#21262d',
                        borderWidth: 1,
                        titleColor: '#e6edf3',
                        bodyColor: '#8b949e',
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: { unit: 'day', tooltipFormat: 'MMM d, h:mm a' },
                        grid: { color: 'rgba(139,148,158,0.1)' },
                        ticks: { color: '#8b949e' },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(139,148,158,0.1)' },
                        ticks: { color: '#8b949e', stepSize: 1 },
                    }
                }
            }
        });
    })
    .catch(() => {
        document.querySelector('.chart-empty').hidden = false;
        document.getElementById('playerChart').style.display = 'none';
    });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>

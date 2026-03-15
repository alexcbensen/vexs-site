<?php
$pageTitle = 'Stats - Neopolitan';
require_once __DIR__ . '/includes/mc-stats.php';
require_once __DIR__ . '/includes/uuid-cache.php';

$boards = getLeaderboards();
$allUUIDs = getAllPlayerUUIDs($boards);
$names = resolveUUIDs($allUUIDs);

$currentStat = $_GET['stat'] ?? 'playtime';
if (!isset(STAT_DEFINITIONS[$currentStat])) $currentStat = 'playtime';
$def = STAT_DEFINITIONS[$currentStat];

$serverTotals = getServerTotals();
$awards = getAwards();
$topMobs = getTopMobKills(5);
$topDeaths = getTopDeathCauses(5);

// Resolve names for award winners
$awardUUIDs = [];
foreach ($awards as $award) {
    foreach ($award['top'] as $entry) {
        $awardUUIDs[$entry['uuid']] = true;
    }
}
$awardNames = resolveUUIDs(array_keys($awardUUIDs));

// Get max values for bar charts
$maxMobCount = !empty($topMobs) ? $topMobs[0]['count'] : 1;
$maxDeathCount = !empty($topDeaths) ? $topDeaths[0]['count'] : 1;

require __DIR__ . '/includes/header.php';
?>

<section class="stats-page">
    <h1>Player Stats</h1>
    <p class="muted"><?= count($allUUIDs) ?> players tracked</p>

    <!-- ── Server Totals ──────────────────────── -->
    <div class="section-divider">
        <span>Server Totals</span>
    </div>

    <div class="totals-grid">
        <?php foreach ($serverTotals as $total): ?>
        <div class="total-card">
            <div class="total-icon"><?= $total['icon'] ?></div>
            <div class="total-value"><?= $total['value'] ?></div>
            <div class="total-label"><?= $total['label'] ?></div>
            <div class="total-detail"><?= $total['detail'] ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Awards ─────────────────────────────── -->
    <div class="section-divider">
        <span>Awards</span>
    </div>

    <div class="awards-grid">
        <?php foreach ($awards as $award): ?>
        <?php if (empty($award['top'])) continue; ?>
        <div class="award-card">
            <div class="award-header">
                <img src="<?= $award["icon"] ?>" alt="" class="award-icon" width="32" height="32">
                <div>
                    <div class="award-title"><?= htmlspecialchars($award['title']) ?></div>
                    <div class="award-desc"><?= htmlspecialchars($award['description']) ?></div>
                </div>
            </div>
            <div class="award-winners">
                <?php foreach ($award['top'] as $rank => $entry):
                    $uuid = $entry['uuid'];
                    $name = $awardNames[$uuid] ?? $names[$uuid] ?? substr($uuid, 0, 8);
                    $rankClass = ['gold', 'silver', 'bronze'][$rank] ?? '';
                ?>
                <div class="award-winner <?= $rankClass ?>">
                    <span class="award-rank"><?= $rank + 1 ?></span>
                    <img src="<?= getPlayerHeadUrl($uuid) ?>" alt="" class="player-head" loading="lazy" width="20" height="20">
                    <span class="award-name"><?= htmlspecialchars($name) ?></span>
                    <span class="award-stat mono"><?= formatStat($entry['value'], $award['format']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Most Killed Mobs ───────────────────── -->
    <div class="section-divider">
        <span>Most Killed Mobs</span>
    </div>

    <div class="bar-chart">
        <?php foreach ($topMobs as $mob): ?>
        <?php $pct = ($mob['count'] / $maxMobCount) * 100; ?>
        <div class="bar-row">
            <span class="bar-label"><?= htmlspecialchars($mob['mob']) ?></span>
            <div class="bar-track">
                <div class="bar-fill" style="width: <?= $pct ?>%"></div>
            </div>
            <span class="bar-value mono"><?= number_format($mob['count']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Top Death Causes ───────────────────── -->
    <div class="section-divider">
        <span>Top Death Causes</span>
    </div>

    <div class="bar-chart">
        <?php foreach ($topDeaths as $cause): ?>
        <?php $pct = ($cause['count'] / $maxDeathCount) * 100; ?>
        <div class="bar-row">
            <span class="bar-label"><?= htmlspecialchars($cause['cause']) ?></span>
            <div class="bar-track">
                <div class="bar-fill bar-fill--red" style="width: <?= $pct ?>%"></div>
            </div>
            <span class="bar-value mono"><?= number_format($cause['count']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Leaderboards ───────────────────────── -->
    <div class="section-divider">
        <span>Leaderboards</span>
    </div>

    <div class="stat-tabs">
        <?php foreach (STAT_DEFINITIONS as $id => $d): ?>
        <a href="?stat=<?= $id ?>" class="tab <?= $id === $currentStat ? 'active' : '' ?>"><?= $d['label'] ?></a>
        <?php endforeach; ?>
    </div>

    <table class="leaderboard">
        <thead>
            <tr>
                <th class="rank-col">#</th>
                <th>Player</th>
                <th class="stat-col"><?= $def['label'] ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $board = $boards[$currentStat] ?? [];
            foreach (array_slice($board, 0, 50) as $i => $entry):
                $rank = $i + 1;
                $uuid = $entry['uuid'];
                $name = $names[$uuid] ?? substr($uuid, 0, 8);
                $rowClass = match($rank) { 1 => 'gold', 2 => 'silver', 3 => 'bronze', default => '' };
            ?>
            <tr class="<?= $rowClass ?>">
                <td class="rank-col"><?= $rank ?></td>
                <td class="player-cell">
                    <img src="<?= getPlayerHeadUrl($uuid) ?>" alt="" class="player-head" loading="lazy" width="24" height="24">
                    <span><?= htmlspecialchars($name) ?></span>
                </td>
                <td class="stat-col mono"><?= formatStat($entry['value'], $def['format']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
$pageTitle = 'Neopolitan - Roblox';
require __DIR__ . '/includes/header.php';

$userId = 3927;
$cacheFile = __DIR__ . '/cache/roblox_cache.json';
$cacheTTL = 300;

function fetchRobloxData($userId) {
    $ctx = stream_context_create(['http' => ['timeout' => 5, 'header' => "Accept: application/json\r\n"]]);
    $userJson = @file_get_contents("https://users.roblox.com/v1/users/{$userId}", false, $ctx);
    $user = $userJson ? json_decode($userJson, true) : [];
    $friendsJson = @file_get_contents("https://friends.roblox.com/v1/users/{$userId}/friends/count", false, $ctx);
    $friends = $friendsJson ? json_decode($friendsJson, true) : [];
    $followersJson = @file_get_contents("https://friends.roblox.com/v1/users/{$userId}/followers/count", false, $ctx);
    $followers = $followersJson ? json_decode($followersJson, true) : [];
    $followingJson = @file_get_contents("https://friends.roblox.com/v1/users/{$userId}/followings/count", false, $ctx);
    $following = $followingJson ? json_decode($followingJson, true) : [];
    return [
        'username' => $user['name'] ?? 'Vexedly',
        'displayName' => $user['displayName'] ?? 'Vexedly',
        'description' => $user['description'] ?? '',
        'created' => $user['created'] ?? '2006-09-01T00:00:00Z',
        'friends' => $friends['count'] ?? 0,
        'followers' => $followers['count'] ?? 0,
        'following' => $following['count'] ?? 0,
        'fetched' => time(),
    ];
}

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
    $data = json_decode(file_get_contents($cacheFile), true);
} else {
    $data = fetchRobloxData($userId);
    @file_put_contents($cacheFile, json_encode($data));
}

$joinDate = date('F Y', strtotime($data['created']));
$accountYears = floor((time() - strtotime($data['created'])) / (365.25 * 86400));
?>

<section class="roblox-hero">
    <div class="roblox-profile-card">
        <div class="roblox-avatar">
            <img src="https://www.roblox.com/headshot-thumbnail/image?userId=<?= $userId ?>&width=150&height=150&format=png" alt="<?= htmlspecialchars($data['username']) ?>" class="roblox-headshot">
        </div>
        <div class="roblox-info">
            <h1><?= htmlspecialchars($data['displayName']) ?></h1>
            <p class="roblox-username">@<?= htmlspecialchars($data['username']) ?> · #<?= number_format($userId) ?></p>
            <?php if (!empty($data['description'])): ?>
            <p class="roblox-bio"><?= nl2br(htmlspecialchars($data['description'])) ?></p>
            <?php endif; ?>
            <div class="roblox-meta">
                <span>Joined <?= $joinDate ?></span>
                <span class="roblox-dot">·</span>
                <span><?= $accountYears ?> years on Roblox</span>
                <span class="roblox-dot">·</span>
                <span>~30,000 visits</span>
            </div>
        </div>
    </div>
</section>

<section class="roblox-stats-section">
    <div class="roblox-stats-grid">
        <div class="roblox-stat">
            <span class="roblox-stat-value"><?= number_format($data['friends']) ?></span>
            <span class="roblox-stat-label">Friends</span>
        </div>
        <div class="roblox-stat">
            <span class="roblox-stat-value"><?= number_format($data['followers']) ?></span>
            <span class="roblox-stat-label">Followers</span>
        </div>
        <div class="roblox-stat">
            <span class="roblox-stat-value"><?= number_format($data['following']) ?></span>
            <span class="roblox-stat-label">Following</span>
        </div>
    </div>
</section>

<section class="roblox-history">
    <h2>Timeline</h2>
    <div class="roblox-timeline">
        <div class="timeline-item">
            <span class="timeline-year">2006</span>
            <div class="timeline-content">
                <h3>Vexedly Account Created</h3>
                <p>User #3,927 — September 2006</p>
            </div>
        </div>
        <div class="timeline-item">
            <span class="timeline-year">2008</span>
            <div class="timeline-content">
                <h3>Started Playing</h3>
                <p>Created original account LordJoe in August 2008</p>
            </div>
        </div>
        <div class="timeline-item">
            <span class="timeline-year">2013</span>
            <div class="timeline-content">
                <h3>Acquired Vexedly</h3>
                <p>Bought the account from ventus48539 in July</p>
            </div>
        </div>
        <div class="timeline-item">
            <span class="timeline-year">2015</span>
            <div class="timeline-content">
                <h3>Roblox Administrator</h3>
                <p>Became an admin on January 14</p>
            </div>
        </div>
        <div class="timeline-item">
            <span class="timeline-year">2018</span>
            <div class="timeline-content">
                <h3>Left Roblox</h3>
                <p>No longer an administrator</p>
            </div>
        </div>
    </div>
</section>

<section class="roblox-links">
    <a href="https://www.roblox.com/users/3927/profile" target="_blank" rel="noopener" class="roblox-link-btn">View on Roblox</a>
    <a href="https://roblox.fandom.com/wiki/Community:Vexedly" target="_blank" rel="noopener" class="roblox-link-btn secondary">Wiki Page</a>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

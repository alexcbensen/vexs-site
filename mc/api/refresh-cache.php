<?php
/**
 * Background UUID cache refresher — runs daily via cron.
 * Re-resolves UUIDs older than 25 days so the page cache never expires mid-request.
 * Also pre-warms the stats cache.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/uuid-cache.php';
require_once __DIR__ . '/../includes/mc-stats.php';

$refreshAge = 25 * 86400; // Refresh anything older than 25 days (TTL is 30)
$cache = loadUUIDCache();
$refreshed = 0;

// Get all UUIDs from stats
$boards = getLeaderboards();
$allUUIDs = getAllPlayerUUIDs($boards);

// Also bootstrap from usercache first
if (file_exists(USERCACHE_PATH)) {
    $usercache = json_decode(file_get_contents(USERCACHE_PATH), true) ?: [];
    foreach ($usercache as $entry) {
        if (isset($entry['uuid'], $entry['name'])) {
            $uuid = $entry['uuid'];
            if (!isset($cache[$uuid]) || (time() - $cache[$uuid]['cached_at']) > $refreshAge) {
                $cache[$uuid] = ['name' => $entry['name'], 'cached_at' => time()];
                $refreshed++;
            }
        }
    }
}

// Resolve remaining stale entries via Mojang (max 50 per run to avoid rate limits)
$apiCalls = 0;
foreach ($allUUIDs as $uuid) {
    if ($apiCalls >= 50) break;
    if (!isset($cache[$uuid]) || (time() - $cache[$uuid]['cached_at']) > $refreshAge) {
        $name = mojangLookup($uuid);
        if ($name) {
            $cache[$uuid] = ['name' => $name, 'cached_at' => time()];
            $refreshed++;
            $apiCalls++;
            usleep(200000); // 200ms between API calls
        }
    }
}

saveUUIDCache($cache);

$msg = "Refreshed $refreshed UUIDs ($apiCalls API calls)";
if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'message' => $msg]);
} else {
    echo "$msg\n";
}

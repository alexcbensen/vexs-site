<?php
// Copy to config.php and fill in your values
define('SITE_NAME', 'Neopolitan');
define('SITE_TAGLINE', 'A whitelisted Minecraft survival server — est. 2020');
define('MC_SERVER_IP', '127.0.0.1');
define('MC_SERVER_PORT', 25565);
define('MC_SERVER_DISPLAY', 'mc.vexedly.net');
define('MC_VERSION', '1.21.11');
define('MC_ENGINE', 'Java · Purpur');
define('STATS_DIR', '/var/www/mcdata/stats');
define('USERCACHE_PATH', '/var/www/mcdata/usercache.json');
define('UUID_CACHE_PATH', '/var/www/html/cache/uuid_cache.json');
define('STATS_CACHE_PATH', '/var/www/html/cache/stats_cache.json');
define('NEWS_PATH', '/var/www/data/news.json');
define('UUID_CACHE_TTL', 2592000); // 30 days
define('STATUS_CACHE_TTL', 30);   // 30 seconds
define('PLAYER_LOG_PATH', '/var/www/data/player_log.json');
define('WHITELIST_PATH', '/var/www/mcdata/whitelist.json');
define('WHITELIST_SECRET', 'change_me');

define('MOJANG_VERSION_CACHE', '/var/www/html/cache/mojang_latest.json');

function mcVersionDisplay(): string {
    $version = MC_VERSION;
    $cacheFile = MOJANG_VERSION_CACHE;
    $latest = null;

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 86400) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        $latest = $cached['release'] ?? null;
    }

    if (!$latest) {
        $ctx = stream_context_create(['http' => ['timeout' => 3]]);
        $json = @file_get_contents('https://piston-meta.mojang.com/mc/game/version_manifest_v2.json', false, $ctx);
        if ($json) {
            $data = json_decode($json, true);
            $latest = $data['latest']['release'] ?? null;
            if ($latest) {
                @file_put_contents($cacheFile, json_encode(['release' => $latest]));
            }
        }
    }

    if ($latest && $version === $latest) {
        return $version . ' <span class="version-latest">(latest)</span>';
    }
    return $version;
}

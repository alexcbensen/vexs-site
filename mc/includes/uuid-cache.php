<?php
require_once __DIR__ . '/config.php';

function resolveUUIDs(array $uuids): array {
    $cache = loadUUIDCache();
    $results = [];
    $needLookup = [];

    // Check cache first — use stale entries as fallback (cron refreshes them)
    foreach ($uuids as $uuid) {
        if (isset($cache[$uuid])) {
            $results[$uuid] = $cache[$uuid]['name'];
            if ((time() - $cache[$uuid]['cached_at']) >= UUID_CACHE_TTL) {
                $needLookup[] = $uuid; // Mark for refresh but still serve cached name
            }
        } else {
            $needLookup[] = $uuid;
        }
    }

    // Bootstrap from MC usercache.json
    if (!empty($needLookup) && file_exists(USERCACHE_PATH)) {
        $usercache = json_decode(file_get_contents(USERCACHE_PATH), true) ?: [];
        $ucMap = [];
        foreach ($usercache as $entry) {
            if (isset($entry['uuid'], $entry['name'])) {
                $ucMap[$entry['uuid']] = $entry['name'];
            }
        }
        $stillNeed = [];
        foreach ($needLookup as $uuid) {
            if (isset($ucMap[$uuid])) {
                $results[$uuid] = $ucMap[$uuid];
                $cache[$uuid] = ['name' => $ucMap[$uuid], 'cached_at' => time()];
            } else {
                $stillNeed[] = $uuid;
            }
        }
        $needLookup = $stillNeed;
    }

    // Mojang API for remaining — cap at 10 per request to keep page fast
    $apiCalls = 0;
    foreach ($needLookup as $uuid) {
        if (isset($results[$uuid])) continue; // Already have stale cached name
        if ($apiCalls >= 10) {
            $results[$uuid] = $results[$uuid] ?? substr($uuid, 0, 8);
            continue;
        }
        $name = mojangLookup($uuid);
        if ($name) {
            $results[$uuid] = $name;
            $cache[$uuid] = ['name' => $name, 'cached_at' => time()];
        } else {
            $results[$uuid] = substr($uuid, 0, 8);
        }
        $apiCalls++;
        usleep(100000); // 100ms between API calls
    }

    if (!empty($needLookup)) {
        saveUUIDCache($cache);
    }

    return $results;
}

function mojangLookup(string $uuid): ?string {
    $clean = str_replace('-', '', $uuid);
    $ctx = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true]]);
    $json = @file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/{$clean}", false, $ctx);
    if (!$json) return null;
    $data = json_decode($json, true);
    return $data['name'] ?? null;
}

function loadUUIDCache(): array {
    if (!file_exists(UUID_CACHE_PATH)) return [];
    $data = json_decode(file_get_contents(UUID_CACHE_PATH), true);
    return is_array($data) ? $data : [];
}

function saveUUIDCache(array $cache): void {
    $tmp = UUID_CACHE_PATH . '.tmp';
    file_put_contents($tmp, json_encode($cache, JSON_PRETTY_PRINT));
    rename($tmp, UUID_CACHE_PATH);
}

function getPlayerHeadUrl(string $uuid, int $size = 32): string {
    $clean = str_replace('-', '', $uuid);
    return "https://mc-heads.net/avatar/{$clean}/{$size}";
}

<?php
require_once __DIR__ . '/config.php';

define('STAT_DEFINITIONS', [
    'playtime'  => ['key' => 'minecraft:play_one_minute', 'label' => 'Playtime', 'format' => 'time'],
    'kills'     => ['key' => 'minecraft:mob_kills',       'label' => 'Mob Kills', 'format' => 'number'],
    'deaths'    => ['key' => 'minecraft:deaths',          'label' => 'Deaths', 'format' => 'number'],
    'walked'    => ['key' => 'minecraft:walk_one_cm',     'label' => 'Distance Walked', 'format' => 'distance'],
    'pkills'    => ['key' => 'minecraft:player_kills',    'label' => 'Player Kills', 'format' => 'number'],
    'sprinted'  => ['key' => 'minecraft:sprint_one_cm',   'label' => 'Distance Sprinted', 'format' => 'distance'],
    'jumped'    => ['key' => 'minecraft:jump',            'label' => 'Jumps', 'format' => 'number'],
    'dmg_dealt' => ['key' => 'minecraft:damage_dealt',    'label' => 'Damage Dealt', 'format' => 'hearts'],
    'bred'      => ['key' => 'minecraft:animals_bred',    'label' => 'Animals Bred', 'format' => 'number'],
    'fished'    => ['key' => 'minecraft:fish_caught',     'label' => 'Fish Caught', 'format' => 'number'],
    'enchanted' => ['key' => 'minecraft:enchant_item',    'label' => 'Items Enchanted', 'format' => 'number'],
    // New aggregate stat tabs
    'elytra'    => ['key' => 'minecraft:aviate_one_cm',   'label' => 'Elytra Distance', 'format' => 'distance'],
    'boat'      => ['key' => 'minecraft:boat_one_cm',     'label' => 'Boat Distance', 'format' => 'distance'],
    'diamonds'  => ['key' => 'diamonds_mined',            'label' => 'Diamonds Mined', 'format' => 'number', 'source' => 'mined', 'keys' => ['minecraft:diamond_ore', 'minecraft:deepslate_diamond_ore']],
    'mined'     => ['key' => 'blocks_mined',              'label' => 'Blocks Mined', 'format' => 'number', 'source' => 'mined_all'],
    'broken'    => ['key' => 'tools_broken',              'label' => 'Tools Broken', 'format' => 'number', 'source' => 'broken_all'],
]);

// Alt accounts -> primary account. Stats get merged under the primary UUID.
define('PLAYER_ALIASES', [
    'c29663e1-be32-4288-af99-504c10d618e2' => '0b3013d0-a05f-4760-bf84-d897fe295715', // Zegga -> Notta
    '48ee8b56-b268-4617-81b8-ccc08fa475c4' => '0b3013d0-a05f-4760-bf84-d897fe295715', // ShaiGodAlex -> Notta
]);

define('HIDDEN_PLAYERS', [
    '9be91f4e-c43c-4b5a-a1bb-a01fd71445fc', // RachelArkless
]);

function getLeaderboards(): array {
    // Check cache
    if (file_exists(STATS_CACHE_PATH)) {
        $cacheTime = filemtime(STATS_CACHE_PATH);
        $newestStat = getNewestStatTime();
        if ($newestStat <= $cacheTime) {
            $cached = json_decode(file_get_contents(STATS_CACHE_PATH), true);
            if ($cached) return $cached;
        }
    }

    $boards = [];
    foreach (STAT_DEFINITIONS as $id => $def) {
        $boards[$id] = [];
    }

    // Collect raw stats per UUID, merging alts into primary
    $playerStats = []; // primaryUUID => [statId => totalValue]
    $files = glob(STATS_DIR . '/*.json');
    foreach ($files as $file) {
        $uuid = basename($file, '.json');
        $data = json_decode(file_get_contents($file), true);
        if (!$data) continue;

        // Resolve to primary UUID if this is an alt
        $primaryUuid = PLAYER_ALIASES[$uuid] ?? $uuid;

        $custom = $data['stats']['minecraft:custom'] ?? [];
        $mined = $data['stats']['minecraft:mined'] ?? [];
        $broken = $data['stats']['minecraft:broken'] ?? [];

        foreach (STAT_DEFINITIONS as $id => $def) {
            $val = 0;

            if (isset($def['source'])) {
                switch ($def['source']) {
                    case 'mined':
                        // Sum specific mined keys
                        foreach ($def['keys'] as $k) {
                            $val += $mined[$k] ?? 0;
                        }
                        break;
                    case 'mined_all':
                        // Sum all mined values
                        $val = array_sum($mined);
                        break;
                    case 'broken_all':
                        // Sum all broken values
                        $val = array_sum($broken);
                        break;
                }
            } else {
                $val = $custom[$def['key']] ?? 0;
            }

            if ($val > 0) {
                $playerStats[$primaryUuid][$id] = ($playerStats[$primaryUuid][$id] ?? 0) + $val;
            }
        }
    }

    // Build boards from merged stats
    foreach ($playerStats as $uuid => $stats) {
        foreach ($stats as $id => $val) {
            $boards[$id][] = ['uuid' => $uuid, 'value' => $val];
        }
    }

    // Sort each board descending
    foreach ($boards as &$board) {
        usort($board, fn($a, $b) => $b['value'] <=> $a['value']);
        $board = array_values(array_filter($board, fn($e) => !in_array($e['uuid'], HIDDEN_PLAYERS)));
    }

    // Cache
    file_put_contents(STATS_CACHE_PATH, json_encode($boards));
    return $boards;
}

function getNewestStatTime(): int {
    $newest = 0;
    $files = glob(STATS_DIR . '/*.json');
    foreach ($files as $f) {
        $t = filemtime($f);
        if ($t > $newest) $newest = $t;
    }
    return $newest;
}

function getAllPlayerUUIDs(array $boards): array {
    $uuids = [];
    foreach ($boards as $board) {
        foreach ($board as $entry) {
            $uuids[$entry['uuid']] = true;
        }
    }
    return array_keys($uuids);
}

function getLastOnlineTimes(): array {
    $times = [];
    $files = glob(STATS_DIR . '/*.json');
    foreach ($files as $file) {
        $uuid = basename($file, '.json');
        $primaryUuid = PLAYER_ALIASES[$uuid] ?? $uuid;
        $mtime = filemtime($file);
        // For merged alts, keep the most recent time
        if (!isset($times[$primaryUuid]) || $mtime > $times[$primaryUuid]) {
            $times[$primaryUuid] = $mtime;
        }
    }
    return $times;
}

function formatTimeAgo(int $timestamp): string {
    $diff = time() - $timestamp;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    if ($diff < 2592000) return floor($diff / 604800) . 'w ago';
    return date('M j', $timestamp);
}

function formatStat($value, string $format): string {
    switch ($format) {
        case 'time':
            $hours = floor($value / 20 / 3600);
            return number_format($hours) . 'h';
        case 'distance':
            $km = $value / 100000;
            return number_format($km, 1) . ' km';
        case 'hearts':
            return number_format($value / 10) . ' hp';
        case 'number':
        default:
            return number_format($value);
    }
}

/**
 * Load all raw player stat data with alias merging.
 * Returns [primaryUuid => mergedData] where mergedData has the full stats structure.
 */
function loadAllPlayerData(): array {
    $players = [];
    $files = glob(STATS_DIR . '/*.json');
    foreach ($files as $file) {
        $uuid = basename($file, '.json');
        $data = json_decode(file_get_contents($file), true);
        if (!$data || !isset($data['stats'])) continue;

        $primaryUuid = PLAYER_ALIASES[$uuid] ?? $uuid;

        if (!isset($players[$primaryUuid])) {
            $players[$primaryUuid] = [];
        }

        // Merge all stat categories
        foreach ($data['stats'] as $category => $entries) {
            if (!isset($players[$primaryUuid][$category])) {
                $players[$primaryUuid][$category] = [];
            }
            foreach ($entries as $key => $val) {
                $players[$primaryUuid][$category][$key] = ($players[$primaryUuid][$category][$key] ?? 0) + $val;
            }
        }
    }
    return $players;
}

/**
 * Returns an array of named awards with top 3 players each.
 */
function getAwards(): array {
    $players = loadAllPlayerData();

    $awardDefs = [
        [
            'id' => 'veteran',
            'title' => 'The Veteran',
            'icon' => '/assets/img/items/clock.png',
            'description' => 'Most total playtime on the server',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:play_one_minute'] ?? 0;
            },
            'format' => 'time',
        ],
        [
            'id' => 'marathon',
            'title' => 'Marathon Runner',
            'icon' => '/assets/img/items/leather_boots.png',
            'description' => 'Most distance walked',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:walk_one_cm'] ?? 0;
            },
            'format' => 'distance',
        ],
        [
            'id' => 'flyer',
            'title' => 'Frequent Flyer',
            'icon' => '/assets/img/items/elytra.png',
            'description' => 'Most distance traveled by elytra',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:aviate_one_cm'] ?? 0;
            },
            'format' => 'distance',
        ],
        [
            'id' => 'sailor',
            'title' => 'The Sailor',
            'icon' => '/assets/img/items/oak_boat.png',
            'description' => 'Most distance traveled by boat',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:boat_one_cm'] ?? 0;
            },
            'format' => 'distance',
        ],
        [
            'id' => 'butcher',
            'title' => 'The Butcher',
            'icon' => '/assets/img/items/diamond_sword.png',
            'description' => 'Most mob kills',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:mob_kills'] ?? 0;
            },
            'format' => 'number',
        ],
        [
            'id' => 'clumsy',
            'title' => 'The Clumsy',
            'icon' => '/assets/img/items/rotten_flesh.png',
            'description' => 'Most deaths',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:deaths'] ?? 0;
            },
            'format' => 'number',
        ],
        [
            'id' => 'glass_cannon',
            'title' => 'Glass Cannon',
            'icon' => '/assets/img/items/crossbow.png',
            'description' => 'Highest kill-to-death difference (min 10 of each)',
            'extract' => function($stats) {
                $kills = $stats['minecraft:custom']['minecraft:mob_kills'] ?? 0;
                $deaths = $stats['minecraft:custom']['minecraft:deaths'] ?? 0;
                if ($kills <= 10 || $deaths <= 10) return 0;
                return $kills - $deaths;
            },
            'format' => 'number',
        ],
        [
            'id' => 'diamond_hands',
            'title' => 'Diamond Hands',
            'icon' => '/assets/img/items/diamond_pickaxe.png',
            'description' => 'Most diamond ore mined',
            'extract' => function($stats) {
                $mined = $stats['minecraft:mined'] ?? [];
                return ($mined['minecraft:diamond_ore'] ?? 0) + ($mined['minecraft:deepslate_diamond_ore'] ?? 0);
            },
            'format' => 'number',
        ],
        [
            'id' => 'lumberjack',
            'title' => 'The Lumberjack',
            'icon' => '/assets/img/items/stone_axe.png',
            'description' => 'Most logs chopped',
            'extract' => function($stats) {
                $mined = $stats['minecraft:mined'] ?? [];
                $total = 0;
                foreach ($mined as $key => $val) {
                    if (str_contains($key, '_log')) {
                        $total += $val;
                    }
                }
                return $total;
            },
            'format' => 'number',
        ],
        [
            'id' => 'tool_breaker',
            'title' => 'Tool Breaker',
            'icon' => '/assets/img/items/wooden_pickaxe.png',
            'description' => 'Most tools and equipment broken',
            'extract' => function($stats) {
                $broken = $stats['minecraft:broken'] ?? [];
                return array_sum($broken);
            },
            'format' => 'number',
        ],
        [
            'id' => 'explorer',
            'title' => 'The Explorer',
            'icon' => '/assets/img/items/compass.png',
            'description' => 'Most total distance traveled by any means',
            'extract' => function($stats) {
                $c = $stats['minecraft:custom'] ?? [];
                return ($c['minecraft:walk_one_cm'] ?? 0)
                     + ($c['minecraft:sprint_one_cm'] ?? 0)
                     + ($c['minecraft:swim_one_cm'] ?? 0)
                     + ($c['minecraft:boat_one_cm'] ?? 0)
                     + ($c['minecraft:horse_one_cm'] ?? 0)
                     + ($c['minecraft:aviate_one_cm'] ?? 0)
                     + ($c['minecraft:minecart_one_cm'] ?? 0);
            },
            'format' => 'distance',
        ],
        [
            'id' => 'night_owl',
            'title' => 'Night Owl',
            'icon' => '/assets/img/items/phantom_membrane.png',
            'description' => 'Most phantoms killed — never sleeps',
            'extract' => function($stats) {
                return $stats['minecraft:killed']['minecraft:phantom'] ?? 0;
            },
            'format' => 'number',
        ],
        [
            'id' => 'hermit',
            'title' => 'The Hermit',
            'icon' => '/assets/img/items/barrel_top.png',
            'description' => 'Most chests opened',
            'extract' => function($stats) {
                return $stats['minecraft:custom']['minecraft:open_chest'] ?? 0;
            },
            'format' => 'number',
        ],
        [
            'id' => 'ender_slayer',
            'title' => 'Ender Slayer',
            'icon' => '/assets/img/items/ender_eye.png',
            'description' => 'Most endermen killed',
            'extract' => function($stats) {
                return $stats['minecraft:killed']['minecraft:enderman'] ?? 0;
            },
            'format' => 'number',
        ],
    ];

    $awards = [];
    foreach ($awardDefs as $def) {
        $scores = [];
        foreach ($players as $uuid => $stats) {
            $val = ($def['extract'])($stats);
            if ($val > 0) {
                $scores[] = ['uuid' => $uuid, 'value' => $val];
            }
        }
        usort($scores, fn($a, $b) => $b['value'] <=> $a['value']);

        $awards[] = [
            'id' => $def['id'],
            'title' => $def['title'],
            'icon' => $def['icon'],
            'description' => $def['description'],
            'format' => $def['format'],
            'top' => array_slice($scores, 0, 3),
        ];
    }

    return $awards;
}

/**
 * Returns server-wide aggregate stats with real-world comparisons.
 */
function getServerTotals(): array {
    $players = loadAllPlayerData();

    $totalPlaytime = 0;
    $totalWalked = 0;
    $totalMined = 0;
    $totalKills = 0;
    $totalDeaths = 0;
    $totalJumps = 0;

    foreach ($players as $uuid => $stats) {
        $c = $stats['minecraft:custom'] ?? [];
        $totalPlaytime += $c['minecraft:play_one_minute'] ?? 0;
        $totalWalked += $c['minecraft:walk_one_cm'] ?? 0;
        $totalKills += $c['minecraft:mob_kills'] ?? 0;
        $totalDeaths += $c['minecraft:deaths'] ?? 0;
        $totalJumps += $c['minecraft:jump'] ?? 0;

        $mined = $stats['minecraft:mined'] ?? [];
        $totalMined += array_sum($mined);
    }

    // Convert playtime ticks to years (20 ticks/sec)
    $playtimeSeconds = $totalPlaytime / 20;
    $playtimeYears = $playtimeSeconds / (365.25 * 24 * 3600);

    // Convert walked cm to km
    $walkedKm = $totalWalked / 100000;
    $earthCircumference = 40075; // km
    $earthPercent = ($walkedKm / $earthCircumference) * 100;

    // Blocks mined in millions
    $minedMillions = $totalMined / 1000000;

    $totals = [
        [
            'label' => 'Combined Playtime',
            'value' => number_format($playtimeYears, 1) . ' years',
            'detail' => number_format($playtimeYears, 1) . ' years of combined playtime',
            'icon' => "\u{23F0}",
        ],
        [
            'label' => 'Distance Walked',
            'value' => number_format($walkedKm, 0) . ' km',
            'detail' => $earthPercent >= 100
                ? 'That\'s ' . number_format($walkedKm / $earthCircumference, 1) . 'x around the Earth'
                : number_format($earthPercent, 0) . '% of the way around the Earth',
            'icon' => "\u{1F6B6}",
        ],
        [
            'label' => 'Blocks Mined',
            'value' => ($minedMillions >= 1 ? number_format($minedMillions, 1) . 'M' : number_format($totalMined)),
            'detail' => ($minedMillions >= 1 ? number_format($minedMillions, 1) . ' million' : number_format($totalMined)) . ' blocks mined',
            'icon' => "\u{26CF}",
        ],
        [
            'label' => 'Mob Kills',
            'value' => number_format($totalKills),
            'detail' => number_format($totalKills) . ' mobs slain',
            'icon' => "\u{2694}",
        ],
        [
            'label' => 'Total Deaths',
            'value' => number_format($totalDeaths),
            'detail' => number_format($totalDeaths) . ' total deaths',
            'icon' => "\u{1F480}",
        ],
        [
            'label' => 'Total Jumps',
            'value' => number_format($totalJumps),
            'detail' => number_format($totalJumps) . ' jumps',
            'icon' => "\u{1F998}",
        ],
    ];

    return $totals;
}

/**
 * Returns the top N most-killed mob types server-wide.
 */
function getTopMobKills(int $limit = 5): array {
    $players = loadAllPlayerData();
    $mobTotals = [];

    foreach ($players as $uuid => $stats) {
        $killed = $stats['minecraft:killed'] ?? [];
        foreach ($killed as $mob => $count) {
            $mobTotals[$mob] = ($mobTotals[$mob] ?? 0) + $count;
        }
    }

    arsort($mobTotals);
    $top = array_slice($mobTotals, 0, $limit, true);

    $results = [];
    foreach ($top as $mob => $count) {
        // Clean up mob name: "minecraft:zombie" -> "Zombie"
        $name = str_replace('minecraft:', '', $mob);
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $results[] = ['mob' => $name, 'count' => $count];
    }

    return $results;
}

/**
 * Returns the top N things that have killed players server-wide.
 */
function getTopDeathCauses(int $limit = 5): array {
    $players = loadAllPlayerData();
    $causeTotals = [];

    foreach ($players as $uuid => $stats) {
        $killedBy = $stats['minecraft:killed_by'] ?? [];
        foreach ($killedBy as $cause => $count) {
            $causeTotals[$cause] = ($causeTotals[$cause] ?? 0) + $count;
        }
    }

    arsort($causeTotals);
    $top = array_slice($causeTotals, 0, $limit, true);

    $results = [];
    foreach ($top as $cause => $count) {
        $name = str_replace('minecraft:', '', $cause);
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        $results[] = ['cause' => $name, 'count' => $count];
    }

    return $results;
}

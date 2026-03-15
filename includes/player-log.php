<?php
/**
 * Player count logger — called periodically to build time-series data.
 * Also provides retrieval for Chart.js frontend.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mc-status.php';

function logPlayerCount(): void {
    $status = getServerStatus();
    $log = [];

    if (file_exists(PLAYER_LOG_PATH)) {
        $log = json_decode(file_get_contents(PLAYER_LOG_PATH), true) ?: [];
    }

    $log[] = [
        'time' => time(),
        'online' => $status['online'] ? 1 : 0,
        'players' => $status['players_online'],
    ];

    // Keep 7 days of 5-minute intervals = ~2016 entries max
    $cutoff = time() - (7 * 86400);
    $log = array_values(array_filter($log, fn($e) => $e['time'] > $cutoff));

    $tmp = PLAYER_LOG_PATH . '.tmp';
    file_put_contents($tmp, json_encode($log));
    rename($tmp, PLAYER_LOG_PATH);
}

function getPlayerLog(): array {
    if (!file_exists(PLAYER_LOG_PATH)) return [];
    return json_decode(file_get_contents(PLAYER_LOG_PATH), true) ?: [];
}

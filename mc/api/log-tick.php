<?php
/**
 * Called by cron every 5 minutes to log current player count.
 * Can also be hit via HTTP for manual trigger (no output).
 */
require_once __DIR__ . '/../includes/player-log.php';

logPlayerCount();

if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
}

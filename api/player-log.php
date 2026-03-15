<?php
/**
 * JSON endpoint for Chart.js — returns player count time-series.
 */
require_once __DIR__ . '/../includes/player-log.php';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=300');

echo json_encode(getPlayerLog());

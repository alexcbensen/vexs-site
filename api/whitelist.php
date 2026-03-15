<?php
/**
 * Whitelist API endpoint.
 * Validates username via Mojang API, adds to whitelist.json.
 */
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$agreedRules = $input['agreed_rules'] ?? false;
$agreedAge = $input['agreed_age'] ?? false;

// Validate input
if (!$username || !$agreedRules || !$agreedAge) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/', $username)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Minecraft username']);
    exit;
}

// Rate limit — 1 request per IP per 60 seconds
$rateFile = '/tmp/wl_rate_' . md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
if (file_exists($rateFile) && (time() - filemtime($rateFile)) < 60) {
    http_response_code(429);
    echo json_encode(['error' => 'Please wait before trying again']);
    exit;
}
touch($rateFile);

// Resolve username to UUID via Mojang API
$ctx = stream_context_create(['http' => ['timeout' => 5]]);
$mojangUrl = 'https://api.mojang.com/users/profiles/minecraft/' . urlencode($username);
$response = @file_get_contents($mojangUrl, false, $ctx);

if ($response === false) {
    http_response_code(404);
    echo json_encode(['error' => 'Could not find that Minecraft account. Check the spelling?']);
    exit;
}

$profile = json_decode($response, true);
if (!$profile || empty($profile['id'])) {
    http_response_code(404);
    echo json_encode(['error' => 'Could not find that Minecraft account']);
    exit;
}

$uuid = $profile['id'];
// Format UUID with dashes
$formattedUuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' .
                 substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' .
                 substr($uuid, 20);
$resolvedName = $profile['name']; // Properly-cased name from Mojang

// Load current whitelist
$whitelist = [];
if (file_exists(WHITELIST_PATH)) {
    $whitelist = json_decode(file_get_contents(WHITELIST_PATH), true) ?: [];
}

// Check if already whitelisted
foreach ($whitelist as $entry) {
    if (strtolower($entry['uuid'] ?? '') === strtolower($formattedUuid)) {
        echo json_encode(['success' => true, 'message' => "$resolvedName is already whitelisted! You can join the server.", 'already' => true]);
        exit;
    }
}

// Add to whitelist
$whitelist[] = [
    'uuid' => $formattedUuid,
    'name' => $resolvedName,
];

// Write to local staging file (web-writable), cron syncs to MC whitelist
$stagePath = '/var/www/data/whitelist_pending.json';
$pending = [];
if (file_exists($stagePath)) {
    $pending = json_decode(file_get_contents($stagePath), true) ?: [];
}
$pending[] = ['uuid' => $formattedUuid, 'name' => $resolvedName];
file_put_contents($stagePath, json_encode($pending, JSON_PRETTY_PRINT), LOCK_EX);

// Also try direct write (works if permissions allow)
@file_put_contents(WHITELIST_PATH, json_encode($whitelist, JSON_PRETTY_PRINT), LOCK_EX);

// Log the application
$logEntry = date('Y-m-d H:i:s') . " | $resolvedName ($formattedUuid) | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";
file_put_contents('/var/www/data/whitelist_log.txt', $logEntry, FILE_APPEND | LOCK_EX);

echo json_encode(['success' => true, 'message' => "$resolvedName has been whitelisted! Connect to " . MC_SERVER_DISPLAY . " to join.", 'name' => $resolvedName]);

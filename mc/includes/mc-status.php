<?php
require_once __DIR__ . '/config.php';

function getServerStatus(): array {
    $cacheFile = '/tmp/mc_status_cache.json';
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < STATUS_CACHE_TTL) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached) return $cached;
    }

    $result = ['online' => false, 'players_online' => 0, 'players_max' => 0, 'player_list' => [], 'version' => '', 'latency_ms' => 0];

    try {
        $start = microtime(true);
        $sock = @fsockopen('tcp://' . MC_SERVER_IP, MC_SERVER_PORT, $errno, $errstr, 3);
        if (!$sock) return $result;

        stream_set_timeout($sock, 3);

        // Build handshake packet
        $host = MC_SERVER_IP;
        $data = packVarInt(0x00);                    // Packet ID
        $data .= packVarInt(-1);                     // Protocol version (-1 = any)
        $data .= packVarInt(strlen($host)) . $host;  // Server address
        $data .= pack('n', MC_SERVER_PORT);          // Server port
        $data .= packVarInt(1);                      // Next state: status
        fwrite($sock, packVarInt(strlen($data)) . $data);

        // Send status request
        $req = packVarInt(0x00);
        fwrite($sock, packVarInt(strlen($req)) . $req);

        // Read response
        $len = readVarInt($sock);
        if ($len < 1) { fclose($sock); return $result; }

        readVarInt($sock); // Packet ID
        $jsonLen = readVarInt($sock);
        $json = '';
        while (strlen($json) < $jsonLen) {
            $chunk = fread($sock, $jsonLen - strlen($json));
            if ($chunk === false || $chunk === '') break;
            $json .= $chunk;
        }

        $latency = round((microtime(true) - $start) * 1000);
        fclose($sock);

        $status = json_decode($json, true);
        if (!$status) return $result;

        $players = $status['players'] ?? [];
        $playerList = [];
        if (isset($players['sample'])) {
            foreach ($players['sample'] as $p) {
                $playerList[] = $p['name'] ?? '';
            }
        }

        $result = [
            'online' => true,
            'players_online' => $players['online'] ?? 0,
            'players_max' => $players['max'] ?? 20,
            'player_list' => $playerList,
            'version' => $status['version']['name'] ?? MC_VERSION,
            'latency_ms' => $latency,
        ];
    } catch (Exception $e) {
        // Server unreachable
    }

    file_put_contents($cacheFile, json_encode($result));
    return $result;
}

function packVarInt(int $value): string {
    $result = '';
    $value &= 0xFFFFFFFF;
    for ($i = 0; $i < 5; $i++) {
        $byte = $value & 0x7F;
        $value >>= 7;
        if ($value != 0) $byte |= 0x80;
        $result .= chr($byte);
        if ($value == 0) break;
    }
    return $result;
}

function readVarInt($sock): int {
    $result = 0;
    for ($i = 0; $i < 5; $i++) {
        $byte = fread($sock, 1);
        if ($byte === false || $byte === '') return -1;
        $byte = ord($byte);
        $result |= ($byte & 0x7F) << (7 * $i);
        if (($byte & 0x80) == 0) break;
    }
    if ($result >= (1 << 31)) $result -= (1 << 32);
    return $result;
}

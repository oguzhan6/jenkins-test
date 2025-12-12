<?php
header('Content-Type: application/json');

function parseUptime($uptime_str) {
    preg_match('/up\s+(.*?),\s+\d+\s+user/', $uptime_str, $matches);
    return isset($matches[1]) ? trim($matches[1]) : 'N/A';
}

function parseMemory($free_str) {
    preg_match('/Mem:\s+(\d+)\s+(\d+)/', $free_str, $matches);
    if (count($matches) < 3) return ['total' => 0, 'used' => 0, 'percent' => 0];
    
    $total = (int)$matches[1];
    $used = (int)$matches[2];
    $percent = $total > 0 ? round(($used / $total) * 100) : 0;
    
    return [
        'total' => $total,
        'used' => $used,
        'percent' => $percent
    ];
}

function parseCpu($top_str) {
    // Looks for a line like: %Cpu(s):  0.3 us,  0.2 sy,  0.0 ni, 99.5 id,  0.0 wa,  0.0 hi,  0.0 si,  0.0 st
    preg_match('/%Cpu\(s\):.*,\s*([0-9.]+)\s*id/', $top_str, $matches);
    if (count($matches) < 2) return ['percent' => 0];

    $idle = (float)$matches[1];
    $percent = round(100 - $idle, 1);
    return ['percent' => $percent];
}

try {
    $uptime = shell_exec('uptime');
    $memory = shell_exec('free -m');
    $cpu_info = shell_exec('top -bn1');

    if ($uptime === null || $memory === null || $cpu_info === null) {
        throw new Exception("One or more shell commands failed to execute.");
    }

    $stats = [
        'uptime' => parseUptime($uptime),
        'memory' => parseMemory($memory),
        'cpu' => parseCpu($cpu_info)
    ];

    echo json_encode($stats);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve system stats.',
        'detail' => $e->getMessage()
    ]);
}
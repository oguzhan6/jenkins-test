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

function parseDisk($df_str) {
    preg_match('/(\d+)[MGTPE]?\s+(\d+)[MGTPE]?\s+(\d+)[MGTPE]?\s+(\d+)%\s+\/var\/www\/html/', $df_str, $matches);
    if (count($matches) < 5) return ['total' => 'N/A', 'used' => 'N/A', 'percent' => 0];
    
    // Note: This is a simplified parser. It assumes units are the same.
    $total = $matches[1];
    $used = $matches[2];
    $percent = (int)$matches[4];

    return [
        'total' => $total,
        'used' => $used,
        'percent' => $percent
    ];
}

try {
    $uptime = shell_exec('uptime');
    $memory = shell_exec('free -m');
    $disk = shell_exec('df -h /var/www/html');

    if ($uptime === null || $memory === null || $disk === null) {
        throw new Exception("One or more shell commands failed to execute.");
    }

    $stats = [
        'uptime' => parseUptime($uptime),
        'memory' => parseMemory($memory),
        'disk' => parseDisk($disk)
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


<?php
// state.php — Orimansi Church Timer backend
// Handles timer state + PIN verification

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$stateFile = __DIR__ . '/state.json';
$pinFile   = __DIR__ . '/pin.json';   // stores the hashed PIN

// ── Default PIN is 1234 (change via ?action=set-pin after deploy) ──
// To change PIN: POST { "command": "set-pin", "pin": "YOURPIN" }
$defaultPin = password_hash('1234', PASSWORD_DEFAULT);

// ── Default state ──
$defaultState = [
    'minutes' => 0,
    'command' => 'idle',
    'setAt'   => 0,
];

function readState($file, $default) {
    if (!file_exists($file)) return $default;
    $data = json_decode(file_get_contents($file), true);
    return $data ?: $default;
}

function writeState($file, $state) {
    file_put_contents($file, json_encode($state), LOCK_EX);
}

function getStoredHash($pinFile, $defaultPin) {
    if (!file_exists($pinFile)) return $defaultPin;
    $data = json_decode(file_get_contents($pinFile), true);
    return $data['hash'] ?? $defaultPin;
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET — timer page polls this (no auth needed) ──
if ($method === 'GET') {
    echo json_encode(readState($stateFile, $defaultState));
    exit;
}

// ── POST — all write operations ──
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    if (!$body || !isset($body['command'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $command = $body['command'];

    // ── PIN verification (no auth token needed for this one) ──
    if ($command === 'verify-pin') {
        $pin = strval($body['pin'] ?? '');
        if ($pin === '') {
            http_response_code(400);
            echo json_encode(['error' => 'PIN required']);
            exit;
        }
        $hash = getStoredHash($pinFile, $defaultPin);
        if (password_verify($pin, $hash)) {
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Incorrect PIN']);
        }
        exit;
    }

    // ── All other commands require PIN in the request ──
    $pin = strval($body['pin'] ?? '');
    $hash = getStoredHash($pinFile, $defaultPin);

    if ($pin === '' || !password_verify($pin, $hash)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    // ── Change PIN ──
    if ($command === 'set-pin') {
        $newPin = strval($body['new_pin'] ?? '');
        if (strlen($newPin) < 4) {
            http_response_code(400);
            echo json_encode(['error' => 'PIN must be at least 4 characters']);
            exit;
        }
        writeState($pinFile, ['hash' => password_hash($newPin, PASSWORD_DEFAULT)]);
        echo json_encode(['ok' => true, 'message' => 'PIN updated']);
        exit;
    }

    // ── Timer commands: start / reset / idle ──
    $minutes = intval($body['minutes'] ?? 0);

    if (!in_array($command, ['start', 'reset', 'idle'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid command']);
        exit;
    }
    if ($command === 'start' && ($minutes < 1 || $minutes > 600)) {
        http_response_code(400);
        echo json_encode(['error' => 'Minutes must be 1–600']);
        exit;
    }

    $state = [
        'minutes' => $minutes,
        'command' => $command,
        'setAt'   => time(),
    ];

    writeState($stateFile, $state);
    echo json_encode(['ok' => true, 'state' => $state]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
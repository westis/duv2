<?php
// File: /api/v1/auth-test.php
header('Content-Type: application/json');

// Load .env
$envPath = __DIR__ . '/../../.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value);
    }
}

// DB config
$host = $env['DB_HOST'] ?? 'localhost';
$db   = $env['DB_NAME'] ?? '';
$user = $env['DB_USER'] ?? '';
$pass = $env['DB_PASS'] ?? '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'DB connect failed']);
    exit;
}

// Read API key
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
if (empty($apiKey)) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'Missing API key']);
    exit;
}

// Validate API key
$stmt = $pdo->prepare(
    'SELECT ak.`key`, ak.tier, u.username, u.role
     FROM api_keys ak
     JOIN users u ON ak.user_id = u.user_id
     WHERE ak.`key` = :key AND ak.active = 1'
);
$stmt->execute(['key' => $apiKey]);
$auth = $stmt->fetch();

if (!$auth) {
    http_response_code(401);
    echo json_encode(['status'=>'error','message'=>'Invalid API key']);
    exit;
}

// Success response
echo json_encode([
    'status' => 'success',
    'message' => 'API key valid',
    'user' => [
        'username' => $auth['username'],
        'role' => $auth['role'],
    ],
    'tier' => $auth['tier']
]);
?> 
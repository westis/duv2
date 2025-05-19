<?php
/**
 * helpers.php
 *
 * Common helper functions for DUV API authentication and database access.
 * Includes database connection, authentication, and response helpers.
 */

function connectToDatabase() {
    // Load configuration from a separate config file if available
    $config = [
        'host' => 'localhost',
        'dbname' => 'ultradb',
        'user' => 'your_db_user',
        'pass' => 'your_db_password',
        'charset' => 'utf8mb4',
    ];
    if (file_exists(__DIR__ . '/config.php')) {
        $config = array_merge($config, include __DIR__ . '/config.php');
    }
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    try {
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 500,
            'code' => 'DB_CONNECTION_ERROR',
            'message' => 'Database connection failed',
            'details' => $e->getMessage(),
        ]);
        exit;
    }
}

/**
 * Validate API key from request header against the database.
 * @param PDO $pdo
 * @param string $apiKey
 * @return array|false User info array if valid, false otherwise
 */
function validateApiKey($pdo, $apiKey) {
    if (!$apiKey) return false;
    $stmt = $pdo->prepare('SELECT user_id, tier, active, expires FROM api_keys WHERE `key` = ?');
    $stmt->execute([$apiKey]);
    $row = $stmt->fetch();
    if ($row && $row['active'] && (!$row['expires'] || strtotime($row['expires']) > time())) {
        return [
            'user_id' => $row['user_id'],
            'tier' => $row['tier'],
            'via' => 'apiKey',
        ];
    }
    return false;
}

/**
 * Validate PHP session for web UI authentication.
 * @return array|false User info array if valid, false otherwise
 */
function validateSession() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) return false;
    return [
        'user_id' => $_SESSION['user_id'],
        'tier' => $_SESSION['role'],
        'via' => 'session',
    ];
}

/**
 * Authenticate the request using API key or session, enforcing required tier.
 * @param PDO $pdo
 * @param string $requiredTier
 * @return array User info if authenticated, else sends error and exits
 */
function authenticate($pdo, $requiredTier = 'public') {
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
    $user = validateApiKey($pdo, $apiKey);
    $method = 'apiKey';
    if (!$user) {
        $user = validateSession();
        $method = 'session';
    }
    if (!$user) {
        logAuthenticationAttempt(false, $method, ['reason' => 'No valid credentials']);
        errorResponse(401, 'AUTHENTICATION_ERROR', 'Authentication required');
    }
    if (!checkUserPermission($user, $requiredTier)) {
        logAuthenticationAttempt(false, $method, ['user_id' => $user['user_id'], 'tier' => $user['tier'], 'required' => $requiredTier]);
        errorResponse(403, 'AUTHORIZATION_ERROR', 'Insufficient permissions');
    }
    logAuthenticationAttempt(true, $method, ['user_id' => $user['user_id'], 'tier' => $user['tier']]);
    return $user;
}

/**
 * Output a JSON response with status code and proper headers.
 * @param mixed $data
 * @param int $status
 */
function outputJson($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Check if user has required tier/role.
 * @param array $user
 * @param string $requiredTier
 * @return bool
 */
function checkUserPermission($user, $requiredTier) {
    $tiers = ['public' => 0, 'basic' => 1, 'premium' => 2, 'admin' => 3];
    $userTier = $user['tier'] ?? 'public';
    return $tiers[$userTier] >= $tiers[$requiredTier];
}

/**
 * Log authentication attempts (success/failure, method, details).
 * @param bool $success
 * @param string $method
 * @param array $details
 */
function logAuthenticationAttempt($success, $method, $details = []) {
    $log = [
        'timestamp' => date('c'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'success' => $success,
        'method' => $method,
        'details' => $details,
    ];
    // Simple file log; in production, use a proper logger
    file_put_contents(__DIR__ . '/auth.log', json_encode($log) . "\n", FILE_APPEND);
}

/**
 * Set CORS headers and handle preflight OPTIONS requests.
 */
function handleCors() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: X-API-Key, Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Send a standardized error response and exit.
 * @param int $status
 * @param string $code
 * @param string $message
 * @param array $details
 */
function errorResponse($status, $code, $message, $details = []) {
    outputJson([
        'status' => $status,
        'code' => $code,
        'message' => $message,
        'details' => $details,
    ], $status);
} 
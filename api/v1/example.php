<?php
// File: /api/v1/example.php
header('Content-Type: application/json');

// 1. Attempt to load .env file (assuming it's in the parent directory of 'api')
$envPath = __DIR__ . '/../../.env'; // Correct path from /api/v1/ to project root
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) { // Skip comments
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $env[trim($name)] = trim($value);
    }
}

if (empty($env['DB_HOST']) || empty($env['DB_NAME']) || empty($env['DB_USER'])) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'DB_HOST, DB_NAME, or DB_USER is missing from .env file.',
        'env_path_checked' => $envPath,
        'env_loaded' => !empty($env)
    ]);
    exit;
}

// DB_PASS can be legitimately empty for root in XAMPP, so check if it's set.
// If it's not set at all, that's an error. An empty string is fine for the PDO connection.
if (!array_key_exists('DB_PASS', $env)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'DB_PASS is missing from .env file (it can be blank, e.g., DB_PASS=, but must be present).',
        'env_path_checked' => $envPath,
        'env_loaded' => !empty($env)
    ]);
    exit;
}

// 2. Database connection details from .env
$host = $env['DB_HOST'];
$db   = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Let's try to fetch a few runners
    // Adjust 'tperson' and 'PersonID', 'Name', 'Nationality' if your columns are different
    $stmt = $pdo->query('SELECT PersonID, FirstName, Nationality FROM tperson LIMIT 5');
    $exampleRunners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($exampleRunners)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Successfully connected to the database! The example query for runners returned no data (is the tperson table empty or has different column names?).',
            'db_host' => $host,
            'db_name' => $db,
            'data' => []
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Successfully connected to the database and fetched example runners!',
            'db_host' => $host,
            'db_name' => $db,
            'data_source_table' => 'tperson',
            'example_data' => $exampleRunners
        ]);
    }

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database operation failed: ' . $e->getMessage(),
        'db_host' => $host,
        'db_name' => $db
    ]);
}
?>
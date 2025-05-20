<?php
require_once __DIR__ . '/helpers.php';

handleCors();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');
        outputJson([
            'status' => 405,
            'code' => 'METHOD_NOT_ALLOWED',
            'message' => 'Only GET requests are allowed.'
        ], 405);
    }

    $pdo = connectToDatabase();
    $user = authenticate($pdo, 'public');

    // Determine metadata resource
    $resource = $_GET['resource'] ?? '';
    switch ($resource) {
        case 'disciplines':
            // Load standard recordable disciplines from config
            $config = include __DIR__ . '/config.php';
            $disc = $config['recordDisciplines'] ?? ['distances' => [], 'durations' => []];
            $distances = $disc['distances'];
            $durations = $disc['durations'];

            outputJson([
                'distances' => $distances,
                'durations' => $durations
            ]);
            break;

        default:
            errorResponse(404, 'RESOURCE_NOT_FOUND', "Metadata resource '{$resource}' not found");
    }
} catch (PDOException $e) {
    outputJson([
        'status' => 500,
        'code' => 'INTERNAL_SERVER_ERROR',
        'message' => 'Database error: ' . $e->getMessage()
    ], 500);
} catch (Throwable $e) {
    outputJson([
        'status' => 500,
        'code' => 'INTERNAL_SERVER_ERROR',
        'message' => 'Unexpected error: ' . $e->getMessage()
    ], 500);
} 
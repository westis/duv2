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

    // Validate runnerId parameter
    if (!isset($_GET['runnerId']) || !preg_match('/^[0-9]+$/', $_GET['runnerId'])) {
        outputJson([
            'status' => 400,
            'code' => 'INVALID_PARAMETER',
            'message' => 'runnerId is required and must be a positive integer.'
        ], 400);
    }
    $runnerId = intval($_GET['runnerId']);

    // Get unified runner profile
    $profile = getUnifiedRunnerProfile($pdo, $runnerId);
    if (!$profile) {
        outputJson([
            'status' => 404,
            'code' => 'RESOURCE_NOT_FOUND',
            'message' => "Runner with ID '$runnerId' not found"
        ], 404);
    }

    // Format response according to OpenAPI Runner schema
    $response = [
        'runnerId' => (string)$profile['PersonID'],
        'firstName' => $profile['FirstName'],
        'lastName' => $profile['LastName'],
        'originalName' => $profile['OrigName'] ?? null,
        'originalNameFirstName' => $profile['OrigNameF'] ?? null,
        'originalNameLastName' => $profile['OrigNameL'] ?? null,
        'nationality' => $profile['Nationality'],
        'nationality2' => $profile['Nat2'] ?? null,
        'gender' => $profile['Gender'],
        'birthYear' => isset($profile['YOB']) ? (int)$profile['YOB'] : null,
        'birthDate' => $profile['Birthday'] ?? null,
        'deathDate' => $profile['DOD'] ?? null,
        'club' => $profile['Club'] ?? null,
        'city' => $profile['City'] ?? null,
        'totalMileage' => isset($profile['Mileage']) ? (float)$profile['Mileage'] : null,
        'raceCount' => isset($profile['RaceCnt']) ? (int)$profile['RaceCnt'] : null,
        'comments' => $profile['Comments'] ?? null,
        'privacy' => isset($profile['Privacy']) ? (int)$profile['Privacy'] : null,
        'linkedProfiles' => array_map(function($p) {
            return [
                'runnerId'     => (string)$p['PersonID'],
                'firstName'    => $p['FirstName'],
                'lastName'     => $p['LastName'],
                'nationality'  => $p['Nationality'],
            ];
        }, $profile['linkedProfiles']),
    ];

    outputJson($response);
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
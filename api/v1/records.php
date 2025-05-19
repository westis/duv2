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

    // Parse optional pagination parameters
    $limit = isset($_GET['limit']) ? max(1, min(1000, intval($_GET['limit']))) : 100;
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

    // Determine record category (type) to retrieve
    $type = $_GET['type'] ?? ($_GET['recordType'] ?? 'world');
    $allowedTypes = ['world','national','age_group','course','german'];
    if (!in_array($type, $allowedTypes, true)) {
        outputJson([
            'status' => 400,
            'code' => 'INVALID_PARAMETER',
            'message' => 'Invalid record type.'
        ], 400);
    }
    switch ($type) {
        case 'german':
            // Count total German records
            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM trecordger');
            $countStmt->execute();
            $total = (int)$countStmt->fetchColumn();
            // Fetch records
            $stmt = $pdo->prepare(
                'SELECT Dist AS recordId, RecType, Perf AS recordValue, CreaDate AS recordDate FROM trecordger LIMIT ? OFFSET ?'
            );
            $stmt->execute([$limit, $offset]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $records = [];
            foreach ($rows as $r) {
                $records[] = [
                    'recordId'    => $r['recordId'] . '-' . $r['RecType'],
                    'perfId'      => null,
                    'runnerId'    => null,
                    'recordType'  => 'german',
                    'recordValue' => $r['recordValue'],
                    'recordDate'  => $r['recordDate'],
                    'eventId'     => null,
                ];
            }
            break;
        default:
            // Other types not yet implemented
            $total = 0;
            $records = [];
    }
    // Build pagination URLs
    $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    $next = ($offset + $limit < $total) ? $baseUrl . '?' . http_build_query(array_merge($query, ['offset' => $offset + $limit, 'limit' => $limit])) : null;
    $prev = ($offset > 0) ? $baseUrl . '?' . http_build_query(array_merge($query, ['offset' => max(0, $offset - $limit), 'limit' => $limit])) : null;
    $response = [
        'data' => $records,
        'pagination' => [
            'total'    => $total,
            'limit'    => $limit,
            'offset'   => $offset,
            'next'     => $next,
            'previous' => $prev,
        ],
    ];
    outputJson($response);
    // End of records logic
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
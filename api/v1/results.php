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
    $user = authenticate($pdo, 'public'); // Allow public and authenticated access

    // Parse filters
    $filters = [];
    $params = [];

    // Filter by raceId
    if (isset($_GET['raceId'])) {
        $filters[] = 'tp.EventID = ?';
        $params[] = $_GET['raceId'];
    }
    // Filter by runnerId
    if (isset($_GET['runnerId'])) {
        $filters[] = 'tp.PersonID = ?';
        $params[] = $_GET['runnerId'];
    }

    // EARLY EXIT: Require at least one filter
    if (empty($filters)) {
        outputJson([
            'status' => 400,
            'code' => 'MISSING_FILTER',
            'message' => 'You must provide at least raceId or runnerId as a filter.'
        ], 400);
    }

    // Filter by maxRank
    if (isset($_GET['maxRank'])) {
        $maxRank = intval($_GET['maxRank']);
        if ($maxRank > 0) {
            $filters[] = 'tp.RankTotal <= ?';
            $params[] = $maxRank;
        } else {
            outputJson([
                'status' => 400,
                'code' => 'INVALID_PARAMETER',
                'message' => 'maxRank must be a positive integer.'
            ], 400);
        }
    }

    // Sorting
    $sortable = [
        'timeGross' => 'tp.TimeGross',
        'rank' => 'tp.RankTotal',
        'raceDate' => 'e.Startdate',
        'runnerLastName' => 'p.LastName'
    ];
    $sortBy = isset($_GET['sortBy']) && isset($sortable[$_GET['sortBy']]) ? $sortable[$_GET['sortBy']] : 'tp.RankTotal';
    $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';

    // Pagination
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 100;
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

    // Time calculation method
    $timeType = isset($_GET['timeType']) && in_array($_GET['timeType'], ['net', 'gross']) ? $_GET['timeType'] : 'net';

    // Count total
    $countSql = 'SELECT COUNT(*) FROM tperformance tp JOIN tevent e ON tp.EventID = e.EventID JOIN tperson p ON tp.PersonID = p.PersonID';
    if ($filters) {
        $countSql .= ' WHERE ' . implode(' AND ', $filters);
    }
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    // Main query with filters, sorting, and pagination
    $sql = 'SELECT 
        tp.PerfID as perfId,
        tp.EventID as raceId,
        tp.PersonID as runnerId,
        tp.RankTotal as rank,
        ' . ($timeType === 'gross' ? 'tp.TimeGross' : 'tp.TimeNet') . ' as time,
        tp.Distance as distance,
        tp.RankMW as rankGender,
        tp.CatGER as categoryGerman,
        tp.RankCatGER as rankCategoryGerman,
        tp.CatEvent as categoryEvent,
        tp.RankCatEvent as rankCategoryEvent,
        tp.CatInt as categoryIAU,
        tp.RankCatInt as rankCategoryIAU,
        tp.Scores as scores,
        tp.Age_Y as age,
        tp.RankIntYear as rankInternationalYear,
        tp.RankNatYear as rankNationalYear,
        tp.Exclude as isExcluded,
        tp.Comments as comments,
        e.EventName as raceName,
        e.Country as raceCountry,
        e.City as raceCity,
        e.Startdate as raceDate,
        p.FirstName as runnerFirstName,
        p.LastName as runnerLastName,
        p.Nationality as runnerNationality,
        p.Gender as runnerGender
    FROM tperformance tp
    JOIN tevent e ON tp.EventID = e.EventID
    JOIN tperson p ON tp.PersonID = p.PersonID';
    if ($filters) {
        $sql .= ' WHERE ' . implode(' AND ', $filters);
    }
    $sql .= " ORDER BY $sortBy $order LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Format time fields as strings, handle DNF/DSQ if present
    foreach ($results as &$row) {
        if (isset($row['time'])) {
            $row['time'] = (string)$row['time'];
            // Example: if DNF/DSQ is encoded in Exclude or Comments, add logic here
        }
    }

    // Pagination URLs
    $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
    $query = $_GET;
    $next = null;
    $prev = null;
    if ($offset + $limit < $total) {
        $query['offset'] = $offset + $limit;
        $query['limit'] = $limit;
        $next = $baseUrl . '?' . http_build_query($query);
    }
    if ($offset > 0) {
        $query['offset'] = max(0, $offset - $limit);
        $query['limit'] = $limit;
        $prev = $baseUrl . '?' . http_build_query($query);
    }

    // Final output: ResultsResponse schema
    outputJson([
        'data' => $results,
        'pagination' => [
            'total' => intval($total),
            'limit' => $limit,
            'offset' => $offset,
            'next' => $next,
            'previous' => $prev
        ]
    ]);
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
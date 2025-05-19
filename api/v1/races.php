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
    $user = authenticate($pdo, 'public'); // Adjust tier as needed

    // Parse filters
    $filters = [];
    $params = [];

    // Year filter
    if (isset($_GET['year'])) {
        $year = intval($_GET['year']);
        if ($year > 1900 && $year < 2100) {
            $filters[] = 'YEAR(Startdate) = ?';
            $params[] = $year;
        } else {
            outputJson([
                'status' => 400,
                'code' => 'INVALID_PARAMETER',
                'message' => 'Invalid year parameter.'
            ], 400);
        }
    }
    // Country filter
    if (isset($_GET['country'])) {
        $country = strtoupper(trim($_GET['country']));
        if (preg_match('/^[A-Z]{3}$/', $country)) {
            $filters[] = 'Country = ?';
            $params[] = $country;
        } else {
            outputJson([
                'status' => 400,
                'code' => 'INVALID_PARAMETER',
                'message' => 'Invalid country parameter.'
            ], 400);
        }
    }
    // Distance range filter
    if (isset($_GET['distanceMin'])) {
        $distanceMin = floatval($_GET['distanceMin']);
        $filters[] = 'NormLen >= ?';
        $params[] = $distanceMin;
    }
    if (isset($_GET['distanceMax'])) {
        $distanceMax = floatval($_GET['distanceMax']);
        $filters[] = 'NormLen <= ?';
        $params[] = $distanceMax;
    }
    // City filter
    if (isset($_GET['city'])) {
        $city = trim($_GET['city']);
        if ($city !== '') {
            $filters[] = 'City LIKE ?';
            $params[] = "%$city%";
        }
    }
    // Edition filter
    if (isset($_GET['edition'])) {
        $edition = trim($_GET['edition']);
        if ($edition !== '') {
            $filters[] = 'Edition = ?';
            $params[] = $edition;
        }
    }
    // EventType filter
    if (isset($_GET['type'])) {
        $type = intval($_GET['type']);
        $filters[] = 'EventType = ?';
        $params[] = $type;
    }
    // DateFrom filter
    if (isset($_GET['dateFrom'])) {
        $dateFrom = $_GET['dateFrom'];
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $filters[] = 'Startdate >= ?';
            $params[] = $dateFrom;
        }
    }
    // DateTo filter
    if (isset($_GET['dateTo'])) {
        $dateTo = $_GET['dateTo'];
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $filters[] = 'Startdate <= ?';
            $params[] = $dateTo;
        }
    }
    // Duration filter
    if (isset($_GET['duration'])) {
        $duration = trim($_GET['duration']);
        if ($duration !== '') {
            $filters[] = 'Duration = ?';
            $params[] = $duration;
        }
    }
    // ResultsStatus filter
    if (isset($_GET['resultsStatus'])) {
        $allowed = ['N','C','P','O','I','R','S','T','Z'];
        if (in_array($_GET['resultsStatus'], $allowed, true)) {
            $filters[] = 'Results = ?';
            $params[] = $_GET['resultsStatus'];
        }
    }
    // IAULabel filter
    if (isset($_GET['iauLabel'])) {
        $allowed = ['Y','N','T','G','S','B'];
        if (in_array($_GET['iauLabel'], $allowed, true)) {
            $filters[] = 'IAULabel = ?';
            $params[] = $_GET['iauLabel'];
        }
    }
    // RecordProof filter
    if (isset($_GET['recordProof'])) {
        $allowed = ['Y','N'];
        if (in_array($_GET['recordProof'], $allowed, true)) {
            $filters[] = 'RecordProof = ?';
            $params[] = $_GET['recordProof'];
        }
    }
    // FinisherMin/Max filters
    if (isset($_GET['finisherMin'])) {
        $finMin = intval($_GET['finisherMin']);
        $filters[] = '(FinisherM + FinisherW) >= ?';
        $params[] = $finMin;
    }
    if (isset($_GET['finisherMax'])) {
        $finMax = intval($_GET['finisherMax']);
        $filters[] = '(FinisherM + FinisherW) <= ?';
        $params[] = $finMax;
    }
    // FinisherMenMin/Max filters
    if (isset($_GET['finisherMenMin'])) {
        $finMenMin = intval($_GET['finisherMenMin']);
        $filters[] = 'FinisherM >= ?';
        $params[] = $finMenMin;
    }
    if (isset($_GET['finisherMenMax'])) {
        $finMenMax = intval($_GET['finisherMenMax']);
        $filters[] = 'FinisherM <= ?';
        $params[] = $finMenMax;
    }
    // FinisherWomenMin/Max filters
    if (isset($_GET['finisherWomenMin'])) {
        $finWomenMin = intval($_GET['finisherWomenMin']);
        $filters[] = 'FinisherW >= ?';
        $params[] = $finWomenMin;
    }
    if (isset($_GET['finisherWomenMax'])) {
        $finWomenMax = intval($_GET['finisherWomenMax']);
        $filters[] = 'FinisherW <= ?';
        $params[] = $finWomenMax;
    }
    // Cup filter (join tcup)
    if (isset($_GET['cup'])) {
        $cup = trim($_GET['cup']);
        if ($cup !== '') {
            $filters[] = 'EventID IN (SELECT EventID FROM tcup WHERE Cupname = ?)';
            $params[] = $cup;
        }
    }
    // CreatedAfter filter
    if (isset($_GET['createdAfter'])) {
        $createdAfter = $_GET['createdAfter'];
        if (preg_match('/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}:\d{2}Z)?$/', $createdAfter)) {
            $filters[] = 'CreatedOn >= ?';
            $params[] = $createdAfter;
        }
    }
    // ChangedAfter filter
    if (isset($_GET['changedAfter'])) {
        $changedAfter = $_GET['changedAfter'];
        if (preg_match('/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}:\d{2}Z)?$/', $changedAfter)) {
            $filters[] = 'ChangedOn >= ?';
            $params[] = $changedAfter;
        }
    }
    // Name search (full-text)
    if (isset($_GET['search'])) {
        $search = trim($_GET['search']);
        if ($search !== '') {
            $filters[] = '(EventName LIKE ? OR City LIKE ? OR PromOrg LIKE ? OR Comments LIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    }

    // Sorting
    $sortable = [
        'date' => 'Startdate',
        'name' => 'EventName',
        'distance' => 'NormLen',
        'country' => 'Country',
        'city' => 'City'
    ];
    $sortBy = isset($_GET['sortBy']) && isset($sortable[$_GET['sortBy']]) ? $sortable[$_GET['sortBy']] : 'Startdate';
    $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'asc') ? 'ASC' : 'DESC';

    // Pagination
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 100;
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

    // Count total
    $countSql = 'SELECT COUNT(*) FROM tevent';
    if ($filters) {
        $countSql .= ' WHERE ' . implode(' AND ', $filters);
    }
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    // Build SQL
    $sql = 'SELECT EventID as raceId, EventName as name, Edition as edition, Country as country, City as city, Startdate as date, Enddate as endDate, NormLen as distance, Length as distanceOriginal, EventType as eventType, AltitudeDiff as altitude, FinisherM + FinisherW as finishers, FinisherM as finishersMen, FinisherW as finishersWomen, TimeLimit as timeLimit, Results as resultsStatus, IAULabel as iauLabel, RecordProof as recordProof, URL as website, PromOrg as organizer FROM tevent';
    if ($filters) {
        $sql .= ' WHERE ' . implode(' AND ', $filters);
    }
    $sql .= " ORDER BY $sortBy $order LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $races = $stmt->fetchAll();

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

    outputJson([
        'data' => $races,
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
<?php
require_once __DIR__ . '/helpers.php';

handleCors();

// --- RECORDS ENDPOINT TEMPORARILY DISABLED ---
// The records endpoint is not implemented in this version because the current database
// does not include the pre-calculated `trecords` table. Once the new schema is available,
// this endpoint will be re-implemented to use the optimized approach described below.
// See documentation at the end of this file for the recommended strategy.

errorResponse(501, 'NOT_IMPLEMENTED', 'The /records endpoint is temporarily disabled. Awaiting new database structure with trecords table.');

// --- END OF TEMPORARY DISABLE ---

/*
RECOMMENDED FUTURE IMPLEMENTATION:

1. Create a denormalized `trecords` table as described in the migration documentation.
2. Implement a background job to update this table after new performances are added.
3. Update this endpoint to query from `trecords` using filters for discipline, recordType, country, region, gender, etc.
4. See the documentation in the migration notes for full SQL and PHP examples.
*/

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

    // Load allowed disciplines from config
    $configRec = include __DIR__ . '/config.php';
    $allowedDistances = $configRec['recordDisciplines']['distances'];
    $allowedDurations = $configRec['recordDisciplines']['durations'];

    // Parse optional pagination parameters
    $limit = isset($_GET['limit']) ? max(1, min(1000, intval($_GET['limit']))) : 100;
    $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;

    // Require recordType parameter
    if (!isset($_GET['recordType'])) {
        errorResponse(400, 'MISSING_PARAMETER', 'recordType query parameter is required');
    }
    $type = $_GET['recordType'];
    $allowedTypes = ['world', 'national', 'region'];
    if (!in_array($type, $allowedTypes, true)) {
        errorResponse(400, 'INVALID_PARAMETER', 'recordType must be one of: world, national, region');
    }

    // Enforce recordType-specific parameters
    if ($type === 'national') {
        if (!isset($_GET['country'])) {
            errorResponse(400, 'INVALID_PARAMETER', 'country is required for national records');
        }
        $country = strtoupper($_GET['country']);
        if (!preg_match('/^[A-Z]{3}$/', $country)) {
            errorResponse(400, 'INVALID_PARAMETER', 'country must be a 3-letter code');
        }
    } elseif (isset($_GET['country'])) {
        errorResponse(400, 'INVALID_PARAMETER', 'country parameter only allowed for national recordType');
    }
    if ($type === 'region') {
        if (!isset($_GET['region']) || !ctype_digit($_GET['region'])) {
            errorResponse(400, 'INVALID_PARAMETER', 'region is required for regional records');
        }
        $region = intval($_GET['region']);
    } elseif (isset($_GET['region'])) {
        errorResponse(400, 'INVALID_PARAMETER', 'region parameter only allowed for region recordType');
    }

    // Base filters
    $baseFilters = ['e.RecordProof = ?'];
    $baseParams = ['Y'];

    // Unified discipline parameter
    if (isset($_GET['discipline'])) {
        $disc = $_GET['discipline'];
        
        if (in_array($disc, $allowedDistances, true)) {
            // For distance-based disciplines
            $baseFilters[] = 'e.Length = ?';
            $baseParams[] = $disc;
        } elseif (in_array($disc, $allowedDurations, true)) {
            // For time-based disciplines
            $baseFilters[] = 'e.Duration = ?';
            $baseParams[] = $disc;
        } else {
            errorResponse(400, 'INVALID_PARAMETER', 'discipline must be one of: ' . 
                          implode(', ', array_merge($allowedDistances, $allowedDurations)));
        }
    } else {
        // If no discipline specified, allow any of the standard ones
        $distanceOrs = array_map(function($d) { return 'e.Length = ?'; }, $allowedDistances);
        $durationOrs = array_map(function($d) { return 'e.Duration = ?'; }, $allowedDurations);
        
        $baseFilters[] = '((' . implode(' OR ', $distanceOrs) . ') OR (' . implode(' OR ', $durationOrs) . '))';
        $baseParams = array_merge($baseParams, $allowedDistances, $allowedDurations);
    }

    // Additional record filters: gender, age group, surface
    if (isset($_GET['gender'])) {
        $gender = $_GET['gender'];
        if (in_array($gender, ['M','W','X'], true)) {
            $baseFilters[] = 'p.Gender = ?';
            $baseParams[] = $gender;
        } else {
            errorResponse(400, 'INVALID_PARAMETER', 'gender must be one of M, W, X');
        }
    }
    if (isset($_GET['ageCategory'])) {
        $ageCategory = $_GET['ageCategory'];
        $baseFilters[] = 'tp.CatEvent = ?';
        $baseParams[] = $ageCategory;
    }
    if (isset($_GET['surface'])) {
        $surface = strtolower($_GET['surface']);
        $surfaceMap = ['road'=>1, 'trail'=>2, 'track'=>3, 'indoor'=>4];
        if (isset($surfaceMap[$surface])) {
            $baseFilters[] = 'e.EventType = ?';
            $baseParams[] = $surfaceMap[$surface];
        } else {
            errorResponse(400, 'INVALID_PARAMETER', 'surface must be one of road, trail, track, indoor');
        }
    }

    // Create base WHERE clause that will be used in all queries
    $baseWhere = 'WHERE ' . implode(' AND ', $baseFilters);

    // Add recordType-specific filters
    $typeWhere = '';
    $typeParams = [];
    
    if ($type === 'national') {
        $typeWhere = ' AND p.Nationality = ?';
        $typeParams[] = $country;
    } elseif ($type === 'region') {
        $typeWhere = ' AND EXISTS (SELECT 1 FROM tcountry c WHERE c.Code = p.Nationality AND c.Region = ?)';
        $typeParams[] = $region;
    }
    
    // Combine filters and parameters
    $fullWhere = $baseWhere . $typeWhere;
    $fullParams = array_merge($baseParams, $typeParams);

    // Get total count of distinct disciplines that match our criteria
    $countSql = "SELECT 
        COUNT(DISTINCT CASE 
            WHEN e.Duration IS NULL THEN CONCAT('dist:', e.Length) 
            ELSE CONCAT('time:', e.Duration) 
        END) AS total
        FROM tperformance tp
        JOIN tperson p ON tp.PersonID = p.PersonID
        JOIN tevent e ON tp.EventID = e.EventID
        $fullWhere";
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($fullParams);
    $total = (int)$countStmt->fetchColumn();

    // Query to fetch records, separating distance and time disciplines
    $sqlRec = "
    (
        -- Distance-based disciplines (fixed distance, variable time)
        SELECT
            CONCAT('dist:', e.Length) as disciplineId,
            tp.PerfID as perfId,
            tp.PersonID as runnerId,
            e.Length as discipline,
            'distance' as disciplineType,
            tp.TimeNet as recordValue,
            e.EventID as eventId,
            e.Startdate as recordDate
        FROM tperformance tp
        JOIN tperson p ON tp.PersonID = p.PersonID
        JOIN tevent e ON tp.EventID = e.EventID
        JOIN (
            -- Get the fastest time for each distance discipline
            SELECT 
                e_sub.Length as discipline,
                MIN(tp_sub.TimeNet) as best_time
            FROM tperformance tp_sub
            JOIN tperson p_sub ON tp_sub.PersonID = p_sub.PersonID
            JOIN tevent e_sub ON tp_sub.EventID = e_sub.EventID
            $fullWhere AND e_sub.Duration IS NULL
            GROUP BY e_sub.Length
        ) best ON e.Length = best.discipline AND tp.TimeNet = best.best_time
        $fullWhere AND e.Duration IS NULL
    )
    UNION
    (
        -- Time-based disciplines (fixed time, variable distance)
        SELECT
            CONCAT('time:', e.Duration) as disciplineId,
            tp.PerfID as perfId,
            tp.PersonID as runnerId,
            e.Duration as discipline,
            'time' as disciplineType,
            tp.Distance as recordValue,
            e.EventID as eventId,
            e.Startdate as recordDate
        FROM tperformance tp
        JOIN tperson p ON tp.PersonID = p.PersonID
        JOIN tevent e ON tp.EventID = e.EventID
        JOIN (
            -- Get the longest distance for each time discipline
            SELECT 
                e_sub.Duration as discipline,
                MAX(tp_sub.Distance) as best_distance
            FROM tperformance tp_sub
            JOIN tperson p_sub ON tp_sub.PersonID = p_sub.PersonID
            JOIN tevent e_sub ON tp_sub.EventID = e_sub.EventID
            $fullWhere AND e_sub.Length IS NULL
            GROUP BY e_sub.Duration
        ) best ON e.Duration = best.discipline AND tp.Distance = best.best_distance
        $fullWhere AND e.Length IS NULL
    )
    ORDER BY disciplineType, discipline
    LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($sqlRec);
    $stmt->execute($fullParams);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $records = [];
    foreach ($rows as $r) {
        $records[] = [
            'disciplineId' => $r['disciplineId'],
            'discipline'   => $r['discipline'],
            'disciplineType' => $r['disciplineType'],
            'perfId'      => (string)$r['perfId'],
            'runnerId'    => (string)$r['runnerId'],
            'recordType'  => $type,
            'recordValue' => $r['recordValue'],
            'recordDate'  => $r['recordDate'],
            'eventId'     => (string)$r['eventId'],
        ];
    }
    
    // Append runner profile to each record
    foreach ($records as &$rec) {
        $profile = getUnifiedRunnerProfile($pdo, intval($rec['runnerId']));
        if ($profile) {
            $rec['runner'] = [
                'runnerId'    => (string)$profile['PersonID'],
                'firstName'   => $profile['FirstName'],
                'lastName'    => $profile['LastName'],
                'nationality' => $profile['Nationality'],
            ];
        }
        
        // Get event details
        $eventSql = "SELECT EventName, City, Country FROM tevent WHERE EventID = ?";
        $eventStmt = $pdo->prepare($eventSql);
        $eventStmt->execute([(int)$rec['eventId']]);
        $eventData = $eventStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($eventData) {
            $rec['event'] = [
                'eventId'   => $rec['eventId'],
                'eventName' => $eventData['EventName'],
                'city'      => $eventData['City'],
                'country'   => $eventData['Country'],
            ];
        }
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
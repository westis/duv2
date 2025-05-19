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

    // --- LIST ENDPOINT LOGIC ---
    if (!isset($_GET['runnerId'])) {
        // Parse filters
        $filters = [];
        $params = [];
        if (isset($_GET['nationality'])) {
            $filters[] = 'Nationality = ?';
            $params[] = $_GET['nationality'];
        }
        if (isset($_GET['club'])) {
            $filters[] = 'Club LIKE ?';
            $params[] = '%' . $_GET['club'] . '%';
        }
        if (isset($_GET['city'])) {
            $filters[] = 'City LIKE ?';
            $params[] = '%' . $_GET['city'] . '%';
        }
        if (isset($_GET['gender'])) {
            $filters[] = 'Gender = ?';
            $params[] = $_GET['gender'];
        }
        if (isset($_GET['yob'])) {
            $filters[] = 'YOB = ?';
            $params[] = intval($_GET['yob']);
        }
        if (isset($_GET['raceCountMin'])) {
            $filters[] = 'RaceCnt >= ?';
            $params[] = intval($_GET['raceCountMin']);
        }
        if (isset($_GET['raceCountMax'])) {
            $filters[] = 'RaceCnt <= ?';
            $params[] = intval($_GET['raceCountMax']);
        }
        if (isset($_GET['mileageMin'])) {
            $filters[] = 'Mileage >= ?';
            $params[] = floatval($_GET['mileageMin']);
        }
        if (isset($_GET['mileageMax'])) {
            $filters[] = 'Mileage <= ?';
            $params[] = floatval($_GET['mileageMax']);
        }
        if (isset($_GET['search'])) {
            $filters[] = '(FirstName LIKE ? OR LastName LIKE ? OR OrigName LIKE ?)';
            $params[] = '%' . $_GET['search'] . '%';
            $params[] = '%' . $_GET['search'] . '%';
            $params[] = '%' . $_GET['search'] . '%';
        }
        // Sorting
        $sortable = ['name'=>'LastName', 'nationality'=>'Nationality', 'mileage'=>'Mileage', 'raceCount'=>'RaceCnt', 'yob'=>'YOB'];
        $sortBy = isset($_GET['sortBy']) && isset($sortable[$_GET['sortBy']]) ? $sortable[$_GET['sortBy']] : 'LastName';
        $order = (isset($_GET['order']) && strtolower($_GET['order']) === 'desc') ? 'DESC' : 'ASC';
        // Pagination
        $limit = isset($_GET['limit']) ? max(1, min(1000, intval($_GET['limit']))) : 100;
        $offset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        // Privacy: exclude runners with privacy >= 4 unless admin
        if ($user['tier'] !== 'admin') {
            $filters[] = 'Privacy < 4';
        }
        // Build WHERE clause
        $where = $filters ? ('WHERE ' . implode(' AND ', $filters)) : '';
        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM tperson $where";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        // Select only needed columns to optimize query
        $selectCols = implode(', ', [
            'PersonID', 'FirstName', 'LastName', 'OrigName', 'OrigNameF', 'OrigNameL',
            'Nationality', 'Nat2', 'Gender', 'YOB', 'Birthday', 'DOD',
            'Club', 'City', 'Mileage', 'RaceCnt', 'Comments', 'Privacy'
        ]);
        $sql = "SELECT $selectCols FROM tperson $where ORDER BY $sortBy $order LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Field visibility per tier (reuse logic)
        $tiers = ['public'=>0, 'basic'=>1, 'premium'=>2, 'admin'=>3];
        $userTier = $tiers[$user['tier']] ?? 0;
        $publicFields  = ['runnerId', 'firstName', 'lastName', 'nationality', 'gender'];
        $basicFields   = array_merge($publicFields, ['nationality2', 'birthYear', 'club', 'city']);
        $premiumFields = array_merge($basicFields, ['birthDate']);
        $adminFields   = array_merge($premiumFields, ['deathDate', 'privacy', 'comments', 'raceCount', 'totalMileage', 'linkedProfiles']);
        if ($userTier >= 3) {
            $allowed = $adminFields;
        } elseif ($userTier >= 2) {
            $allowed = $premiumFields;
        } elseif ($userTier >= 1) {
            $allowed = $basicFields;
        } else {
            $allowed = $publicFields;
        }
        // Map DB fields to API fields
        $result = [];
        foreach ($rows as $profile) {
            $item = [
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
                // linkedProfiles not included in list for now
            ];
            // Filter fields by allowed
            foreach (array_keys($item) as $key) {
                if (!in_array($key, $allowed, true)) {
                    unset($item[$key]);
                }
            }
            $result[] = $item;
        }
        // Pagination info
        $next = ($offset + $limit < $total) ? $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['offset'=>$offset+$limit])) : null;
        $prev = ($offset > 0) ? $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['offset'=>max(0,$offset-$limit)])) : null;
        outputJson([
            'data' => $result,
            'pagination' => [
                'total' => (int)$total,
                'limit' => $limit,
                'offset' => $offset,
                'next' => $next,
                'previous' => $prev,
            ]
        ]);
        exit;
    }

    // Now validate runnerId and run single-runner logic
    if (!preg_match('/^[0-9]+$/', $_GET['runnerId'])) {
        outputJson([
            'status' => 400,
            'code' => 'INVALID_PARAMETER',
            'message' => 'runnerId is required and must be a positive integer.'
        ], 400);
    }
    $runnerId = intval($_GET['runnerId']);
    // Cache unified runner profile for 5 minutes to reduce DB load
    $cacheKey = "runner_profile_{$runnerId}";
    if (function_exists('apcu_fetch') && ($cached = apcu_fetch($cacheKey)) !== false) {
        $profile = $cached;
    } else {
        $profile = getUnifiedRunnerProfile($pdo, $runnerId);
        if ($profile && function_exists('apcu_store')) {
            apcu_store($cacheKey, $profile, 300);
        }
    }
    if (!$profile) {
        outputJson([
            'status' => 404,
            'code' => 'RESOURCE_NOT_FOUND',
            'message' => "Runner with ID '{$runnerId}' not found"
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

    // Optional: include performance history
    if (isset($_GET['includePerformances']) && filter_var($_GET['includePerformances'], FILTER_VALIDATE_BOOLEAN)) {
        // Collect all related PersonIDs (main + linked)
        $personIds = [$profile['PersonID']];
        foreach ($profile['linkedProfiles'] as $p) {
            $personIds[] = $p['PersonID'];
        }
        // Pagination for performances
        $perfLimit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 100;
        $perfOffset = isset($_GET['offset']) ? max(0, intval($_GET['offset'])) : 0;
        // Prepare placeholders
        $placeholders = implode(',', array_fill(0, count($personIds), '?'));
        $perfSql = "SELECT
            tp.PerfID as perfId,
            tp.EventID as raceId,
            tp.PersonID as runnerId,
            tp.RankTotal as rank,
            tp.TimeGross as timeGross,
            tp.TimeNet as timeNet,
            tp.Distance as distance,
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
            e.Startdate as raceDate,
            e.EventName as raceName
        FROM tperformance tp
        JOIN tevent e ON tp.EventID = e.EventID
        WHERE tp.PersonID IN ($placeholders)
        ORDER BY tp.RankTotal ASC
        LIMIT $perfLimit OFFSET $perfOffset";
        $perfStmt = $pdo->prepare($perfSql);
        $perfStmt->execute($personIds);
        $response['performances'] = $perfStmt->fetchAll();
    }

    // Define tier levels
    $tiers = ['public'=>0, 'basic'=>1, 'premium'=>2, 'admin'=>3];
    $userTier = $tiers[$user['tier']] ?? 0;
    // Field visibility per authentication tier
    $publicFields  = ['runnerId', 'firstName', 'lastName', 'nationality', 'gender'];
    $basicFields   = array_merge($publicFields, ['nationality2', 'birthYear', 'club', 'city']);
    $premiumFields = array_merge($basicFields, ['birthDate', 'performances']);
    $adminFields   = array_merge($premiumFields, ['deathDate', 'privacy', 'comments', 'raceCount', 'totalMileage', 'linkedProfiles']);
    // Select allowed fields based on tier
    if ($userTier >= 3) {
        $allowed = $adminFields;
    } elseif ($userTier >= 2) {
        $allowed = $premiumFields;
    } elseif ($userTier >= 1) {
        $allowed = $basicFields;
    } else {
        $allowed = $publicFields;
    }
    // Filter out fields not allowed
    foreach (array_keys($response) as $key) {
        if (!in_array($key, $allowed, true)) {
            unset($response[$key]);
        }
    }

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
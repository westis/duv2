<?php
// races_list.php
require_once 'Database.php';
require 'helpers.php';

// Fetch a list of races with query parameters (main logic)
function getRacesList($conn) {
    // Initialize query parameters
    $search = $_GET['search'] ?? null;
    $raceType = $_GET['raceType'] ?? 'all';
    $raceSurface = $_GET['raceSurface'] ?? 'all';
    $dateFrom = $_GET['dateFrom'] ?? null;
    $dateTo = $_GET['dateTo'] ?? null;
    $countries = isset($_GET['countries']) ? explode(',', $_GET['countries']) : ['all'];
    $distanceMinKm = $_GET['distMinKm'] ?? 'all';
    $distanceMaxKm = $_GET['distMaxKm'] ?? 'all';
    $durationMin = $_GET['durMin'] ?? 'all';
    $durationMax = $_GET['durMax'] ?? 'all';
    $rankingEligible = $_GET['rankingEligible'] ?? 'all';
    $resultStatus = $_GET['resultStatus'] ?? 'all';
    $orderBy = $_GET['orderBy'] ?? 'date';
    $allowedOrderByValues = ['date', 'numberOfFinishers'];
    if (!in_array($orderBy, $allowedOrderByValues)) {
        $orderBy = 'date'; // Reset to default if invalid value
    }
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 25;
    if ($limit > 100) { $limit = 100; }
    $offset = $_GET['offset'] ?? 0;
    $sortOrder = $_GET['sortOrder'] ?? 'ASC';
    if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) {
        $sortOrder = 'ASC'; // Reset to default if invalid
    }
     $splits = $_GET['splits'] ?? 'no'; // 'all', 'no', or 'yes'

    // Build the query dynamically
    $whereConditions = [];
    $bindParams = [];
    $query = "
        SELECT *,
               (COALESCE(FinisherM, 0) + COALESCE(FinisherW, 0)) AS numberOfFinishers
        FROM tevent
        WHERE 1 = 1
    ";

    // Add the splits condition
    if ($splits === 'no') {
        $whereConditions[] = "PartOf =''";
    } elseif ($splits === 'yes') {
        $whereConditions[] = "PartOf !=''";
    }

    // Add filters to the query
    if ($raceType !== 'all') {
        if ($raceType === 'fixedTime') {
            $whereConditions[] = "(Duration != '' AND EventType NOT IN (4, 10, 11, 12, 13, 14))";
        } elseif ($raceType === 'fixedDistance') {
            $whereConditions[] = "Length != ''";
        } elseif ($raceType === 'backyardUltra') {
            $whereConditions[] = "EventType = 10";
        } elseif ($raceType === 'stageRace') {
            $whereConditions[] = "EventType = 4";
        } elseif ($raceType === 'raceWalking') {
            $whereConditions[] = "EventType IN (11, 12, 13, 14)";
        } elseif ($raceType === 'other') {
            $whereConditions[] = "(EventType NOT IN (4, 10, 11, 12, 13, 14) OR Duration IS NULL)";
        }
    }

    if ($raceSurface !== 'all') {
        if ($raceSurface === 'trail') {
            $whereConditions[] = "EventType = 2";
        } elseif ($raceSurface === 'road') {
            $whereConditions[] = "EventType IN (1, 3, 11, 12)";
        } elseif ($raceSurface === 'track') {
            $whereConditions[] = "EventType IN (5, 13)";
        } elseif ($raceSurface === 'indoor') {
            $whereConditions[] = "EventType IN (6, 14)";
        }
    }

    if ($search) {
        $whereConditions[] = "(EventName LIKE :search OR Description LIKE :search)";
        $bindParams[':search'] = '%' . $search . '%';
    }
    if ($dateFrom) {
        $whereConditions[] = "Startdate >= :dateFrom";
        $bindParams[':dateFrom'] = $dateFrom;
    }
    if ($dateTo) {
        $whereConditions[] = "Startdate <= :dateTo";
        $bindParams[':dateTo'] = $dateTo;
    }
    if ($distanceMinKm !== 'all') {
        $whereConditions[] = "numeric_distance_km >= :distanceMinKm";
        $bindParams[':distanceMinKm'] = (float)$distanceMinKm;
    }
    if ($distanceMaxKm !== 'all') {
        $whereConditions[] = "numeric_distance_km <= :distanceMaxKm";
        $bindParams[':distanceMaxKm'] = (float)$distanceMaxKm;
    }
    if ($durationMin !== 'all') {
        $whereConditions[] = "numeric_duration_hours >= :durationMin";
        $bindParams[':durationMin'] = (float)$durationMin;
    }
    if ($durationMax !== 'all') {
        $whereConditions[] = "numeric_duration_hours <= :durationMax";
        $bindParams[':durationMax'] = (float)$durationMax;
    }
    if ($rankingEligible !== 'all') {
        $whereConditions[] = "RecordProof = :rankingEligible";
        $bindParams[':rankingEligible'] = $rankingEligible;
    }
    if ($resultStatus !== 'all') {
        $whereConditions[] = "Results = :resultStatus";
        $bindParams[':resultStatus'] = $resultStatus;
    }
    if (!empty($countries) && $countries[0] !== 'all') {
        $countryConditions = [];
        foreach ($countries as $index => $country) {
            $param = ":country_" . $index;
            $countryConditions[] = $param;
            $bindParams[$param] = $country;
        }
        $whereConditions[] = "Country IN (" . implode(',', $countryConditions) . ")";
    }

    // Add all conditions to the query
    if (!empty($whereConditions)) {
        $query .= " AND " . implode(" AND ", $whereConditions);
    }

    // Order results
    $orderByField = $orderBy === 'date' ? 'Startdate' : 'numberOfFinishers';
    $query .= " ORDER BY {$orderByField} {$sortOrder} LIMIT :limit OFFSET :offset";

    // Prepare and execute the final query
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    foreach ($bindParams as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare the races list with desired format
        $races = [];
        foreach ($events as $event) {
            $races[] = [
                'eventId' => (int)$event['EventID'],
                'parentId' => (int)$event['ParentID'],
                'partOf' => (int)$event['PartOf'],
                'eventName' => $event['EventName'],
                'startDate' => $event['Startdate'],
                'endDate' => $event['Enddate'],
                'city' => $event['City'],
                'country' => $event['Country'],
                'raceType' => mapRaceType($event['EventType']),
                'raceSurface' => mapRaceSurface($event['EventType']),
                'rankingEligible' => (bool)$event['RecordProof'],
                'resultStatus' => $event['Results'],
                'edition' => $event['Edition'],
                'distance' => $event['Length'], // or apply conversions if needed
                'duration' => $event['Duration'], // or apply conversions if needed
                'IAULabel' => $event['IAULabel'],
                'year' => (int)date('Y', strtotime($event['Startdate'])),
                'month' => (int)date('m', strtotime($event['Startdate'])),
                'day' => (int)date('d', strtotime($event['Startdate']))
            ];
        }

        // Count all races that match the filtering criteria
        $totalCountQuery = "
            SELECT COUNT(*) 
            FROM tevent
            WHERE 1 = 1 AND " . implode(" AND ", $whereConditions);

        $totalCountStmt = $conn->prepare($totalCountQuery);

        // Bind parameters for the total count query
        foreach ($bindParams as $param => $value) {
            $totalCountStmt->bindValue($param, $value);
        }

        $totalCountStmt->execute();
        $totalCount = $totalCountStmt->fetchColumn();

        // Return the races list with the total count
        return [
            'totalCount' => (int)$totalCount,
            'races' => $races
        ];
    } else {
        return ['error' => 'Query execution failed.'];
    }
}


// Main handler for /api/races
$db = new Database();
$conn = $db->getConnection();
echo json_encode(getRacesList($conn));

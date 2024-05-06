<?php
// races.php
require 'Database.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

$endpoint = $_GET['endpoint'] ?? null;
$parentId = $_GET['parentId'] ?? null;
$eventId = $_GET['eventId'] ?? null;

// Establish database connection
$db = new Database();
$conn = $db->getConnection();

switch ($endpoint) {
    case 'filters':
        // Provide available filter options for races
        echo json_encode(getRaceFilters($conn));
        break;

    case 'editions':
        if ($parentId) {
            echo json_encode(getRaceEditions($conn, $parentId));
        } else {
            echo json_encode(['error' => 'Invalid parentId']);
        }
        break;

    case 'results':
        if ($eventId) {
            echo json_encode(getRaceResultsWithEditionDetails($conn, $eventId));
        } else {
            echo json_encode(['error' => 'Invalid eventId']);
        }
        break;

    case 'resultsFilters':
        if ($eventId) {
            echo json_encode(getResultsFilters($conn, $eventId));
        } else {
            echo json_encode(['error' => 'Invalid eventId']);
        }
        break;

    default:
        // Default case to get the list of races
        echo json_encode(getRacesList($conn));
        break;
}

function convertLengthToKm($length) {
    if (preg_match('/(\d+(?:\.\d+)?)\s*(km|mi)/i', $length, $matches)) {
        $value = floatval($matches[1]);
        $unit = strtolower($matches[2]);
        if ($unit == 'km') {
            return $value;
        } elseif ($unit == 'mi') {
            return $value * 1.60934;
        }
    }
    if (preg_match('/(\d+)\s*km\/(\d+)\s*Etappen/i', $length, $matches)) {
        return floatval($matches[1]);
    }
    return null;
}

function convertDurationToHours($duration) {
    if (preg_match('/(\d+(?:\.\d+)?)\s*(h|d)/i', $duration, $matches)) {
        $value = floatval($matches[1]);
        $unit = strtolower($matches[2]);
        if ($unit == 'h') {
            return $value;
        } elseif ($unit == 'd') {
            return $value * 24;
        }
    }
    return null;
}

// Format race distance using `convertLengthToKm`
function formatRaceDistance($length) {
    $km = convertLengthToKm($length);
    return $km !== null ? "{$km} km" : "";
}

// Format race duration using `convertDurationToHours`
function formatRaceDuration($duration) {
    $hours = convertDurationToHours($duration);
    return $hours !== null ? "{$hours} h" : "";
}

// Map race type based on EventType value
function mapRaceType($eventType) {
    if ($eventType == 4) return 'stageRace';
    if ($eventType == 10) return 'backyardUltra';
    if (in_array($eventType, [11, 12, 13, 14])) return 'raceWalking';
    return 'fixedTime'; // Default to fixedTime if not otherwise categorized
}

// Map race surface based on EventType value
function mapRaceSurface($eventType) {
    if ($eventType == 2) return 'trail';
    if (in_array($eventType, [1, 3, 11, 12])) return 'road';
    if (in_array($eventType, [5, 13])) return 'track';
    if (in_array($eventType, [6, 14])) return 'indoor';
    return 'unknown'; // Default if the surface type cannot be determined
}

// Fetch available filters for races
function getRaceFilters($conn) {
    // This is a placeholder; adjust to your database schema
    return [
        'raceType' => ['all', 'fixedTime', 'fixedDistance', 'backyardUltra', 'stageRace', 'raceWalking', 'other'],
        'raceSurface' => ['all', 'trail', 'road', 'track', 'indoor'],
        // Add more filter options here as needed
    ];
}

// Fetch editions of a specific race
function getRaceEditions($conn, $parentId) {
    $query = "SELECT * FROM editions WHERE parent_id = :parentId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch race results and edition details
function getRaceResultsWithEditionDetails($conn, $eventId) {
    // Fetch event details from `tevent`
    $queryEvent = "
        SELECT EventID, PartOf, Edition, EventName, Startdate, Enddate, Length, Duration, EventType,
               City, Country, URL, Email, AltitudeDiff, FinisherM, FinisherW,
               RecordProof, ResultSource, CreatedBy, ChangedOn, Results,
               ParentID
        FROM tevent
        WHERE EventID = :eventId
    ";
    $stmtEvent = $conn->prepare($queryEvent);
    $stmtEvent->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmtEvent->execute();
    $eventData = $stmtEvent->fetch(PDO::FETCH_ASSOC);

    if (!$eventData) {
        return ['error' => 'Event not found'];
    }

    // Map raceType and raceSurface based on EventType
    $eventData['raceType'] = mapRaceType($eventData['EventType']);
    $eventData['raceSurface'] = mapRaceSurface($eventData['EventType']);

    // Format raceDuration and raceDistance using conversion functions
    $eventData['raceDuration'] = formatRaceDuration($eventData['Duration']);
    $eventData['raceDistance'] = formatRaceDistance($eventData['Length']);

    // Combine male and female finisher counts into a single object
    $eventData['finisherCount'] = [
        'total' => $eventData['FinisherM'] + $eventData['FinisherW'],
        'male' => $eventData['FinisherM'],
        'female' => $eventData['FinisherW']
    ];

    // Prepare edition details based on field mappings
    $editionDetails = [
        'eventId' => (int) $eventData['EventID'],
        'parentId' => (int) $eventData['ParentID'],
        'partOf' => (int) $eventData['PartOf'],
        'eventName' => $eventData['EventName'],
        'edition' => $eventData['Edition'],
        'startDate' => $eventData['Startdate'],
        'endDate' => $eventData['Enddate'],
        'city' => $eventData['City'],
        'country' => $eventData['Country'],
        'raceURL' => $eventData['URL'],
        'email' => $eventData['Email'],
        'raceDistance' => $eventData['raceDistance'],
        'raceDuration' => $eventData['raceDuration'],
        'raceType' => $eventData['raceType'],
        'raceSurface' => $eventData['raceSurface'],
        'altitudeDiff' => $eventData['AltitudeDiff'],
        'finisherCount' => $eventData['finisherCount'],
        'recordEligible' => (bool) $eventData['RecordProof'],
        'resultSource' => $eventData['ResultSource'],
        'recordedBy' => $eventData['CreatedBy'] ?? 'unknown', // Use default value if not available
        'recordedAt' => $eventData['ChangedOn'] ?? 'unknown', // Use default value if not available    
        'resultCount' => (int) $eventData['Results']
    ];

    // Fetch race results from `tperformance` and join `tperson` to get full names
    $queryResults = "
        SELECT tp.PersonID as personId, tp.RankTotal as rankTotal, 
               tp.TimeGross as performance, tp.TimeNet as performanceNumeric,
               tp.Age_Y as ageGradedPerformance, tp.CatEvent as extra,
               tpe.LastName as lastname, tpe.FirstName as firstname,
               CONCAT(tpe.LastName, ', ', tpe.FirstName) as fullName,
               tpe.OrigName as origName, tpe.Nationality as nationality,
               tp.CatInt as ageGroup, tp.RankCatInt as rankAgeGroup, 
               tpe.Club as club, tpe.City as city, tpe.Nationality as country,
               tpe.YOB as yearOfBirth, tpe.Birthday as dateOfBirth
        FROM tperformance tp
        JOIN tperson tpe ON tp.PersonID = tpe.PersonID
        WHERE tp.EventID = :eventId
    ";
    $stmtResults = $conn->prepare($queryResults);
    $stmtResults->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmtResults->execute();
    $results = $stmtResults->fetchAll(PDO::FETCH_ASSOC);

    // Combine edition details and results into the final JSON format
    return [
        'editionDetails' => $editionDetails,
        'results' => $results
    ];
}


// Fetch filters available for results of a specific race event
function getResultsFilters($conn, $eventId) {
    // Implement logic to fetch event-specific filters
    return [
        'gender' => ['male', 'female'],
        'ageGroup' => ['junior', 'senior', 'veteran'],
        // Add more options here if needed
    ];
}

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

    // Build the query dynamically
    $whereConditions = [];
    $bindParams = [];
    $query = "
        SELECT *
        FROM (
            SELECT *,
                   (COALESCE(FinisherM, 0) + COALESCE(FinisherW, 0)) AS numberOfFinishers,
                   (CASE
                       WHEN LOWER(REGEXP_SUBSTR(Duration, '[a-z]+$')) = 'h' THEN CAST(REGEXP_SUBSTR(Duration, '^[0-9]+') AS DECIMAL(10,2))
                       WHEN LOWER(REGEXP_SUBSTR(Duration, '[a-z]+$')) = 'd' THEN CAST(REGEXP_SUBSTR(Duration, '^[0-9]+') AS DECIMAL(10,2)) * 24
                       ELSE NULL
                   END) AS numeric_duration_hours,
                   (CASE
                       WHEN LOWER(REGEXP_SUBSTR(Length, '[a-z]+$')) = 'km' THEN CAST(REGEXP_SUBSTR(Length, '^[0-9]+') AS DECIMAL(10,2))
                       WHEN LOWER(REGEXP_SUBSTR(Length, '[a-z]+$')) = 'mi' THEN CAST(REGEXP_SUBSTR(Length, '^[0-9]+') AS DECIMAL(10,2)) * 1.60934
                       ELSE NULL
                   END) AS numeric_distance_km
            FROM tevent
        ) AS subquery
        WHERE 1 = 1";

    // Add filters to the query
    if ($raceType !== 'all') {
        if ($raceType === 'fixedTime') {
            $whereConditions[] = "(subquery.Duration != '' AND subquery.EventType NOT IN (4, 10, 11, 12, 13, 14))";
        } elseif ($raceType === 'fixedDistance') {
            $whereConditions[] = "subquery.Length != ''";
        } elseif ($raceType === 'backyardUltra') {
            $whereConditions[] = "subquery.EventType = 10";
        } elseif ($raceType === 'stageRace') {
            $whereConditions[] = "subquery.EventType = 4";
        } elseif ($raceType === 'raceWalking') {
            $whereConditions[] = "subquery.EventType IN (11, 12, 13, 14)";
        } elseif ($raceType === 'other') {
            $whereConditions[] = "(subquery.EventType NOT IN (4, 10, 11, 12, 13, 14) OR subquery.Duration IS NULL)";
        }
    }

    if ($raceSurface !== 'all') {
        if ($raceSurface === 'trail') {
            $whereConditions[] = "subquery.EventType = 2";
        } elseif ($raceSurface === 'road') {
            $whereConditions[] = "subquery.EventType IN (1, 3, 11, 12)";
        } elseif ($raceSurface === 'track') {
            $whereConditions[] = "subquery.EventType IN (5, 13)";
        } elseif ($raceSurface === 'indoor') {
            $whereConditions[] = "subquery.EventType IN (6, 14)";
        }
    }

    if ($search) {
        $whereConditions[] = "(subquery.EventName LIKE :search OR subquery.Description LIKE :search)";
        $bindParams[':search'] = '%' . $search . '%';
    }
    if ($dateFrom) {
        $whereConditions[] = "subquery.Startdate >= :dateFrom";
        $bindParams[':dateFrom'] = $dateFrom;
    }
    if ($dateTo) {
        $whereConditions[] = "subquery.Startdate <= :dateTo";
        $bindParams[':dateTo'] = $dateTo;
    }
    if ($distanceMinKm !== 'all') {
        $whereConditions[] = "subquery.numeric_distance_km >= :distanceMinKm";
        $bindParams[':distanceMinKm'] = (float)$distanceMinKm;
    }
    if ($distanceMaxKm !== 'all') {
        $whereConditions[] = "subquery.numeric_distance_km <= :distanceMaxKm";
        $bindParams[':distanceMaxKm'] = (float)$distanceMaxKm;
    }
    if ($durationMin !== 'all') {
        $whereConditions[] = "subquery.numeric_duration_hours >= :durationMin";
        $bindParams[':durationMin'] = (float)$durationMin;
    }
    if ($durationMax !== 'all') {
        $whereConditions[] = "subquery.numeric_duration_hours <= :durationMax";
        $bindParams[':durationMax'] = (float)$durationMax;
    }
    if ($rankingEligible !== 'all') {
        $whereConditions[] = "subquery.RecordProof = :rankingEligible";
        $bindParams[':rankingEligible'] = $rankingEligible;
    }
    if ($resultStatus !== 'all') {
        $whereConditions[] = "subquery.Results = :resultStatus";
        $bindParams[':resultStatus'] = $resultStatus;
    }
    if (!empty($countries) && $countries[0] !== 'all') {
        $countryConditions = [];
        foreach ($countries as $index => $country) {
            $param = ":country_" . $index;
            $countryConditions[] = $param;
            $bindParams[$param] = $country;
        }
        $whereConditions[] = "subquery.Country IN (" . implode(',', $countryConditions) . ")";
    }

    // Add all conditions to the query
    if (!empty($whereConditions)) {
        $query .= " AND " . implode(" AND ", $whereConditions);
    }

    // Order results
    if ($orderBy === 'date') {
        $query .= " ORDER BY subquery.Startdate " . $sortOrder . " LIMIT :limit OFFSET :offset";
    } else {
        $query .= " ORDER BY subquery.numberOfFinishers " . $sortOrder . " LIMIT :limit OFFSET :offset";
    }

    // Prepare and execute the final query
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    foreach ($bindParams as $param => $value) {
        $stmt->bindValue($param, $value);
    }

    if ($stmt->execute()) {
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['Races' => $events]; // Wrap the results in a 'races' element
    } else {
        return ['error' => 'Query execution failed.'];
    }
}

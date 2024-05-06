<?php
// race_results.php
require_once 'Database.php';
require 'helpers.php';

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


function getResultsFilters($conn, $eventId) {
    // Implement logic to fetch event-specific filters
    return [
        'gender' => ['male', 'female'],
        'ageGroup' => ['junior', 'senior', 'veteran']
    ];
}

// Main handler for /api/races/{eventId}/results or results/filters
$eventId = $_GET['eventId'] ?? null;
$endpoint = $_GET['endpoint'] ?? null;
$db = new Database();
$conn = $db->getConnection();
if ($eventId) {
    if ($endpoint === 'resultsFilters') {
        echo json_encode(getResultsFilters($conn, $eventId));
    } else {
        echo json_encode(getRaceResultsWithEditionDetails($conn, $eventId));
    }
} else {
    echo json_encode(['error' => 'Invalid eventId']);
}

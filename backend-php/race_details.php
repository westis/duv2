<?php
// race_details.php
require_once 'Database.php';
require_once 'helpers.php';

function getRaceDetails($conn, $eventId) {
    $query = "
        SELECT EventID, ParentID, PartOf, EventName, Edition, Startdate, Enddate, City, Country,
               URL as raceURL, Email, Length as raceDistance, Duration as raceDuration, EventType,
               AltitudeDiff, FinisherM, FinisherW, RecordProof, ResultSource, CreatedBy, ChangedOn, Results
        FROM tevent
        WHERE EventID = :eventId
    ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    $edition = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$edition) {
        return ['error' => 'Event not found'];
    }

    // Format the details
    return [
        'eventId' => (int)$edition['EventID'],
        'parentId' => (int)$edition['ParentID'],
        'partOf' => (int)$edition['PartOf'],
        'eventName' => $edition['EventName'],
        'edition' => $edition['Edition'],
        'startDate' => $edition['Startdate'],
        'endDate' => $edition['Enddate'],
        'city' => $edition['City'],
        'country' => $edition['Country'],
        'raceURL' => $edition['raceURL'],
        'email' => $edition['Email'],
        'raceDistance' => formatRaceDistance($edition['raceDistance']),
        'raceDuration' => formatRaceDuration($edition['raceDuration']),
        'raceType' => mapRaceType($edition['EventType']),
        'raceSurface' => mapRaceSurface($edition['EventType']),
        'altitudeDiff' => $edition['AltitudeDiff'],
        'finisherCount' => [
            'total' => (int)($edition['FinisherM'] + $edition['FinisherW']),
            'male' => (int)$edition['FinisherM'],
            'female' => (int)$edition['FinisherW']
        ],
        'recordEligible' => (bool)$edition['RecordProof'],
        'resultSource' => $edition['ResultSource'],
        'recordedBy' => $edition['CreatedBy'] ?? 'unknown',
        'recordedAt' => $edition['ChangedOn'] ?? 'unknown',
        'resultCount' => (int)$edition['Results']
    ];
}

// Main handler for /api/races/{eventId}
$eventId = $_GET['eventId'] ?? null;
$db = new Database();
$conn = $db->getConnection();
if ($eventId) {
    $response = getRaceDetails($conn, $eventId);
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Invalid eventId']);
}

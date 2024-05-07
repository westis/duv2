<?php
// race_editions.php
require_once 'Database.php';
require_once 'helpers.php';

function getRaceEditions($conn, $parentId) {
    // 1. Find the grandparent race ID if a child's EventID is provided
    $grandparentQuery = "SELECT ParentID FROM tevent WHERE EventID = :eventId";
    $stmt = $conn->prepare($grandparentQuery);
    $stmt->bindParam(':eventId', $parentId, PDO::PARAM_INT);
    $stmt->execute();
    $grandparentResult = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($grandparentResult) {
        $grandparentId = $grandparentResult['ParentID'];
    } else {
        $grandparentId = $parentId; // Use the original ID if it's already the grandparent
    }

    // 2. Query for editions based on the grandparent ID
    $query = "
        SELECT EventID, ParentID, PartOf, EventName, Edition, Startdate, Enddate, City, Country,
               URL as raceURL, Email, Length as raceDistance, Duration as raceDuration, EventType,
               AltitudeDiff, FinisherM, FinisherW, RecordProof, ResultSource, CreatedBy, ChangedOn, Results
        FROM tevent
        WHERE ParentID = :grandparentId 
     ";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':grandparentId', $grandparentId, PDO::PARAM_INT); // Use grandparentId here
    $stmt->execute();
    $editions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the output as per the provided JSON structure
    $output = [];
    foreach ($editions as $edition) {
        $output[] = [
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

    return $output;
}

// Main handler for /api/races/{parentId}/editions
$parentId = $_GET['parentId'] ?? null;
$db = new Database();
$conn = $db->getConnection();
if ($parentId) {
    echo json_encode(getRaceEditions($conn, $parentId));
} else {
    echo json_encode(['error' => 'Invalid parentId']);
}

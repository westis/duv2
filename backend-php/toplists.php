<?php
require_once 'Database.php'; // adjust the path as necessary

// Get parameters from the URL query string
$discipline = $_GET['discipline'] ?? null;
$year = $_GET['year'] ?? 'alltime';
$scope = $_GET['scope'] ?? null;
$gender = $_GET['gender'] ?? null;
$lang = $_GET['lang'] ?? null;
$country = $_GET['country'] ?? null;
$limit = $_GET['limit'] ?? 25;
$offset = $_GET['offset'] ?? 0;

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    $whereClauses = [];
    $params = [];
    $orderBy = '';
    $rankColumn = '';
    $ageGroupRankColumn = '';

    // Set up the Extra field logic with an empty string as the base
    $extraColumn = "CONCAT(";
    $extraParts = [];
    if ($discipline !== null) { // Extra condition needed to avoid PHP notice
        $extraParts[] = "CASE WHEN tevent.PartOf > 0 THEN 'S' ELSE '' END";
        $extraParts[] = "CASE WHEN tevent.EventType IN (5, 13) THEN 'T' ELSE '' END";
        $extraParts[] = "CASE WHEN tevent.EventType IN (6, 14) THEN 'I' ELSE '' END";
    }
    $extraColumn .= implode(", ", $extraParts);
    $extraColumn .= ") AS Extra";

    // Determine the performance column and sorting order based on discipline
    if (in_array($discipline, ['50km', '50mi', '100km', '100mi', '1000km', '1000mi'])) {
        $whereClauses[] = 'tevent.Length = :discipline';
        $params[':discipline'] = $discipline;
        $performanceColumn = 'tperformance.TimeGross AS Performance';
        $orderBy = 'Performance ASC';
        $rankColumn = 'RANK() OVER (ORDER BY tperformance.TimeGross) AS RankTotal';
        $ageGroupRankColumn = 'RANK() OVER (PARTITION BY tperformance.CatInt ORDER BY tperformance.TimeGross) AS RankAgeGroup';
    } elseif (in_array($discipline, ['6h', '12h', '24h', '48h', '72h', '6d', '10d'])) {
        $whereClauses[] = 'tevent.Duration = :discipline';
        $params[':discipline'] = $discipline;
        $performanceColumn = 'tperformance.Distance AS Performance';
        $orderBy = 'Performance DESC';
        $rankColumn = 'RANK() OVER (ORDER BY tperformance.Distance DESC) AS RankTotal';
        $ageGroupRankColumn = 'RANK() OVER (PARTITION BY tperformance.CatInt ORDER BY tperformance.Distance DESC) AS RankAgeGroup';
    }

    // Additional conditions based on other parameters
    if ($gender !== null) {
        $whereClauses[] = 'tperson.Gender = :gender';
        $params[':gender'] = $gender;
    }

    if ($country !== null) {
        $whereClauses[] = 'tperson.Nationality = :country';
        $params[':country'] = $country;
    }

    if ($year !== 'alltime') {
        $whereClauses[] = 'YEAR(tevent.StartDate) = :year';
        $params[':year'] = $year;
    }

    $whereClauses[] = "tevent.RecordProof = 'y'";
    $whereClauses[] = "tperformance.Exclude = 0";

    // Construct the full SQL query with ranking
    $sql = "
    SELECT $rankColumn, $extraColumn, tperson.PersonID, tperson.FirstName, tperson.LastName, tperson.Nationality, tperson.Birthday AS DateOfBirth, $performanceColumn, tperformance.CatInt AS AgeGroup, $ageGroupRankColumn, tevent.StartDate, tevent.City, tevent.Country, tevent.EventID, tevent.EventName
    FROM tperformance
    JOIN tperson ON tperformance.PersonID = tperson.PersonID
    JOIN tevent ON tperformance.EventID = tevent.EventID
    WHERE " . implode(' AND ', $whereClauses) . "
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset
    ";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    // Explicitly cast limit and offset as integers
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output results as JSON
    header('Content-Type: application/json');
    echo json_encode($results);
} else {
    echo json_encode(['error' => 'Database connection failed']);
}
?>

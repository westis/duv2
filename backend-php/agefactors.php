<?php

require_once 'Database.php';

function loadAgeFactors($filepath) {
    $factors = [];
    if (($handle = fopen($filepath, "r")) !== FALSE) {
        fgetcsv($handle); // Skip the header
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $factors[(int)$data[0]] = ['FactorWomen' => (float)$data[1], 'FactorMen' => (float)$data[2]];
        }
        fclose($handle);
    }
    return $factors;
}

function timeToSeconds($time) {
    sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
    return $hours * 3600 + $minutes * 60 + $seconds;
}

function secondsToTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}

function calculateAgeGradedTimes($performances, $ageFactors) {
    $results = array_map(function ($performance) use ($ageFactors) {
        $age = (int) round($performance['Age_Y']);
        $genderKey = $performance['Gender'] === 'W' ? 'FactorWomen' : 'FactorMen';  // Correct key based on gender
        $factor = $ageFactors[$age][$genderKey] ?? 1;

        $timeInSeconds = timeToSeconds($performance['TimeGross']);
        $ageGradedTimeInSeconds = $timeInSeconds * $factor;

        return array_merge($performance, [
            'AgeGradedTime' => secondsToTime($ageGradedTimeInSeconds),
            'AppliedFactor' => $factor
        ]);
    }, $performances);

    usort($results, function ($a, $b) {
        return timeToSeconds($a['AgeGradedTime']) <=> timeToSeconds($b['AgeGradedTime']);
    });
    
    return $results;
}

$db = new Database();
$conn = $db->getConnection();

$query = "
    SELECT 
        p.TimeGross, 
        p.Age_Y, 
        per.Gender, 
        per.FirstName, 
        per.LastName,
        e.EventName, 
        e.StartDate
    FROM tperformance p
    JOIN tevent e ON p.EventID = e.EventID
    JOIN tperson per ON p.PersonID = per.PersonID
    WHERE e.RecordProof = 'Y' AND e.Length = 100 AND per.Gender = 'M'
";
$stmt = $conn->prepare($query);
$stmt->execute();
$performances = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ageFactors = loadAgeFactors("C:/Users/westi/Documents/server/duv-nuxt/backend-php/AgeFactors.csv");
$ageGradedTimes = calculateAgeGradedTimes($performances, $ageFactors);
$topAgeGradedTimes = array_slice($ageGradedTimes, 0, 30); // Limit to the top 30 age-graded times

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>First Name</th><th>Last Name</th><th>Event Name</th><th>Start Date</th><th>Original Time</th><th>Age at Event</th><th>Gender</th><th>Age Graded Time</th><th>Applied Factor</th></tr>";
foreach ($topAgeGradedTimes as $time) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($time['FirstName']) . "</td>";
    echo "<td>" . htmlspecialchars($time['LastName']) . "</td>";
    echo "<td>" . htmlspecialchars($time['EventName']) . "</td>";
    echo "<td>" . htmlspecialchars($time['StartDate']) . "</td>";
    echo "<td>" . htmlspecialchars($time['TimeGross']) . "</td>";
    echo "<td>" . round($time['Age_Y'], 2) . "</td>";
    echo "<td>" . htmlspecialchars($time['Gender']) . "</td>";
    echo "<td>" . htmlspecialchars($time['AgeGradedTime']) . "</td>";
    echo "<td>" . htmlspecialchars($time['AppliedFactor']) . "</td>";
    echo "</tr>";
}
echo "</table>";

?>




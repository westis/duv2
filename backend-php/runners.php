<?php
// runners.php
require_once 'Database.php';

$db = new Database();
$conn = $db->getConnection();

$query_param = $_GET['search'] ?? null;

$query = "SELECT * FROM tperson";
if ($query_param) {
    $query .= " WHERE Name LIKE :search";
}

$stmt = $conn->prepare($query);
if ($query_param) {
    $search_value = "%$query_param%";
    $stmt->bindParam(':search', $search_value);
}

$stmt->execute();
$runners = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($runners);

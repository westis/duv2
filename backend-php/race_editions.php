<?php
// race_editions.php
require_once 'Database.php';
require 'helpers.php';

function getRaceEditions($conn, $parentId) {
    $query = "SELECT * FROM editions WHERE parent_id = :parentId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

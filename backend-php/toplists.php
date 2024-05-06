<?php
// toplists.php
require_once 'Database.php';

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT * FROM tstatlong";
$stmt = $conn->prepare($query);
$stmt->execute();

$top_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($top_lists);

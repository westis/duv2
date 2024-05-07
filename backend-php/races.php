<?php
// races.php
require_once 'Database.php';

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

$endpoint = $_GET['endpoint'] ?? null;
$parentId = $_GET['parentId'] ?? null;
$eventId = $_GET['eventId'] ?? null;

switch ($endpoint) {
    case 'filters':
        require 'race_filters.php';
        break;

    case 'editions':
        if ($parentId) {
            require 'race_editions.php';
        } else {
            echo json_encode(['error' => 'Invalid parentId']);
        }
        break;

    case 'results':
    case 'resultsFilters':
        if ($eventId) {
            require 'race_results.php';
        } else {
            echo json_encode(['error' => 'Invalid eventId']);
        }
        break;

    default:
        if ($eventId) {
            require 'race_details.php';
        } else {
            require 'races_list.php';
        }
        break;
}

<?php
// race_filters.php
require_once 'Database.php';
require 'helpers.php';

function getRaceFilters($conn) {
    // This is a placeholder; adjust to your database schema
    return [
        'raceType' => ['all', 'fixedTime', 'fixedDistance', 'backyardUltra', 'stageRace', 'raceWalking', 'other'],
        'raceSurface' => ['all', 'trail', 'road', 'track', 'indoor']
    ];
}

// Main handler for /api/races/filters
$db = new Database();
$conn = $db->getConnection();
echo json_encode(getRaceFilters($conn));

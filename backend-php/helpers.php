<?php

// helpers.php
function convertLengthToKm($length) {
    if (preg_match('/(\d+(?:\.\d+)?)\s*(km|mi)/i', $length, $matches)) {
        $value = floatval($matches[1]);
        $unit = strtolower($matches[2]);
        return $unit === 'km' ? $value : $value * 1.60934;
    }
    if (preg_match('/(\d+)\s*km\/(\d+)\s*Etappen/i', $length, $matches)) {
        return floatval($matches[1]);
    }
    return null;
}

function convertDurationToHours($duration) {
    if (preg_match('/(\d+(?:\.\d+)?)\s*(h|d)/i', $duration, $matches)) {
        $value = floatval($matches[1]);
        return strtolower($matches[2]) === 'h' ? $value : $value * 24;
    }
    return null;
}

function formatRaceDistance($length) {
    $km = convertLengthToKm($length);
    return $km !== null ? "{$km} km" : "";
}

function formatRaceDuration($duration) {
    $hours = convertDurationToHours($duration);
    return $hours !== null ? "{$hours} h" : "";
}

function mapRaceType($eventType) {
    if ($eventType == 4) return 'stageRace';
    if ($eventType == 10) return 'backyardUltra';
    if (in_array($eventType, [11, 12, 13, 14])) return 'raceWalking';
    return 'fixedTime';
}

function mapRaceSurface($eventType) {
    if ($eventType == 2) return 'trail';
    if (in_array($eventType, [1, 3, 11, 12])) return 'road';
    if (in_array($eventType, [5, 13])) return 'track';
    if (in_array($eventType, [6, 14])) return 'indoor';
    return 'unknown';
}

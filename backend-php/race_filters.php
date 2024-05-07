<?php
require_once 'Database.php';
require_once 'translations.php';

function getAvailableFilters($conn, $lang = 'en') {
    $countryNameField = getLanguageField($lang);  // Assuming you have a getLanguageField function in helpers.php

    // Translated race types
    $translatedRaceTypes = array_map(function($type) use ($lang) {
        return [
            'value' => $type,
            'name' => translate('raceTypes', $type, $lang)
        ];
    }, array_keys(getTranslations($lang)['raceTypes']));

    // Translated race surfaces
    $translatedRaceSurfaces = array_map(function($surface) use ($lang) {
        return [
            'value' => $surface,
            'name' => translate('raceSurfaces', $surface, $lang)
        ];
    }, array_keys(getTranslations($lang)['raceSurfaces']));

    // Translated disciplines
    $translatedDisciplines = array_map(function($discipline) use ($lang) {
        return [
            'value' => $discipline,
            'name' => translate('disciplines', $discipline, $lang)
        ];
    }, array_keys(getTranslations($lang)['disciplines']));

    // Query for active countries
    $countriesQuery = "
        SELECT DISTINCT tevent.Country AS code, tcountry.$countryNameField AS name
        FROM tevent
        JOIN tcountry ON tevent.Country = tcountry.Code
        WHERE tcountry.Active = 'Y'
    ";
    $countriesStmt = $conn->query($countriesQuery);
    $countries = $countriesStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'countries' => $countries,
        'raceTypes' => $translatedRaceTypes,
        'raceSurfaces' => $translatedRaceSurfaces,
        'disciplines' => $translatedDisciplines
    ];
}

$db = new Database();
$conn = $db->getConnection();
$lang = $_GET['lang'] ?? 'en';  // Default to English if no lang parameter is provided
$filters = getAvailableFilters($conn, $lang);
header('Content-Type: application/json');
echo json_encode($filters);

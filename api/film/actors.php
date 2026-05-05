<?php
// GET /api/film/actors?film=inception
$filmName = isset($_GET['film']) ? $_GET['film'] : null;

if (!$filmName) {
    http_response_code(400);
    echo json_encode(["error" => "Parametro 'film' obbligatorio"]);
    exit;
}

// STEP 1: cerca il film per nome e recupera l'ID
$searchUrl = "https://api.imdbapi.dev/search/titles?query=" . urlencode($filmName);

$ch = curl_init($searchUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    http_response_code(502);
    echo json_encode(["error" => "Impossibile contattare IMDB API"]);
    exit;
}

$searchData = json_decode($response, true);
$results = $searchData['titles'] ?? [];

if (empty($results)) {
    http_response_code(404);
    echo json_encode(["error" => "Nessun film trovato con questo nome"]);
    exit;
}

$filmId = $results[0]['id'];
$filmTitle = $results[0]['primaryTitle'];

// STEP 2: recupera i dettagli del film con il cast
$detailUrl = "https://api.imdbapi.dev/titles/" . urlencode($filmId);

$ch = curl_init($detailUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    http_response_code(502);
    echo json_encode(["error" => "Impossibile recuperare i dettagli del film"]);
    exit;
}

$detailData = json_decode($response, true);

$actors = array_map(function ($star) {
    $personUrl = "https://api.imdbapi.dev/names/" . urlencode($star['id']);
    $ch = curl_init($personUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $personResponse = curl_exec($ch);
    curl_close($ch);
    
    $personData = json_decode($personResponse, true);
    
    $bday = $personData['birthDate'] ?? null;
    $bdayFormatted = (isset($bday['year']) && isset($bday['month']) && isset($bday['day'])) 
        ? sprintf("%04d-%02d-%02d", $bday['year'], $bday['month'], $bday['day']) 
        : null;
    
    return [
        "name" => $personData['displayName'] ?? ($star['displayName'] ?? null),
        "birthday" => $bdayFormatted,
        "image" => $personData['primaryImage']['url'] ?? null,
        "birthLocation" => $personData['birthLocation'] ?? null,
    ];
}, $detailData['stars'] ?? []);

echo json_encode([
    "film" => $filmTitle,
    "actors" => $actors
]);

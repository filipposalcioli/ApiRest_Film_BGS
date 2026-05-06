<?php
// GET /api/film/actors?film=Inception
$filmName = isset($_GET['film']) ? $_GET['film'] : null;

if (!$filmName) {
    http_response_code(400);
    echo json_encode(["error" => "Parametro 'film' obbligatorio"]);
    exit;
}

// Cerca il film per nome e recupera l'ID
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

$data = json_decode($response, true);
$results = $data['titles'] ?? [];

// Cerchiamo il primo record che sia un "movie"
$selectedFilm = null;
foreach ($results as $film) {
    if (isset($film['type']) && $film['type'] === 'movie') {
        $selectedFilm = $film;
        break; // Trovato il primo film, usciamo dal ciclo
    }
}

// Se non abbiamo trovato nessun film
if (!$selectedFilm) {
    http_response_code(404);
    echo json_encode(["error" => "Nessun film trovato con questo nome"]);
    exit;
}

$filmId = $selectedFilm['id'];
$filmTitle = $selectedFilm['primaryTitle'];

// Chiamiamo l'API per i dettagli del film per ottenere la lista degli ID attori (stars)
$detailsUrl = "https://api.imdbapi.dev/titles/" . urlencode($filmId);
$ch = curl_init($detailsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$detailsResponse = curl_exec($ch);
curl_close($ch);

$detailsData = json_decode($detailsResponse, true);
$stars = $detailsData['stars'] ?? [];

$actors = [];

// 3. Per ogni attore, recuperiamo i dettagli anagrafici 
foreach ($stars as $star) {
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

    $actors[] = [
        "name" => $personData['displayName'] ?? ($star['displayName'] ?? null),
        "birthday" => $bdayFormatted,
        "image" => $personData['primaryImage']['url'] ?? ($star['primaryImage']['url'] ?? null),
        "birthLocation" => $personData['birthLocation'] ?? null,
    ];
}

echo json_encode([
    "film" => $filmTitle,
    "actors" => $actors
]);

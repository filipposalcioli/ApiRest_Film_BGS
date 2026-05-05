<?php
// GET /api/film/titles?film=titolo
$filmName = isset($_GET['film']) ? urlencode($_GET['film']) : null;

if (!$filmName) {
    http_response_code(400);
    echo json_encode(["error" => "Parametro 'film' obbligatorio"]);
    exit;
}
$url = "https://api.imdbapi.dev/search/titles?query=" . $query;

$ch = curl_init($url);
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

$titles = array_map(function ($film) {
    return [
        "id" => $film['id'] ?? null,
        "title" => $film['primaryTitle'] ?? null,
        "year" => $film['startYear'] ?? null,
        "type" => $film['type'] ?? null,
        "image" => $film['primaryImage']['url'] ?? null,
        "rating" => $film['rating']['aggregateRating'] ?? null,
    ];
}, $data['titles'] ?? []);

echo json_encode(["film" => $titles]);

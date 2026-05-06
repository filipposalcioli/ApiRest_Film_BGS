<?php
// GET /api/film/titles?film=Micheal
$filmName = isset($_GET['film']) ? urlencode($_GET['film']) : null;

if (!$filmName) {
    http_response_code(400);
    echo json_encode(["error" => "Parametro 'film' obbligatorio"]);
    exit;
}
$url = "https://api.imdbapi.dev/search/titles?query=" . $filmName;

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

$titles = [];

foreach ($data['titles'] as $film) {
    // Filtriamo: includiamo solo se il tipo è 'movie'
    if (isset($film['type']) && $film['type'] === 'movie') {
        $titles[] = [
            "id" => $film['id'] ?? null,
            "title" => $film['primaryTitle'] ?? null,
            "year" => $film['startYear'] ?? null,
            "image" => $film['primaryImage']['url'] ?? null,
            "rating" => $film['rating']['aggregateRating'] ?? null,
        ];
    }
}

// Se dopo il filtro non abbiamo trovato nessun "movie", diamo errore
if (empty($titles)) {
    http_response_code(404);
    echo json_encode(["error" => "Nessun film trovato con questo nome"]);
    exit;
}

echo json_encode(["film" => $titles]);

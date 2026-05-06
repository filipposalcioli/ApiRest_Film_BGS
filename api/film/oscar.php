<?php

require __DIR__ . '/../../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$conn = getDb();


if ($method === 'GET') {
    // GET /api/film/oscar?film=Titanic
    $filmName = isset($_GET['film']) ? $_GET['film'] : null;

    if ($filmName) {
        $sql = "SELECT * FROM oscar WHERE titolo LIKE ? ORDER BY anno DESC";
        $stmt = mysqli_prepare($conn, $sql);
        $searchTerm = "%$filmName%";
        mysqli_stmt_bind_param($stmt, "s", $searchTerm);
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Parametro 'film' obbligatorio"]);
        mysqli_close($conn);
        exit;
    }

    if (!mysqli_stmt_execute($stmt)) {
        http_response_code(500);
        echo json_encode(["error" => "Errore nell'esecuzione della query"]);
        mysqli_close($conn);
        exit;
    }

    $result = mysqli_stmt_get_result($stmt);
    $records = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode(["oscar" => $records]);

} elseif ($method === 'POST') {
    // POST /api/film/oscar
    // Body: { "title": "...", "year": 2024, "category": "...", "winner": "..." }
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "Dati JSON non validi"]);
        mysqli_close($conn);
        exit;
    }

    $title = $data['title'] ?? null;
    $year = $data['year'] ?? null;
    $category = $data['category'] ?? null;
    $winner = $data['winner'] ?? null;

    if (!$title || !$year || !$category || !$winner) {
        http_response_code(400);
        echo json_encode(["error" => "Campi obbligatori mancanti: title, year, category, winner"]);
        mysqli_close($conn);
        exit;
    }

    $sql = "INSERT INTO oscar (title, year, category, winner) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "siss", $title, $year, $category, $winner);

    if (mysqli_stmt_execute($stmt)) {
        http_response_code(201);
        echo json_encode(["message" => "Record creato con successo"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Errore nell'inserimento del record: " . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    http_response_code(405);
    echo json_encode(["error" => "Metodo non consentito"]);
    mysqli_close($conn);
}


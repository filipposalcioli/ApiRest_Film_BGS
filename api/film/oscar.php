<?php
// GET /api/film/oscar
// GET /api/film/oscar?year=2023
require __DIR__ . '/../../config/db.php';

$year = isset($_GET['year']) ? (int)$_GET['year'] : null;

$conn = getDb();

if ($year) {
    $sql  = "SELECT * FROM oscar WHERE anno = ? ORDER BY anno DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $year);
} else {
    $sql  = "SELECT * FROM oscar ORDER BY anno DESC";
    $stmt = mysqli_prepare($conn, $sql);
}

if (!mysqli_stmt_execute($stmt)) {
    http_response_code(500);
    echo json_encode(["error" => "Errore nell'esecuzione della query"]);
    mysqli_close($conn);
    exit;
}

$result  = mysqli_stmt_get_result($stmt);
$records = [];

while ($row = mysqli_fetch_assoc($result)) {
    $records[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode(["oscar" => $records]);

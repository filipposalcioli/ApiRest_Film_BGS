<?php
function getDb()
{
    $host = 'localhost';
    $db = 'oscar_db';
    $user = 'root';
    $pass = '';

    $conn = mysqli_connect($host, $user, $pass, $db);

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Connessione al database fallita: " . mysqli_connect_error()]);
        exit;
    }

    mysqli_set_charset($conn, 'utf8');

    return $conn;
}

<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

$route = isset($_GET['url']) ? $_GET['url'] : '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET' && $method !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Metodo non consentito"]);
    exit;
}

switch ($route) {
    case 'api/film/titles':
        require __DIR__ . '/api/film/titles.php';
        break;
    case 'api/film/actors':
        require __DIR__ . '/api/film/actors.php';
        break;
    case 'api/film/oscar':
        require __DIR__ . '/api/film/oscar.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint non trovato"]);
        break;
}

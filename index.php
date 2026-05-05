<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$route = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
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
        echo json_encode(["error" => "Endpoint not found"]);
        break;
}

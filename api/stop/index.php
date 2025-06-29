<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/StopModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $stops = getAllStops();
            echo json_encode(['status' => 'success', 'data' => $stops]);
        } else {
            $stop = getStopById($id);
            if ($stop) {
                echo json_encode(['status' => 'success', 'data' => $stop]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Stop not found']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
} 
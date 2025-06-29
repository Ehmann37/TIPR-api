<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$stop_id = $_GET['stop_id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $routes = getAllRouteStops();
            echo json_encode(['status' => 'success', 'data' => $routes]);
        } else {
            $route = getRouteStopsByRouteId($id);
            if ($route) {
                echo json_encode(['status' => 'success', 'data' => $route]);
            } else {
                echo json_encode(['status' => 'success', 'data' => []]);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
} 
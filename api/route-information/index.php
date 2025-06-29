<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteInfoModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $routes = getAllRoutesInfo();
            echo json_encode(['status' => 'success', 'data' => $routes]);
        } else {
            $route = getRouteInfoById($id);
            if ($route) {
                echo json_encode(['status' => 'success', 'data' => $route]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Route information not found']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
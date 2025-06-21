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

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['route_name']) || !isset($data['schedule_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $routeId = addRouteInfo($data);
            echo json_encode(['status' => 'success', 'route_id' => $routeId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing route ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['route_name']) || !isset($data['schedule_id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $updated = updateRouteInfo($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Route information updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Route information not found or no changes made']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing route ID']);
            exit;
        }

        try {
            $deleted = deleteRouteInfo($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Route information deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Route information not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
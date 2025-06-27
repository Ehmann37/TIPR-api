<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/BusModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;
$status = !empty($_GET['status']) ? $_GET['status'] : null;
$route_id = !empty($_GET['routeId']) ? $_GET['routeId'] : null;
$route_status = !empty($_GET['routeStatus']) ? $_GET['routeStatus'] : null;

if ($id !== null){
    checkBusExists($id);
}

switch ($method) {
    case 'GET':
        if ($id === null) {
            $filters = [];

            if ($route_id !== null) {
                $filters['route_id'] = $route_id;
            }

            if ($route_status !== null) {
                $filters['route_status'] = $route_status;
            }

            if ($status !== null) {
                $filters['status'] = $status;
            }

            $buses = getBuses($filters);
            echo json_encode(['status' => 'success', 'data' => $buses]);
        } else {
            $bus = getBusById($id);
            if ($bus) {
                echo json_encode(['status' => 'success', 'data' => $bus]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Bus not found']);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['route_id']) || !isset($data['company_id']) || 
            !isset($data['bus_driver_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $busId = addBus($data);
            echo json_encode(['status' => 'success', 'bus_id' => $busId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing bus ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['route_id']) || !isset($data['company_id']) || 
            !isset($data['bus_driver_id']) || !isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $updated = updateBus($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Bus updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Bus not found or no changes made']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing bus ID']);
            exit;
        }

        try {
            $deleted = deleteBus($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Bus deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Bus not found']);
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
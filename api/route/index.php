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

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['route_id']) || !isset($data['stop_id']) || !isset($data['stop_order'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $result = addRouteStop($data);
            if($result) {
                echo json_encode(['status' => 'success', 'message' => 'Route stop added successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to add route stop']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing route ID in URL']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['stop_id']) || !isset($data['stop_order'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields in body']);
            exit;
        }

        try {
            $updated = updateRouteStop($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Route stop updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Route stop not found or no changes made']);
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
            if ($stop_id !== null) {
                // Delete a specific stop from a route
                $deleted = deleteRouteStop($id, $stop_id);
                $message = 'Route stop deleted';
            } else {
                // Delete all stops for a route
                $deleted = deleteRouteStopsByRouteId($id);
                $message = 'All stops for the route have been deleted';
            }

            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => $message]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'No route stops found to delete']);
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
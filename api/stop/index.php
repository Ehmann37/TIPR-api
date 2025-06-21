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

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['stop_name']) || !isset($data['longitude']) || !isset($data['latitude'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $stopId = addStop($data);
            echo json_encode(['status' => 'success', 'stop_id' => $stopId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing stop ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['stop_name']) || !isset($data['longitude']) || !isset($data['latitude'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $updated = updateStop($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Stop updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Stop not found or no changes made']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing stop ID']);
            exit;
        }

        try {
            $deleted = deleteStop($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Stop deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Stop not found']);
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
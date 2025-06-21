<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/DriverModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $drivers = getDrivers();
            echo json_encode(['status' => 'success', 'data' => $drivers]);
        } else {
            $driver = getDriverById($id);
            if ($driver) {
                echo json_encode(['status' => 'success', 'data' => $driver]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Driver not found']);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['company_id']) || !isset($data['first_name']) || !isset($data['last_name']) || 
            !isset($data['license_number']) || !isset($data['contact_info'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $driverId = addDriver($data);
            echo json_encode(['status' => 'success', 'driver_id' => $driverId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing driver ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['company_id']) || !isset($data['first_name']) || !isset($data['last_name']) || 
            !isset($data['license_number']) || !isset($data['contact_info'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
            exit;
        }

        try {
            $updated = updateDriver($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Driver updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Driver not found or no changes made']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing driver ID']);
            exit;
        }

        try {
            $deleted = deleteDriver($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Driver deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Driver not found']);
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
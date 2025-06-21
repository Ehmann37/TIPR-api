<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/DriverModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed. Use DELETE.'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['driver_id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing driver ID'
    ]);
    exit;
}

try {
    $deleted = deleteDriver(intval($data['driver_id']));

    if ($deleted) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Driver deleted successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Driver not found'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete driver',
        'error' => $e->getMessage()
    ]);
} 
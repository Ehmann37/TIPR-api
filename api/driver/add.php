<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/DriverModel.php';

checkAuthorization();

$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
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
<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/DriverModel.php';

checkAuthorization();

$data = json_decode(file_get_contents("php://input"), true);

// Basic validation
if (
    !isset($data['driver_id']) ||
    !isset($data['company_id']) ||
    !isset($data['first_name']) ||
    !isset($data['last_name']) ||
    !isset($data['license_number']) ||
    !isset($data['contact_info'])
) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields'
    ]);
    exit;
}

try {
    $updated = updateDriver($data);

    if ($updated) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Driver updated successfully'
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
        'message' => 'Failed to update driver',
        'error' => $e->getMessage()
    ]);
} 
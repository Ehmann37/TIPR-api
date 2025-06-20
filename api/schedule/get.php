<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

checkAuthorization();

try {
    $schedules = getSchedules();

    echo json_encode([
        'status' => 'success',
        'data' => $schedules
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to retrieve schedules',
        'error' => $e->getMessage()
    ]);
}

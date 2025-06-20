<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

checkAuthorization();

$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['first_trip']) || !isset($data['last_trip']) || !isset($data['time_interval'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

try {
    $scheduleId = addSchedule($data);
    echo json_encode(['status' => 'success', 'schedule_id' => $scheduleId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

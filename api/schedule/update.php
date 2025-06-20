<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed. Use PUT.'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['schedule_id']) ||
    !isset($data['first_trip']) ||
    !isset($data['last_trip']) ||
    !isset($data['time_interval'])
) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields'
    ]);
    exit;
}

try {
    $updated = updateSchedule($data);

    if ($updated) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule updated successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Schedule not found'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update schedule',
        'error' => $e->getMessage()
    ]);
}
?>
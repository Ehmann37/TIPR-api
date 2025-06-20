<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

checkAuthorization();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing schedule ID'
    ]);
    exit;
}

try {
    $deleted = deleteSchedule(intval($_GET['id']));

    if ($deleted) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Schedule deleted successfully'
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
        'message' => 'Failed to delete schedule',
        'error' => $e->getMessage()
    ]);
}

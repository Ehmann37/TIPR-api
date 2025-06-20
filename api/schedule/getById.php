<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';


checkAuthorization();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing schedule ID']);
    exit;
}

$schedule = getScheduleById(intval($_GET['id']));

if ($schedule) {
    echo json_encode(['status' => 'success', 'data' => $schedule]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Schedule not found']);
}

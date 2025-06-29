<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/ScheduleModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $schedules = getSchedules();
            echo json_encode(['status' => 'success', 'data' => $schedules]);
        } else {
            $schedule = getScheduleById($id);
            if ($schedule) {
                echo json_encode(['status' => 'success', 'data' => $schedule]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Schedule not found']);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['first_trip'], $data['last_trip'], $data['time_interval'])) {
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
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}

<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/DriverModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing driver ID']);
    exit;
}

$driver = getDriverById(intval($_GET['id']));

if ($driver) {
    echo json_encode(['status' => 'success', 'data' => $driver]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Driver not found']);
} 
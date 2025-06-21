<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['route_id']) || !isset($data['stop_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing route_id or stop_id"]);
    exit;
}

try {
    $result = deleteRouteStop($data['route_id'], $data['stop_id']);

    if ($result) {
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Route stop not found"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
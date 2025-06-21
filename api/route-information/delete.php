<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteInfoModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['route_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing route ID"]);
    exit;
}

try {
    $result = deleteRouteInfo($data['route_id']);
    echo json_encode(["status" => $result ? "success" : "error"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
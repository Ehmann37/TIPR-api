<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

if (!isset($_GET['route_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing route ID"]);
    exit;
}

try {
    $data = getRouteStopsByRouteId($_GET['route_id']);
    echo json_encode(["status" => "success", "data" => $data]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
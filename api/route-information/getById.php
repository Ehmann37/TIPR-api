<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteInfoModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing route ID"]);
    exit;
}

try {
    $data = getRouteInfoById($_GET['id']);
    if ($data) {
        echo json_encode(["status" => "success", "data" => $data]);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Route information not found"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
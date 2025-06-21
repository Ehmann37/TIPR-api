<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/StopModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['stop_id']) || !isset($data['stop_name']) || !isset($data['longitude']) || !isset($data['latitude'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

try {
    $result = updateStop($data);
    echo json_encode(["status" => $result ? "success" : "error"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

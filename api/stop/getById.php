<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/StopModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing stop ID"]);
    exit;
}

try {
    $stop = getStopById($id);
    if ($stop) {
        echo json_encode(["status" => "success", "data" => $stop]);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Stop not found"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

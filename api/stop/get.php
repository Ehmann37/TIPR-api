<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/StopModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

try {
    $stops = getAllStops();
    echo json_encode(["status" => "success", "data" => $stops]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

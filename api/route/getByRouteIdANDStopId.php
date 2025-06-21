<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/RouteModel.php';

checkAuthorization();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

if (!isset($_GET['route_id']) || !isset($_GET['stop_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing route_id or stop_id"]);
    exit;
}

$route_id = $_GET['route_id'];
$stop_id = $_GET['stop_id'];

try {
    $data = getRouteStopsByRouteId($route_id);
    $filtered = array_filter($data, function ($item) use ($stop_id) {
        return $item['stop_id'] == $stop_id;
    });

    if (empty($filtered)) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Stop not found for this route"]);
    } else {
        echo json_encode(["status" => "success", "data" => array_values($filtered)[0]]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>

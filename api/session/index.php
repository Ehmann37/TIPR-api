<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/SessionController.php';

checkAuthorization();
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        handleTripPost();
        break;
    default:
        http_response_code(405);
        header('Allow: POST');
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
exit;

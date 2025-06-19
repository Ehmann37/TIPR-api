<?php
// CORS + Security Headers (optional: move these here too)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// TOKEN CHECK
define('API_TOKEN', 'trip123api');

function checkAuthorization() {
    $headers = apache_request_headers();
    $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

    if ($token !== API_TOKEN) {
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized access. Invalid token.'
        ]);
        exit;
    }
}

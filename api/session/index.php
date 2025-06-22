<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/Token.php';

checkAuthorization();
header("Content-Type: application/json");

$KEY = 'mysecretkey1234567890abcdef'; // 32 bytes for AES-256 (temporary, for testing purposes)

$payload = json_encode([
    "current_stop" => "talamban",
    "timestamp" => getCurrentTime()
]);

$method = $_SERVER['REQUEST_METHOD'];
$token = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($token === null) {
            $encryptedParam = encryptData(json_encode($payload), $KEY);
            echo json_encode(['status' => 'success', 'token' => $encryptedParam]);
        } else {
            $decryptedData = decryptData($token, $KEY);

            if ($decryptedData !== null) {
                echo json_encode(['status' => 'success', 'data' => $decryptedData]);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
            }
        }
        
        break;
    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
} 
<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/Session.php';
require_once __DIR__ . '/../../models/StopModel.php';
require_once __DIR__ . '/../../models/BusModel.php';


checkAuthorization();
header("Content-Type: application/json");

$KEY = 'mysecretkey1234567890abcdef'; // 32 bytes for AES-256 (temporary, for testing purposes)


$method = $_SERVER['REQUEST_METHOD'];
$token = !empty($_GET['id']) ? $_GET['id'] : null;


switch ($method) {
    case 'GET':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing token parameter']);
            exit;
        }
        
        $decryptedData = decryptData($_GET['id'], $KEY);
        if ($decryptedData === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
            exit;
        }
        
        echo json_encode(['status' => 'success', 'data' => $decryptedData]);
        break;

    case 'POST':
        $input = file_get_contents('php://input');
        if (empty($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Empty request body']);
            exit;
        }

        $data = json_decode($input, true);

        if (!isset($data['bus_id'], $data['longitude'], $data['latitude'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields: bus_id, longitude, latitude']);
            exit;
        }
        

        if (!is_numeric($data['bus_id']) || intval($data['bus_id']) != $data['bus_id']) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid bus_id. It must be an integer.']);
            exit;
        }
        
        $nearestStop = findNearestStop($data['latitude'], $data['longitude']);
        $busId = intval($data['bus_id']);

        checkBusExists($busId);

        if (!$nearestStop) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'No nearby stop found']);
            exit;
        }

        $payload['timestamp'] = getCurrentTime();
        $payload['current_stop'] = $nearestStop['stop_name'];
        $payload['bus_id'] = $data['bus_id'];

        $encryptedParam = encryptData(json_encode($payload), $KEY);
        echo json_encode(['status' => 'success', 'stop_name' => $payload['current_stop'], 'token' => $encryptedParam]);
        break;

    default:
        http_response_code(405);
        header('Allow: GET, POST');
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
exit;
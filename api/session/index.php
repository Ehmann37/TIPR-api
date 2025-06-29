<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../controllers/Session.php';
require_once __DIR__ . '/../../models/StopModel.php';
require_once __DIR__ . '/../../models/BusModel.php';


checkAuthorization();
header("Content-Type: application/json");

$KEY = 'mysecretkey1234567890abcdef'; // 32 bytes for AES-256 (temporary, for testing purposes)


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $input = file_get_contents('php://input');
        if (empty($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Empty request body']);
            exit;
        }
        $data = json_decode($input, true);
        

        if (isset($data['latitude']) && isset($data['longitude']) && isset($data['bus_id'])) {
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
        } elseif (isset($data['id']) && isset($data['payment_id'])) {
            $tripDetails = decryptData($data['id'], $KEY);
            if ($tripDetails === null) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
                exit;
            }

            $passengerDetails = checkPayment($data['payment_id']);

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'trip_details' => $tripDetails,
                    'passenger_details' => $passengerDetails
                ]
            ]);
            break;
            
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request structure']);
        }

    default:
        http_response_code(405);
        header('Allow: GET, POST');
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
exit;
<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../utils/RequestUtils.php';
require_once __DIR__ . '/../../utils/ResponseUtils.php';


checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $queryParams = getQueryParams(['ticket_id', 'payment_id', 'bus_id', 'passenger_status', 'payment_status', 'latitud', 'longitude']);
        handleGetTicket($queryParams);
        break;

    case 'POST':
        handleCreateTicket();
        break;


        $data = json_decode(file_get_contents("php://input"), true);

        

        if (isset($data['payment'])) {
            $ticketData = $data;
            $paymentData = $data['payment'];
            unset($ticketData['payment']);

            $requiredTicketFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'full_name', 'seat_number', 'passenger_category', 'passenger_status', 'boarding_time', 'trip_id'];

            foreach ($requiredTicketFields as $field) {
                if (!isset($ticketData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required ticket field: $field"]);
                    exit;
                }
            }

            $requiredPaymentFields = ['fare_amount', 'payment_id'];
            foreach ($requiredPaymentFields as $field) {
                if (!isset($paymentData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required payment field: $field"]);
                    exit;
                }
            }

            
            try {
                $result = createTicketWithPayment($ticketData, $paymentData);

                if ($result !== null) {
                    incrementTotalPassengers($ticketData['trip_id']);
                    incrementBusPassengerCount($ticketData['bus_id']);
                }

                echo json_encode(['status' => 'success', 'data' => $result]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Payment data is required for ticket creation']);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing ticket ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if ($data === null){
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No data provided to update']);
            exit;
        }

        
        try {
            $result = updateTicket($id, $data);
            if ($result['success']){
                if ($data['passenger_status'] === "left_bus"){
                    decrementBusPassengerCount($id);
                }

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Update Successful',
                    'details' => [
                        'ticket_updated' => $result['ticket_updated'],
                        'payment_updated' => $result['payment_updated']
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No changes were made to ticket or payment.',
                    'details' => [
                        'ticket_updated' => $result['ticket_updated'],
                        'payment_updated' => $result['payment_updated']
                    ]
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        respond(405, 'Method Not Allowed');

}
<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/TicketModel.php';
require_once __DIR__ . '/../../models/PaymentModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;
$busId = !empty($_GET['busId']) ? $_GET['busId'] : null;
$passengerStatus = !empty($_GET['passengerStatus']) ? $_GET['passengerStatus'] : null;
$payment_status = !empty($_GET['paymentStatus']) ? $_GET['paymentStatus'] : null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $filters = [];

            if ($busId !== null) {
                $filters['bus_id'] = $busId;
            }

            if ($passengerStatus !== null) {
                $filters['pass$passengerStatus'] = $passengerStatus;
            }

            if ($payment_status !== null) {
                $filters['payment_status'] = $payment_status;
            }

            $tickets = getTickets($filters);
            echo json_encode(['pass$passengerStatus' => 'success', 'data' => $tickets]);
        } else {
            $ticket = getTicketById($id);
            if ($ticket) {
                echo json_encode(['pass$passengerStatus' => 'success', 'data' => $ticket]);
            } else {
                http_response_code(404);
                echo json_encode(['pass$passengerStatus' => 'error', 'message' => 'Ticket not found']);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['payment'])) {
            $ticketData = $data;
            $paymentData = $data['payment'];
            unset($ticketData['payment']);

            $requiredTicketFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'full_name', 'seat_number', 'passenger_category', 'passenger_status', 'boarding_time'];
            foreach ($requiredTicketFields as $field) {
                if (!isset($ticketData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required ticket field: $field"]);
                    exit;
                }
            }

            $requiredPaymentFields = ['payment_mode', 'payment_platform', 'fare_amount', 'payment_status'];
            foreach ($requiredPaymentFields as $field) {
                if (!isset($paymentData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required payment field: $field"]);
                    exit;
                }
            }

            try {
                $result = createTicketWithPayment($ticketData, $paymentData);
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

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing ticket ID']);
            exit;
        }

        try {
            $deleted = deleteTicket($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}
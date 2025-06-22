<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/TicketModel.php';
require_once __DIR__ . '/../../models/PaymentModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id === null) {
            $tickets = getTickets();
            echo json_encode(['status' => 'success', 'data' => $tickets]);
        } else {
            $ticket = getTicketWithPayment($id);
            if ($ticket) {
                echo json_encode(['status' => 'success', 'data' => $ticket]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found']);
            }
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if this is a combined ticket+payment creation
        if (isset($data['payment'])) {
            // Combined ticket and payment creation
            $ticketData = $data;
            $paymentData = $data['payment'];
            unset($ticketData['payment']);

            // Validate ticket data
            $requiredTicketFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'first_name', 'last_name', 'seat_number', 'passenger_category', 'boarding_time', 'arrival_time'];
            foreach ($requiredTicketFields as $field) {
                if (!isset($ticketData[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required ticket field: $field"]);
                    exit;
                }
            }

            // Validate payment data
            $requiredPaymentFields = ['payment_mode', 'payment_platform', 'fare_amount'];
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
            // Regular ticket creation only
            $requiredFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'first_name', 'last_name', 'seat_number', 'passenger_category', 'boarding_time', 'arrival_time'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                    exit;
                }
            }

            try {
                $ticketId = addTicket($data);
                echo json_encode(['status' => 'success', 'ticket_id' => $ticketId]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing ticket ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $requiredFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'first_name', 'last_name', 'seat_number', 'passenger_category', 'boarding_time', 'arrival_time'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                exit;
            }
        }

        try {
            $updated = updateTicket($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Ticket not found or no changes made']);
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
<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/PaymentModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;
$ticketId = !empty($_GET['ticket_id']) ? $_GET['ticket_id'] : null;

switch ($method) {
    case 'GET':
        if ($id !== null) {
            // Get payment by payment_id
            $payment = getPaymentById($id);
            if ($payment) {
                echo json_encode(['status' => 'success', 'data' => $payment]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found']);
            }
        } elseif ($ticketId !== null) {
            // Get payment by ticket_id
            $payment = getPaymentByTicketId($ticketId);
            if ($payment) {
                echo json_encode(['status' => 'success', 'data' => $payment]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found for this ticket']);
            }
        } else {
            // Get all payments
            $payments = getPayments();
            echo json_encode(['status' => 'success', 'data' => $payments]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $requiredFields = ['ticket_id', 'payment_mode', 'payment_platform', 'fare_amount'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                exit;
            }
        }

        try {
            $paymentId = addPayment($data);
            echo json_encode(['status' => 'success', 'payment_id' => $paymentId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'PUT':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing payment ID']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $requiredFields = ['ticket_id', 'payment_mode', 'payment_platform', 'fare_amount'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => "Missing required field: $field"]);
                exit;
            }
        }

        try {
            $updated = updatePayment($id, $data);
            if ($updated) {
                echo json_encode(['status' => 'success', 'message' => 'Payment updated']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found or no changes made']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        if ($id === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing payment ID']);
            exit;
        }

        try {
            $deleted = deletePayment($id);
            if ($deleted) {
                echo json_encode(['status' => 'success', 'message' => 'Payment deleted']);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found']);
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

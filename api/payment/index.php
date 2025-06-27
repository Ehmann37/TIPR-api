<?php
require_once __DIR__ . '/../middleware.php';
require_once __DIR__ . '/../../models/PaymentModel.php';

checkAuthorization();

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$id = !empty($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id !== null) {
            $payment = getPaymentById($id);
            if ($payment) {
                echo json_encode(['status' => 'success', 'data' => $payment]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Payment not found']);
            }
        } else {
            $payments = getPayments();
            echo json_encode(['status' => 'success', 'data' => $payments]);
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

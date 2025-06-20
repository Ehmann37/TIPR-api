<?php
require_once __DIR__ . '/middleware.php';
require_once __DIR__ . '/../models/PassengerModel.php';

// Check token
checkAuthorization();

// Proceed with logic
$data = json_decode(file_get_contents("php://input"), true);

try {
    $ticketId = addPassengerTicket($data);
    echo json_encode(['status' => 'success', 'ticket_id' => $ticketId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

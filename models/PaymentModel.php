<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/TicketModel.php';
require_once __DIR__ . '/StopModel.php';
require_once __DIR__ . '/../utils/DBUtils.php';


function addPayment(array $data): bool {
    return insertRecord('payment', $data);
}

function updatePayment($ticket_id, $data, $allowedFields) {
    global $pdo;
    
    $sql = "Select payment_id FROM ticket WHERE ticket_id = :ticket_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $ticket_id]);
    $payment_id = $stmt->fetchColumn();

    return updateRecord('payment', 'payment_id', $payment_id, $data, $allowedFields);
}

function checkPaymentExists($id) {
    return checkExists('payment', 'payment_id', $id);
}

function getPaymentByTicketId($ticket_id) {
    global $pdo;

    $sql = "SELECT p.payment_id, p.amount, p.status, p.created_at, t.ticket_id
            FROM payment p
            JOIN ticket t ON p.payment_id = t.payment_id
            WHERE t.ticket_id = :ticket_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $ticket_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
<?php
require_once __DIR__ . '/../config/db.php';
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


function checkUnpaidTickets($ticket_id){
    global $pdo;

    $sql = "SELECT full_name FROM ticket 
    JOIN payment ON ticket.payment_id = payment.payment_id
    WHERE ticket.ticket_id = :ticket_id AND payment.payment_status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $ticket_id]);
    
    return $stmt->fetchColumn() !== false;
}
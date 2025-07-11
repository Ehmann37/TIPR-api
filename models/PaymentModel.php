<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/TicketModel.php';
require_once __DIR__ . '/StopModel.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function addPayment($data) {
    global $pdo;

    $sql = "INSERT INTO payment (ticket_id, payment_id, payment_mode, payment_platform, fare_amount, payment_status)
            VALUES (:ticket_id, :payment_id, :payment_mode, :payment_platform, :fare_amount, :payment_status)";


    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ticket_id' => $data['ticket_id'],
        ':payment_id' => $data['payment_id'],
        ':payment_mode' => isset($data['payment_mode']) ? $data['payment_mode'] : null,
        ':payment_platform' => isset($data['payment_platform']) ? $data['payment_platform'] : null,
        ':payment_status' => isset($data['payment_status']) ? $data['payment_status'] : 'pending',
        ':fare_amount' => isset($data['fare_amount']) ? $data['fare_amount'] : null
    ]);

    
    return $data['payment_id'] ?? $pdo->lastInsertId();
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
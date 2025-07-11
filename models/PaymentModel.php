<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/TicketModel.php';
require_once __DIR__ . '/StopModel.php';

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

function updatePayment($id, $data) {
    global $pdo;

    $fieldsToUpdate = [];
    $params = [];

    $allowedFields = ['payment_status'];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $fieldsToUpdate[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (empty($fieldsToUpdate)) {
        return false; 
    }


    $sql = "UPDATE payment SET " . implode(", ", $fieldsToUpdate) . " WHERE ticket_id = :ticket_id";
    $params[':ticket_id'] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt;
}

function checkPaymentExists($id) {
    return checkExists('payment', 'payment_id', $id);
}
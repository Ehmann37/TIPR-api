<?php
require_once __DIR__ . '/../config/db.php';

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

function getPayments() {
    global $pdo;

    $sql = "SELECT p.*, pt.full_name, pt.seat_number
            FROM payment p
            LEFT JOIN ticket pt ON p.ticket_id = pt.ticket_id
            ORDER BY p.payment_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}   

function getPaymentById($id) {
    global $pdo;
    
    $sql = "SELECT p.*, pt.full_name, pt.seat_number
            FROM payment p
            LEFT JOIN ticket pt ON p.ticket_id = pt.ticket_id
            WHERE p.payment_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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

function deletePayment($id) {
    global $pdo;

    $sql = "DELETE FROM payment WHERE payment_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
}


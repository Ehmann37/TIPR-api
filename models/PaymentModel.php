<?php
require_once __DIR__ . '/../config/db.php';

function addPayment($data) {
    global $pdo;

    $sql = "INSERT INTO payment (ticket_id, payment_mode, payment_platform, fare_amount)
            VALUES (:ticket_id, :payment_mode, :payment_platform, :fare_amount)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ticket_id' => $data['ticket_id'],
        ':payment_mode' => $data['payment_mode'],
        ':payment_platform' => $data['payment_platform'],
        ':fare_amount' => $data['fare_amount']
    ]);

    return $pdo->lastInsertId();
}

function getPayments() {
    global $pdo;

    $sql = "SELECT p.*, pt.first_name, pt.last_name, pt.seat_number
            FROM payment p
            LEFT JOIN passenger_ticket pt ON p.ticket_id = pt.ticket_id
            ORDER BY p.payment_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPaymentById($id) {
    global $pdo;
    
    $sql = "SELECT p.*, pt.first_name, pt.last_name, pt.seat_number
            FROM payment p
            LEFT JOIN passenger_ticket pt ON p.ticket_id = pt.ticket_id
            WHERE p.payment_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPaymentByTicketId($ticketId) {
    global $pdo;
    
    $sql = "SELECT p.*, pt.first_name, pt.last_name, pt.seat_number
            FROM payment p
            LEFT JOIN passenger_ticket pt ON p.ticket_id = pt.ticket_id
            WHERE p.ticket_id = :ticket_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $ticketId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updatePayment($id, $data) {
    global $pdo;

    $sql = "UPDATE payment SET 
                ticket_id = :ticket_id,
                payment_mode = :payment_mode,
                payment_platform = :payment_platform,
                fare_amount = :fare_amount
            WHERE payment_id = :payment_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ticket_id' => $data['ticket_id'],
        ':payment_mode' => $data['payment_mode'],
        ':payment_platform' => $data['payment_platform'],
        ':fare_amount' => $data['fare_amount'],
        ':payment_id' => intval($id)
    ]);

    return $stmt->rowCount() > 0;
}

function deletePayment($id) {
    global $pdo;

    $sql = "DELETE FROM payment WHERE payment_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
}
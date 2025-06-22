<?php
require_once __DIR__ . '/../config/db.php';

function addTicket($data) {
    global $pdo;

    $sql = "INSERT INTO passenger_ticket (bus_id, origin_stop_id, destination_stop_id, first_name, last_name, seat_number, passenger_category, boarding_time, arrival_time)
            VALUES (:bus_id, :origin_stop_id, :destination_stop_id, :first_name, :last_name, :seat_number, :passenger_category, :boarding_time, :arrival_time)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':bus_id' => $data['bus_id'],
        ':origin_stop_id' => $data['origin_stop_id'],
        ':destination_stop_id' => $data['destination_stop_id'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':seat_number' => $data['seat_number'],
        ':passenger_category' => $data['passenger_category'],
        ':boarding_time' => $data['boarding_time'],
        ':arrival_time' => $data['arrival_time']
    ]);

    return $pdo->lastInsertId();
}

function getTickets() {
    global $pdo;

    $sql = "SELECT pt.*, b.bus_id, s1.stop_name as origin_stop, s2.stop_name as destination_stop
            FROM passenger_ticket pt
            LEFT JOIN bus b ON pt.bus_id = b.bus_id
            LEFT JOIN stop s1 ON pt.origin_stop_id = s1.stop_id
            LEFT JOIN stop s2 ON pt.destination_stop_id = s2.stop_id
            ORDER BY pt.ticket_id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTicketById($id) {
    global $pdo;
    
    $sql = "SELECT pt.*, b.bus_id, s1.stop_name as origin_stop, s2.stop_name as destination_stop
            FROM passenger_ticket pt
            LEFT JOIN bus b ON pt.bus_id = b.bus_id
            LEFT JOIN stop s1 ON pt.origin_stop_id = s1.stop_id
            LEFT JOIN stop s2 ON pt.destination_stop_id = s2.stop_id
            WHERE pt.ticket_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTicketWithPayment($id) {
    global $pdo;
    
    $sql = "SELECT pt.*, b.bus_id, s1.stop_name as origin_stop, s2.stop_name as destination_stop,
                   p.payment_id, p.payment_mode, p.payment_platform, p.fare_amount, p.payment_timestamp
            FROM passenger_ticket pt
            LEFT JOIN bus b ON pt.bus_id = b.bus_id
            LEFT JOIN stop s1 ON pt.origin_stop_id = s1.stop_id
            LEFT JOIN stop s2 ON pt.destination_stop_id = s2.stop_id
            LEFT JOIN payment p ON pt.ticket_id = p.ticket_id
            WHERE pt.ticket_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateTicket($id, $data) {
    global $pdo;

    $sql = "UPDATE passenger_ticket SET 
                bus_id = :bus_id,
                origin_stop_id = :origin_stop_id,
                destination_stop_id = :destination_stop_id,
                first_name = :first_name,
                last_name = :last_name,
                seat_number = :seat_number,
                passenger_category = :passenger_category,
                boarding_time = :boarding_time,
                arrival_time = :arrival_time
            WHERE ticket_id = :ticket_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':bus_id' => $data['bus_id'],
        ':origin_stop_id' => $data['origin_stop_id'],
        ':destination_stop_id' => $data['destination_stop_id'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':seat_number' => $data['seat_number'],
        ':passenger_category' => $data['passenger_category'],
        ':boarding_time' => $data['boarding_time'],
        ':arrival_time' => $data['arrival_time'],
        ':ticket_id' => intval($id)
    ]);

    return $stmt->rowCount() > 0;
}

function deleteTicket($id) {
    global $pdo;

    // First delete associated payment
    $sql = "DELETE FROM payment WHERE ticket_id = :ticket_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $id]);

    // Then delete the ticket
    $sql = "DELETE FROM passenger_ticket WHERE ticket_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
}

function createTicketWithPayment($ticketData, $paymentData) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Create ticket first
        $ticketId = addTicket($ticketData);
        
        // Add ticket_id to payment data
        $paymentData['ticket_id'] = $ticketId;
        
        // Create payment
        $paymentId = addPayment($paymentData);
        
        $pdo->commit();
        
        return [
            'ticket_id' => $ticketId,
            'payment_id' => $paymentId
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

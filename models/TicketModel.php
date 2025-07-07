<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/PaymentModel.php';

function createTicketWithPayment($ticketData, $paymentData) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $ticketId = addTicket($ticketData);

        $paymentData['ticket_id'] = $ticketId;
        
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

function addTicket($data) {
    global $pdo;
    

    $sql = "INSERT INTO ticket (bus_id, origin_stop_id, destination_stop_id, full_name, seat_number, passenger_category, boarding_time, arrival_time, passenger_status, contact_info, trip_id)
            VALUES (:bus_id, :origin_stop_id, :destination_stop_id, :full_name, :seat_number, :passenger_category, :boarding_time, :arrival_time, :passenger_status, :contact_info, :trip_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':bus_id' => $data['bus_id'],
        ':origin_stop_id' => $data['origin_stop_id'],
        ':destination_stop_id' => $data['destination_stop_id'],
        ':full_name' => $data['full_name'],
        ':seat_number' => $data['seat_number'],
        ':passenger_category' => $data['passenger_category'],
        ':passenger_status' => $data['passenger_status'],
        ':boarding_time' => $data['boarding_time'],
        ':arrival_time' => isset($data['arrival_time']) ? $data['arrival_time'] : null,
        ':contact_info' => isset($data['contact_info']) ? $data['contact_info'] : null,
        ':trip_id' => $data['trip_id']
    ]);


    return $pdo->lastInsertId();
}

function getTickets($filters = []) {
    global $pdo;

    $sql = "SELECT t.*, b.bus_id FROM ticket t
            LEFT JOIN bus b ON t.bus_id = b.bus_id
            LEFT JOIN payment p ON t.ticket_id = p.ticket_id
            WHERE 1=1";

    $busId = $filters['bus_id'] ?? null;
    $boardingStatus = $filters['passengerStatus'] ?? null;
    $payment_status = $filters['payment_status'] ?? null;


    $params = [];
    
   

    if ($busId !== null) {
        $sql .= ' AND t.bus_id = :bus_id';
        $params[':bus_id'] = $busId;
    }
    
    if ($boardingStatus !== null) {
        $sql .= ' AND t.passengerStatus = :passengerStatus';
        $params[':passengerStatus'] = $boardingStatus;
    }

    if ($payment_status !== null) {
        $sql .= ' AND p.payment_status = :payment_status';
        $params[':payment_status'] = $payment_status;
    }


    $sql .= ' ORDER BY t.ticket_id ASC';
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tickets)) {
        return [];
    }
    
    $result = [];
    foreach ($tickets as $ticket) {
        $payment = getPaymentFromTicket($ticket['ticket_id']);
        
        if ($payment && isset($payment['ticket_id'])) {
            unset($payment['ticket_id']);
        }
        
        $ticket['payment'] = $payment ?: null;
        $result[] = $ticket;
    }
    
    return $result;
}

function getPaymentFromTicket($id) {
    global $pdo;
    
    $sql = "SELECT * FROM payment WHERE ticket_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTicketById($id) {
    global $pdo;
    
    $sql = "SELECT t.*, b.bus_id FROM ticket t
            LEFT JOIN bus b ON t.bus_id = b.bus_id
            WHERE t.ticket_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        return null; 
    }
    
    $payment = getPaymentFromTicket($id);
    
    if ($payment) {
        unset($payment['ticket_id']);
        $ticket['payment'] = $payment;
    } else {
        $ticket['payment'] = null; 
    }
    
    return $ticket;
}

function updateTicket($id, $data) {
    global $pdo;

    $fieldsToUpdate = [];
    $params = [];

    $ticketUpdated = false;
    $paymentUpdated = false;

    
    $allowedFields = ['passenger_status', 'boarding_time', 'arrival_time', 'seat_number', 'passenger_category'];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $fieldsToUpdate[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (!empty($fieldsToUpdate)) {
        $sql = "UPDATE ticket SET " . implode(", ", $fieldsToUpdate) . " WHERE ticket_id = :ticket_id";
        $params[':ticket_id'] = $id;
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $ticketUpdated = $stmt->rowCount() > 0;
    }



    if (isset($data['payment'])) {
        $stmt = updatePayment($id, $data['payment']);
        if ($stmt) {
            $paymentUpdated = true;
        }
    }

    return [
        'ticket_updated' => $ticketUpdated,
        'payment_updated' => $paymentUpdated,
        'success' => $ticketUpdated || $paymentUpdated
    ];
}

function deleteTicket($id) {
    global $pdo;

    $sql = "DELETE FROM payment WHERE ticket_id = :ticket_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $id]);

    $sql = "DELETE FROM ticket WHERE ticket_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
}



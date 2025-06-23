<?php
require_once __DIR__ . '/../config/db.php';

function addTicket($data) {
    global $pdo;

    $sql = "INSERT INTO passenger_ticket (bus_id, origin_stop_id, destination_stop_id, first_name, last_name, seat_number, passenger_category, boarding_time, arrival_time, passenger_status)
            VALUES (:bus_id, :origin_stop_id, :destination_stop_id, :first_name, :last_name, :seat_number, :passenger_category, :boarding_time, :arrival_time, :passenger_status)";

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
        ':passenger_status' => isset($data['passenger_status']) ? $data['passenger_status'] : 'onboard'
    ]);

    return $pdo->lastInsertId();
}

function getTickets($filters = []) {
    global $pdo;

    // Base query
    $sql = "SELECT pt.*, b.bus_id FROM passenger_ticket pt
            LEFT JOIN bus b ON pt.bus_id = b.bus_id
            WHERE 1=1";
    $params = [];
    
    // Handle bus_id filter (accepts both 'busid' and 'bus_id')
    $busId = $filters['busid'] ?? $filters['bus_id'] ?? null;
    if ($busId !== null) {
        $sql .= ' AND pt.bus_id = :bus_id';
        $params[':bus_id'] = $busId;
    }
    
    // Handle passenger_status filter
    if (isset($filters['status'])) {
        $sql .= ' AND pt.passenger_status = :passenger_status';
        $params[':passenger_status'] = $filters['status'];
    }

    // Add sorting
    $sql .= ' ORDER BY pt.ticket_id DESC';
    
    // Execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no tickets found, return empty array
    if (empty($tickets)) {
        return [];
    }
    
    // Get payment info for each ticket
    $result = [];
    foreach ($tickets as $ticket) {
        $payment = getPayment($ticket['ticket_id']);
        
        // Remove duplicate ticket_id from payment if it exists
        if ($payment && isset($payment['ticket_id'])) {
            unset($payment['ticket_id']);
        }
        
        // Combine ticket and payment info
        $ticket['payment'] = $payment ?: null;
        $result[] = $ticket;
    }
    
    return $result;
}

function getPayment($id) {
    global $pdo;
    
    $sql = "SELECT * FROM payment WHERE ticket_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTicketById($id) {
    global $pdo;
    
    // Get ticket information
    $sql = "SELECT pt.*, b.bus_id FROM passenger_ticket pt
            LEFT JOIN bus b ON pt.bus_id = b.bus_id
            WHERE pt.ticket_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ticket) {
        return null; // Ticket not found
    }
    
    // Get payment information
    $payment = getPayment($id);
    
    // Combine ticket and payment information
    if ($payment) {
        unset($payment['ticket_id']);
        $ticket['payment'] = $payment;
    } else {
        $ticket['payment'] = null; // No payment found
    }
    
    return $ticket;
}

// function getTicketWithPayment($id) {
//     global $pdo;
    
//     $sql = "SELECT pt.*, b.bus_id, s1.stop_name as origin_stop, s2.stop_name as destination_stop,
//                    p.payment_id, p.payment_mode, p.payment_platform, p.fare_amount, p.payment_timestamp
//             FROM passenger_ticket pt
//             LEFT JOIN bus b ON pt.bus_id = b.bus_id
//             LEFT JOIN stop s1 ON pt.origin_stop_id = s1.stop_id
//             LEFT JOIN stop s2 ON pt.destination_stop_id = s2.stop_id
//             LEFT JOIN payment p ON pt.ticket_id = p.ticket_id
//             WHERE pt.ticket_id = :id";
    
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([':id' => $id]);
//     return $stmt->fetch(PDO::FETCH_ASSOC);
// }

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
                arrival_time = :arrival_time,
                passenger_status = :passenger_status
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
        ':passenger_status' => isset($data['passenger_status']) ? $data['passenger_status'] : 'onboard',
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

// function ticketHasPayment($id) {
//     global $pdo;

//     $sql = "SELECT COUNT(*) FROM payment WHERE ticket_id = :ticket_id";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute(['ticket_id' => $id]);
//     $count = $stmt->fetchColumn();

//     return ($count > 0) ? 1 : 0;
// }
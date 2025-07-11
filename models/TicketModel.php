<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/PaymentModel.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getTickets($filters = []) {
    global $pdo;

    $params = [];
    $where = buildWhereClause([
        'bus_id' => $filters['bus_id'] ?? null;
        'boarding_status' => $filters['passengerStatus'] ?? null;
        'payment_status' => $filters['payment_status'] ?? null;
    ], $params);


    $sql = "SELECT t.*, b.bus_id, s_orig.stop_name AS origin_stop_name, s_dest.stop_name AS destination_stop_name, p.*
        FROM ticket t
        LEFT JOIN stop s_orig ON t.origin_stop_id = s_orig.stop_id
        LEFT JOIN stop s_dest ON t.destination_stop_id = s_dest.stop_id
        LEFT JOIN bus b ON t.bus_id = b.bus_id
        LEFT JOIN payment p ON t.payment_id = p.payment_id
        WHERE 1=1 $where ORDER BY t.ticket_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTicketPaymentId($paymentId) {
    global $pdo;

    $paymentSql = "SELECT * FROM payment WHERE payment_id = :payment_id";
    $paymentStmt = $pdo->prepare($paymentSql);
    $paymentStmt->execute([':payment_id' => $paymentId]);
    $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);

    $ticketSql = "SELECT * FROM ticket WHERE payment_id = :payment_id";
    $ticketStmt = $pdo->prepare($ticketSql);
    $ticketStmt->execute([':payment_id' => $paymentId]);
    $tickets = $ticketStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'payment' => $payment,
        'tickets' => $tickets
    ];
}

function getTicketByTicketId($ticketId) {
    global $pdo;
    
    $sql = "SELECT t.*,p.* FROM ticket t
            JOIN payment p ON t.payment_id = p.payment_id
            WHERE t.ticket_id = :ticket_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $paymentId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkTicketExists($id) {
    return checkExists('ticket', 'ticket_id', $id);
}



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


function getTicketsByLocation($stop_id, $trip_id){
    global $pdo;

    $sql = "SELECT * FROM ticket WHERE destination_stop_id = :stop_id AND trip_id = :trip_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':stop_id' => $stop_id,
        ':trip_id' => $trip_id
    ]);

    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tickets as &$ticket) {
        $payment = getPaymentFromTicket($ticket['ticket_id']);
        $ticket['payment'] = $payment ? $payment : null;
    }

    return $tickets;
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




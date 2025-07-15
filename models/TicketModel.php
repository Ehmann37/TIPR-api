<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/PaymentModel.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getPaymentFromTicket($id) {
    global $pdo;
    
    $sql = "SELECT * FROM payment WHERE payment_id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTickets($filters = []) {
    global $pdo;

    $params = [];
    $where = buildWhereClause([
        't.bus_id' => $filters['bus_id'] ?? null,
        't.passenger_status' => $filters['passenger_status'] ?? null,
        't.passenger_category' => $filters['passenger_category'] ?? null,
        'p.payment_status' => $filters['payment_status'] ?? null
    ], $params);

    $sql = "SELECT t.*, b.bus_id, s_orig.stop_name AS origin_stop_name, s_dest.stop_name AS destination_stop_name
        FROM ticket t
        LEFT JOIN stop s_orig ON t.origin_stop_id = s_orig.stop_id
        LEFT JOIN stop s_dest ON t.destination_stop_id = s_dest.stop_id
        LEFT JOIN bus b ON t.bus_id = b.bus_id
        LEFT JOIN payment p ON t.payment_id = p.payment_id
        WHERE 1=1 $where ORDER BY t.ticket_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets =  $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tickets)) {
        return [];
    }

    $result = [];
    foreach ($tickets as $ticket) {
        $payment = getPaymentFromTicket($ticket['payment_id']);
        
        $ticket['payment'] = $payment ?: null;
        $result[] = $ticket;
    }
    
    return $result;
}

function getTicketByPaymentId($payment_id) {
    global $pdo;

    $paymentSql = "SELECT * FROM payment WHERE payment_id = :payment_id";
    $paymentStmt = $pdo->prepare($paymentSql);
    $paymentStmt->execute([':payment_id' => $payment_id]);
    $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);

    $ticketSql = "SELECT * FROM ticket WHERE payment_id = :payment_id";
    $ticketStmt = $pdo->prepare($ticketSql);
    $ticketStmt->execute([':payment_id' => $payment_id]);
    $tickets = $ticketStmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'payment' => $payment,
        'tickets' => $tickets
    ];
}

function getTicketByTicketId($ticket_id) {
    global $pdo;
    
    $sql = "SELECT t.*,p.* FROM ticket t
            JOIN payment p ON t.payment_id = p.payment_id
            WHERE t.ticket_id = :ticket_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':ticket_id' => $ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    return $ticket ?: null;
}

function getTotalFareByPaymentId($payment_id) {
    global $pdo;

    $sql = "SELECT SUM(fare_amount) as total_fare
            FROM ticket 
            WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':payment_id' => $payment_id]);
    return $stmt->fetchColumn();
}

function checkTicketExists($id) {
    return checkExists('ticket', 'ticket_id', $id);
}

function getTicketsByLocation($stop_id, $trip_id){
    global $pdo;

    $sql = "SELECT t.*, p.* FROM ticket t 
        JOIN payment p ON t.payment_id = p.payment_id
        WHERE destination_stop_id = :stop_id AND trip_id = :trip_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':stop_id' => $stop_id,
        ':trip_id' => $trip_id
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalPassengersByPaymentId($payment_id) {
    global $pdo;

    $sql = "SELECT COUNT(*) as total_passengers FROM ticket WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':payment_id' => $payment_id]);
    
    return $stmt->fetchColumn();
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

function addTicket(array $data): int|false {
    global $pdo;
    $success = insertRecord('ticket', $data);
    return $success ? $pdo->lastInsertId() : false;
}

function updateTicket($id, $data, $allowedFields) {
    return updateRecord('ticket', 'ticket_id', $id, $data, $allowedFields);
}

function checkSeatConflicts(array $seatNumbers, int $tripId): array {
    global $pdo;

    $placeholders = implode(',', array_fill(0, count($seatNumbers), '?'));
    $query = "SELECT seat_number FROM ticket WHERE trip_id = ? AND seat_number IN ($placeholders)";

    $stmt = $pdo->prepare($query);
    $stmt->execute(array_merge([$tripId], $seatNumbers));

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
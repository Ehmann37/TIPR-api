<?php
require_once __DIR__ . '/../config/db.php';

function addPassengerTicket($data) {
    global $pdo;

    $sql = "INSERT INTO passenger_ticket (
        bus_id, origin_stop_id, destination_stop_id, payment_id, 
        first_name, last_name, seat_number, passenger_category, 
        boarding_time, arrival_time
    ) VALUES (
        :bus_id, :origin_stop_id, :destination_stop_id, :payment_id,
        :first_name, :last_name, :seat_number, :passenger_category,
        :boarding_time, :arrival_time
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':bus_id' => $data['bus_id'],
        ':origin_stop_id' => $data['origin_stop_id'],
        ':destination_stop_id' => $data['destination_stop_id'],
        ':payment_id' => $data['payment_id'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':seat_number' => $data['seat_number'],
        ':passenger_category' => $data['passenger_category'],
        ':boarding_time' => $data['boarding_time'],
        ':arrival_time' => $data['arrival_time'],
    ]);

    return $pdo->lastInsertId();
}

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/TimeUtils.php';
require_once __DIR__ . '/../utils/TimeUtils.php';


function getActiveTrip($status = 'active') {
  global $pdo;

  $sql = "SELECT trip_id FROM trip WHERE status = :status";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':status' => $status]);
  $existingTripId = $stmt->fetchColumn();

  if ($existingTripId) {
      return $existingTripId;
  } else {
    return null;
  }
}

function getTripIdByTicketId($ticket_id) {
  global $pdo;

  $sql = "SELECT t.trip_id FROM trip t
          JOIN ticket ti ON t.trip_id = ti.trip_id
          WHERE ti.ticket_id = :ticket_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':ticket_id' => $ticket_id]);
  return $stmt->fetchColumn();
}

function completeInstatnce($trip_id, $status = 'complete') {
  global $pdo;

  $sql = "UPDATE trip SET status = :status, arrival_time = now() WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':status' => $status, ':trip_id' => $trip_id]);

  return $stmt->rowCount() > 0;
}

function createInstance($route_id, $status = 'active') {
  global $pdo;

  $sql = "
      INSERT INTO trip (route_id, boarding_time, status)
      VALUES (:route_id, :boarding_time, :status)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      ':route_id' => $route_id,
      ':boarding_time' => getCurrentTime(),
      ':status' => $status
  ]);

  if ($stmt->rowCount() > 0) {
    return $pdo->lastInsertId();
  }

  return false;
}

function checkBusifActive($bus_id) {
  global $pdo;

  $sql = "SELECT trip_id FROM trip WHERE bus_id = :bus_id AND status = 'active'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);
  return $stmt->fetchColumn() !== false;
}

function incrementTotalPassengers($trip_id, $numPassengers) {
  global $pdo;

  $sql = "UPDATE trip SET total_passenger = total_passenger + $numPassengers WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':trip_id' => $trip_id]);

}

function incrementTotalRevenue($trip_id, $totalFare) {
  global $pdo;

  $sql = "UPDATE trip SET total_revenue = total_revenue + :total_fare WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      ':total_fare' => $totalFare,
      ':trip_id' => $trip_id
  ]);

  return $stmt->rowCount() > 0;
}

function checkTripExist($trip_id){
  return checkExists('trip', 'trip_id', $trip_id);
}

function checkPassengerLeftBus($trip_id){
  global $pdo;

  $sql = "SELECT COUNT(*) FROM ticket WHERE trip_id = :trip_id AND passenger_status = 'on_bus'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':trip_id' => $trip_id]);
  
  return $stmt->fetchColumn() > 0;
}

function getTripDetails($trip_id){
  global $pdo;

  $sql = 'SELECT * FROM trip WHERE trip_id = :trip_id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':trip_id' => $trip_id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);

}
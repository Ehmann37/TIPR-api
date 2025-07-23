<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/TimeUtils.php';
require_once __DIR__ . '/../utils/TimeUtils.php';


function getActiveTrip($status = 'active') {
  global $pdo;

  $sql = "SELECT trip_id FROM trips WHERE status = :status";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':status' => $status]);
  $existingTripId = $stmt->fetchColumn();

  if ($existingTripId) {
      return $existingTripId;
  } else {
    return null;
  }
}

function completeInstatnce($trip_id, $status = 'complete') {
  global $pdo;

  $sql = "UPDATE trips SET status = :status, arrival_time = now() WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':status' => $status, ':trip_id' => $trip_id]);

  return $stmt->rowCount() > 0;
}

function createInstance($trip_id, $route_id, $status = 'active') {
  global $pdo;

  $sql = "
      INSERT INTO trips (trip_id, route_id, boarding_time, status)
      VALUES (:trip_id, :route_id, :boarding_time, :status)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      ':trip_id' => $trip_id,
      ':route_id' => $route_id,
      ':boarding_time' => getCurrentTime(),
      ':status' => $status
  ]);

  return $stmt->rowCount() > 0;
}

function incrementTotalPassengers($trip_id, $numPassengers) {
  global $pdo;

  $sql = "UPDATE trips SET total_passenger = total_passenger + $numPassengers WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':trip_id' => $trip_id]);

}

function incrementTotalRevenue($trip_id, $totalFare) {
  global $pdo;

  $sql = "UPDATE trips SET total_revenue = total_revenue + :total_fare WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      ':total_fare' => $totalFare,
      ':trip_id' => $trip_id
  ]);

  return $stmt->rowCount() > 0;
}

function checkTripExist($trip_id){
  return checkExists('trips', 'trip_id', $trip_id);
}

function checkPassengerLeftBus($trip_id){
  global $pdo;

  $sql = "SELECT COUNT(*) FROM tickets WHERE trip_id = :trip_id AND passenger_status = 'on_bus'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':trip_id' => $trip_id]);
  
  return $stmt->fetchColumn() > 0;
}

function getTripDetails($trip_id){
  global $pdo;

  $sql = 'SELECT * FROM trips WHERE trip_id = :trip_id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':trip_id' => $trip_id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);

}
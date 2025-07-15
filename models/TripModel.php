<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/TimeUtils.php';

function getActiveTrip($bus_id) {
  global $pdo;

  $sql = "SELECT trip_id FROM trip WHERE bus_id = :bus_id AND status = 'active'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);
  $existingTripId = $stmt->fetchColumn();

  if ($existingTripId) {
      return $existingTripId;
  } else {
    return false;
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

function completeInstatnce($bus_id, $status) {
  global $pdo;

  $sql = "SELECT trip_id FROM trip WHERE bus_id = :bus_id AND status = 'active'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);
  $trip_id = $stmt->fetchColumn();


  $sql = "UPDATE trip SET status = :status, arrival_time = now() WHERE trip_id = :trip_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':status' => $status, ':trip_id' => $trip_id]);

  return $stmt->rowCount() > 0;
}

function createInstance($bus_id, $status) {
  global $pdo;

  $sql = "SELECT route_id, driver_id, conductor_id FROM bus WHERE bus_id = :bus_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);
  $busInfo = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$busInfo) {
      throw new Exception("Bus not found");
  }

  $sql = "
      INSERT INTO trip (bus_id, route_id, driver_id, conductor_id, boarding_time, status)
      VALUES (:bus_id, :route_id, :driver_id, :conductor_id, :boarding_time, :status)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      ':bus_id' => $bus_id,
      ':route_id' => $busInfo['route_id'],
      ':driver_id' => $busInfo['driver_id'],
      ':conductor_id' => $busInfo['conductor_id'],
      ':boarding_time' => getCurrentTime(),
      ':status' => 'active'
  ]);

  return $stmt->rowCount() > 0;
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


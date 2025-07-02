<?php
require_once __DIR__ . '/../config/db.php';

function generateTripId($bus_id, $stop_id, $timestamp) {
  global $pdo;

  $sql = "SELECT trip_id FROM trip WHERE bus_id = :bus_id AND status = 'active' ORDER BY boarding_time DESC LIMIT 1";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':bus_id' => $bus_id]);
  $existingTripId = $stmt->fetchColumn();

  if ($existingTripId) {
      return $existingTripId;
  }

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
      ':boarding_time' => $timestamp,
      ':status' => 'active'
  ]);

  return $pdo->lastInsertId();
}


function updateTripStatus($bus_id, $status) {
  global $pdo;

  $sql = "UPDATE trip SET status = :status, arrival_time = now() WHERE bus_id = :bus_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':status' => $status, ':bus_id' => $bus_id]);


  return $stmt->rowCount() > 0;
}

?>
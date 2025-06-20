<?php
require_once __DIR__ . '/../config/db.php';

function addSchedule($data) {
    global $pdo;

    // Convert to 24-hour format for MySQL TIME
    $firstTrip = date("H:i:s", strtotime($data['first_trip']));
    $lastTrip = date("H:i:s", strtotime($data['last_trip']));
    $time_interval = intval($data['time_interval']);

    $sql = "INSERT INTO schedule (first_trip, last_trip, time_interval)
            VALUES (:first_trip, :last_trip, :time_interval)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':first_trip' => $firstTrip,
        ':last_trip' => $lastTrip,
        ':time_interval' => $time_interval
    ]);

    return $pdo->lastInsertId();
}

function getSchedules() {
  global $pdo;

  $sql = "SELECT * FROM schedule ORDER BY schedule_id ASC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getScheduleById($id) {
  global $pdo;
  $sql = "SELECT * FROM schedule WHERE schedule_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateSchedule($data) {
  global $pdo;

  $sql = "UPDATE schedule SET 
              first_trip = :first_trip,
              last_trip = :last_trip,
              time_interval = :time_interval
          WHERE schedule_id = :schedule_id";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
      ':first_trip' => date("H:i:s", strtotime($data['first_trip'])),
      ':last_trip' => date("H:i:s", strtotime($data['last_trip'])),
      ':time_interval' => intval($data['time_interval']),
      ':schedule_id' => intval($data['schedule_id'])
  ]);

  return $stmt->rowCount() > 0;
}

function deleteSchedule($id) {
  global $pdo;

  $sql = "DELETE FROM schedule WHERE schedule_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $id]);

  return $stmt->rowCount() > 0;
}

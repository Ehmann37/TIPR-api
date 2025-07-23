<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function findDistance($lat1, $lon1, $lat2, $lon2) {
  $earthRadius = 6371;

  $dLat = deg2rad($lat2 - $lat1);
  $dLon = deg2rad($lon2 - $lon1);

  $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
  
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  
  return $earthRadius * $c; 
}

function getStopCoordinates(int $stop_id) {
  global $pdo;


  $sql = "SELECT latitude, longitude FROM stops WHERE stop_id = :stop_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':stop_id' => $stop_id]);

  return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
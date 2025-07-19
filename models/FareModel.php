<?php
function getTotalFareByPaymentId($payment_id) {
  global $pdo;

  $sql = "SELECT SUM(fare_amount) as total_fare
          FROM ticket 
          WHERE payment_id = :payment_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':payment_id' => $payment_id]);
  return $stmt->fetchColumn();
}

function calculateFare($distance){
  $base_fare = 40.0;
  $base_distance = 4;
  $additional_kilometer = $distance - $base_distance;
  $rate_per_additional_km = 5;
  $additional_fare = $additional_kilometer * $rate_per_additional_km;
  $total_fare = $base_fare + $additional_fare;
  return [
    'base_distance' => $base_distance,
    'additional_distance' => $additional_kilometer,
    'total_distance' => $distance,
    'base_fare' => $base_fare,
    'rate_per_additional_km' => $rate_per_additional_km,
    'additional_fare' => $additional_fare,
    'total_fare' => $total_fare
  ];
}
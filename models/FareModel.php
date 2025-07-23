<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getTotalFareByPaymentId($payment_id) {
  global $pdo;

  $sql = "SELECT SUM(fare_amount) as total_fare
          FROM tickets 
          WHERE payment_id = :payment_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':payment_id' => $payment_id]);
  return $stmt->fetchColumn();
}

function calculateFare($distance){
  $base_fare = 15.0;
  $base_distance = 4;
  $additional_kilometer = $distance - $base_distance;
  $rate_per_additional_km = 2.10;
  $additional_fare = $additional_kilometer * $rate_per_additional_km;
  $total_fare = $base_fare + $additional_fare;
  return [
    'total_fare' => $total_fare,
    'breakdown' => '₱' . number_format($base_fare, 2) .' base fare + ₱' . number_format($rate_per_additional_km, 2) . ' per km',  
  ];
}
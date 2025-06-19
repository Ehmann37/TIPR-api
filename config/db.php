<?php
$servername = "localhost";
$username = "s24103435_trip-api";
$password = "api-trip";
$dbname = "s24103435_trip-api";

try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Set default fetch mode to associative arraya
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Connection successful
    // echo "Connected successfully";

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
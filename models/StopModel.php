<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getstopById($stopId) {
    global $pdo;

    $sql = "SELECT stop_id, stop_name, latitude, longitude FROM stop WHERE stop_id = :stop_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':stop_id' => $stopId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getStopsByTripId($trip_id, $currentStopId) {
    global $pdo;

    $sqlRoute = "SELECT route_id FROM trip WHERE trip_id = :trip_id";
    $stmtRoute = $pdo->prepare($sqlRoute);
    $stmtRoute->execute([':trip_id' => $trip_id]);
    $route = $stmtRoute->fetch(PDO::FETCH_ASSOC);

    if (!$route) return []; 
    $routeId = $route['route_id'];

    $sqlCurrentOrder = "SELECT stop_order FROM route WHERE route_id = :route_id AND stop_id = :stop_id";
    $stmtCurrentOrder = $pdo->prepare($sqlCurrentOrder);
    $stmtCurrentOrder->execute([
        ':route_id' => $routeId,
        ':stop_id' => $currentStopId
    ]);
    $currentOrder = $stmtCurrentOrder->fetchColumn();

    if ($currentOrder === false) return []; 

    $sqlStops = "
        SELECT s.stop_name, s.stop_id
        FROM route r
        JOIN stop s ON r.stop_id = s.stop_id
        WHERE r.route_id = :route_id
          AND r.stop_order > :current_order
        ORDER BY r.stop_order ASC
    ";
    $stmtStops = $pdo->prepare($sqlStops);
    $stmtStops->execute([
        ':route_id' => $routeId,
        ':current_order' => $currentOrder
    ]);

    return $stmtStops->fetchAll(PDO::FETCH_ASSOC);
}

function findNearestStop($lat, $lng, $radiusMeters = 1000000){
    global $pdo;

    $sql = "
        SELECT stop_id, stop_name, latitude, longitude,
            (6371000 * ACOS(
                COS(RADIANS(:lat)) * COS(RADIANS(latitude)) *
                COS(RADIANS(longitude) - RADIANS(:lng)) +
                SIN(RADIANS(:lat)) * SIN(RADIANS(latitude))
            )) AS distance
        FROM stop
        HAVING distance <= :radius
        ORDER BY distance ASC
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':lat', $lat);
    $stmt->bindParam(':lng', $lng);
    $stmt->bindParam(':radius', $radiusMeters);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkStopExists($id) {
    return checkExists('stop', 'stop_id', $id);
}
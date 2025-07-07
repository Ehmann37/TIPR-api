<?php
require_once __DIR__ . '/../config/db.php';

function addRouteStop($data){
    global $pdo;
    $sql = "INSERT INTO route (route_id, stop_id, stop_order) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['route_id'],
        $data['stop_id'],
        $data['stop_order']
    ]);
}

function getAllRouteStops(){
    global $pdo;
    $sql = "SELECT * FROM route ORDER BY route_id, stop_order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRouteStopsByRouteId($route_id){
    global $pdo;
    
    $RouteInfoSQL = "SELECT route_name, schedule_id FROM route_information WHERE route_id = ?";
    $stmt = $pdo->prepare($RouteInfoSQL);
    $stmt->execute([$route_id]);
    $route_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$route_info) {
        return null; // Route not found
    }

    $StopsSQL = "SELECT s.stop_name, r.stop_id 
    FROM route r
    JOIN stop s ON r.stop_id = s.stop_id
    WHERE route_id = ? ORDER BY stop_order";

    $stmt = $pdo->prepare($StopsSQL);
    $stmt->execute([$route_id]);
    $stops = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        "route_id" => $route_id,
        "route_name" => $route_info['route_name'],
        "schedule_id" => $route_info['schedule_id'],
        "stops" => $stops
    ];
}


function updateRouteStop($route_id, $data){
    global $pdo;
    $sql = "UPDATE route SET stop_order = ? WHERE route_id = ? AND stop_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['stop_order'],
        $route_id,
        $data['stop_id']
    ]);
}

function deleteRouteStop($route_id, $stop_id){
    global $pdo;
    $sql = "DELETE FROM route WHERE route_id = ? AND stop_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$route_id, $stop_id]);
    return $stmt->rowCount() > 0;
}

function deleteRouteStopsByRouteId($route_id){
    global $pdo;
    $sql = "DELETE FROM route WHERE route_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$route_id]);
    return $stmt->rowCount() > 0;
}
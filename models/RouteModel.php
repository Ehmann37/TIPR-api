<?php
require_once __DIR__ . '/../config/db.php';

// ➕ Add a stop to a route
function addRouteStop($data)
{
    global $pdo;
    $sql = "INSERT INTO route (route_id, stop_id, stop_order) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['route_id'],
        $data['stop_id'],
        $data['stop_order']
    ]);
}

// 📥 Get all route-stop mappings
function getAllRouteStops()
{
    global $pdo;
    $sql = "SELECT * FROM route ORDER BY route_id, stop_order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🔍 Get stops for a specific route
function getRouteStopsByRouteId($route_id)
{
    global $pdo;
    $sql = "SELECT * FROM route WHERE route_id = ? ORDER BY stop_order";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$route_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✏️ Update a stop's order in a route
function updateRouteStop($data)
{
    global $pdo;
    $sql = "UPDATE route SET stop_order = ? WHERE route_id = ? AND stop_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['stop_order'],
        $data['route_id'],
        $data['stop_id']
    ]);
    return $stmt->rowCount() > 0;
}

// 🗑️ Delete a stop from a route
function deleteRouteStop($route_id, $stop_id)
{
    global $pdo;
    $sql = "DELETE FROM route WHERE route_id = ? AND stop_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$route_id, $stop_id]);
    return $stmt->rowCount() > 0;
}
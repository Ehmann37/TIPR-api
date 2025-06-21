<?php
require_once __DIR__ . '/../config/db.php';

// 🔍 Get all route information
function getAllRoutesInfo()
{
    global $pdo;
    $sql = "SELECT * FROM route_information";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🔍 Get a specific route info by ID
function getRouteInfoById($route_id)
{
    global $pdo;
    $sql = "SELECT * FROM route_information WHERE route_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$route_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ➕ Add a new route information
function addRouteInfo($data)
{
    global $pdo;
    $sql = "INSERT INTO route_information (route_name, schedule_id) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['route_name'],
        $data['schedule_id']
    ]);
}

// ✏️ Update route information
function updateRouteInfo($data)
{
    global $pdo;
    $sql = "UPDATE route_information SET route_name = ?, schedule_id = ? WHERE route_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['route_name'],
        $data['schedule_id'],
        $data['route_id']
    ]);
}

// 🗑️ Delete route information
function deleteRouteInfo($route_id)
{
    global $pdo;
    $sql = "DELETE FROM route_information WHERE route_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$route_id]);
}

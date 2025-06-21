<?php
require_once __DIR__ . '/../config/db.php';

function addBus($data) {
    global $pdo;

    $sql = "INSERT INTO bus (route_id, company_id, bus_driver_id, status)
            VALUES (:route_id, :company_id, :bus_driver_id, :status)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':route_id' => $data['route_id'],
        ':company_id' => $data['company_id'],
        ':bus_driver_id' => $data['bus_driver_id'],
        ':status' => $data['status']
    ]);

    return $pdo->lastInsertId();
}

function getBuses() {
    global $pdo;

    $sql = "SELECT * FROM bus ORDER BY bus_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBusById($id) {
    global $pdo;
    $sql = "SELECT * FROM bus WHERE bus_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateBus($id, $data) {
    global $pdo;

    $sql = "UPDATE bus SET 
                route_id = :route_id,
                company_id = :company_id,
                bus_driver_id = :bus_driver_id,
                status = :status
            WHERE bus_id = :bus_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':route_id' => $data['route_id'],
        ':company_id' => $data['company_id'],
        ':bus_driver_id' => $data['bus_driver_id'],
        ':status' => $data['status'],
        ':bus_id' => intval($id)
    ]);

    return $stmt->rowCount() > 0;
}

function deleteBus($id) {
    global $pdo;

    $sql = "DELETE FROM bus WHERE bus_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
} 
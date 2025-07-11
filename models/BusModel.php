<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getBusById($id) {
    global $pdo;
    $sql = "SELECT * FROM bus WHERE bus_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getBuses($filters = []) {
    global $pdo;

    $params = [];
    $where = buildWhereClause([
        'route_id' => $filters['route_id'] ?? null,
        'route_status' => $filters['route_status'] ?? null,
        'status' => $filters['status'] ?? null
    ], $params);

    $sql = "SELECT b.* FROM bus b WHERE 1=1 $where ORDER BY b.bus_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateBus($id, $data, $allowedFields) {
    return updateRecord('bus', 'bus_id', $id, $data, $allowedFields);
}

function checkBusExists($id) {
    return checkExists('bus', 'bus_id', $id);
}

function incrementBusPassengerCount($busId) {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE bus SET passenger_count = passenger_count + 1 WHERE bus_id = ?");
    $stmt->execute([$busId]);
        
}

function decrementBusPassengerCount($ticket_id){
    global $pdo;

    $stmt = $pdo->prepare("UPDATE bus SET passenger_count = passenger_count - 1 WHERE bus_id = (SELECT bus_id FROM ticket WHERE ticket_id = ?)");
    $stmt->execute([$ticket_id]);
}
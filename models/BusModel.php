<?php
require_once __DIR__ . '/../config/db.php';

function addBus($data) {
    global $pdo;

    $sql = "INSERT INTO bus (route_id, company_id, bus_driver_id, status, route_status)
            VALUES (:route_id, :company_id, :bus_driver_id, :status, route_status)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':route_id' => $data['route_id'],
        ':company_id' => $data['company_id'],
        ':bus_driver_id' => $data['bus_driver_id'],
        ':status' => $data['status'],
        ':route_status' => $data['route_status']
    ]);

    return $pdo->lastInsertId();
}

function getBuses($filters = []) {
    global $pdo;

    $sql = "SELECT b.* FROM bus b WHERE 1=1";

    $route_id = $filters['route_id'] ?? null;
    $route_status = $filters['route_status'] ?? null;
    $status = $filters['status'] ?? null;

    $params = [];

    if ($route_id !== null) {
        $sql .= ' AND b.route_id = :route_id';
        $params[':route_id'] = $route_id;
    }

    if ($route_status !== null)     {
        $sql .= ' AND b.route_status = :route_status'; 
        $params[':route_status'] = $route_status;
    }

    if ($status !== null) {
        $sql .= ' AND b.status = :status';
        $params[':status'] = $status;
    }

    $sql .= ' ORDER BY b.bus_id ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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

    $allowedFields = ['route_id', 'conductor_id', 'driver_id'];
    $fieldsToUpdate = [];
    $params = [':id' => $id];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $column = $field;
            $fieldsToUpdate[] = "$column = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (empty($fieldsToUpdate)) {
        return false; 
    }

    $sql = "UPDATE bus SET " . implode(', ', $fieldsToUpdate) . " WHERE bus_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount() > 0;
}


function deleteBus($id) {
    global $pdo;

    $sql = "DELETE FROM bus WHERE bus_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
} 

function checkBusExists($busId){
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bus WHERE bus_id = ?");
        $stmt->execute([$busId]);
        $exists = $stmt->fetchColumn();
    
        if (!$exists) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Bus ID not found in database.']);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error', 'error' => $e->getMessage()]);
        exit;
    }
}
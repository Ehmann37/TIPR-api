<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getAlerts($filters = []) {
    global $pdo;

    $params = [];
    $where = buildWhereClause([
        'bus_id' => $filters['bus_id'] ?? null,
        'status' => $filters['status'] ?? null
    ], $params);

    $sql = "SELECT * FROM alert WHERE 1=1 $where ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createAlert(array $data): bool {
    return insertRecord('alert', $data);
}

function updateAlert($id) {
    global $pdo;
    $sql = "UPDATE alert SET has_read = TRUE WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}
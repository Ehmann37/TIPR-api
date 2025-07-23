<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getAlerts($filters = []) {
    global $pdo;

    $params = [];
    $where = buildWhereClause([
        'trip_id' => $filters['trip_id'] ?? null
    ], $params);

    $sql = "SELECT * FROM alerts WHERE 1=1 $where ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createAlert(array $data): bool {
    return insertRecord('alerts', $data);
}

function updateAlert($id) {
    global $pdo;
    $sql = "UPDATE alerts SET has_read = TRUE WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount() > 0;
}

function checkAlertExists($id) {
    return checkExists('alerts', 'id', $id);
}
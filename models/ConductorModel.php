<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/DBUtils.php';

function getConductors() {
    global $pdo;

    $sql = "SELECT *
      FROM user 
      WHERE role = 'conductor'
      ORDER BY user_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getConductorById($id) {
    global $pdo;
    $sql = "SELECT *
      FROM user 
      WHERE user_id = :id and role = 'conductor'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function checkConductorExists($id) {
  return checkExists('user', 'user_id', $id, ['role' => 'conductor']);
}

<?php
require_once __DIR__ . '/../config/db.php';



function getConductors() {
    global $pdo;

    $sql = "SELECT conductor.*, user.full_name, user.contact_info 
      FROM conductor 
      INNER JOIN user ON conductor.conductor_id = user.user_id 
      ORDER BY conductor.conductor_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getConductorById($id) {
    global $pdo;
    $sql = "SELECT conductor.*, user.full_name, user.contact_info 
      FROM conductor 
      INNER JOIN user ON conductor.conductor_id = user.user_id 
      WHERE conductor_id = :id
      ORDER BY conductor.conductor_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


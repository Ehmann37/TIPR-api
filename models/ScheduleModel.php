<?php
require_once __DIR__ . '/../config/db.php';

function getSchedules(){
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM schedule");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getScheduleById($id){
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM schedule WHERE schedule_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

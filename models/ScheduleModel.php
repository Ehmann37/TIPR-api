<?php
require_once __DIR__ . '/../config/db.php';

function getSchedules()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM schedule");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getScheduleById($id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM schedule WHERE schedule_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function addSchedule($data)
{
    global $pdo;
    $data['first_trip'] = date("H:i:s", strtotime($data['first_trip']));
    $data['last_trip'] = date("H:i:s", strtotime($data['last_trip']));
    $stmt = $pdo->prepare("INSERT INTO schedule (first_trip, last_trip, time_interval) VALUES (?, ?, ?)");
    $stmt->execute([$data['first_trip'], $data['last_trip'], $data['time_interval']]);
    return $pdo->lastInsertId();
}

function updateSchedule($id, $data)
{
    global $pdo;
    $data['first_trip'] = date("H:i:s", strtotime($data['first_trip']));
    $data['last_trip'] = date("H:i:s", strtotime($data['last_trip']));
    $stmt = $pdo->prepare("UPDATE schedule SET first_trip = ?, last_trip = ?, time_interval = ? WHERE schedule_id = ?");
    $stmt->execute([$data['first_trip'], $data['last_trip'], $data['time_interval'], $id]);
    return $stmt->rowCount() > 0;
}

function deleteSchedule($id)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM schedule WHERE schedule_id = ?");
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0;
}

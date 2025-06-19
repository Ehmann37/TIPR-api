<?php
require_once __DIR__ . '/../config/db.php';

function getTests()
{
    global $pdo;
    $sql = "SELECT * FROM test";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
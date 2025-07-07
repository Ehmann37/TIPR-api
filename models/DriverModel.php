<?php
require_once __DIR__ . '/../config/db.php';

function addDriver($data) {
    global $pdo;

    $sql = "INSERT INTO driver (company_id, first_name, last_name, license_number, contact_info)
            VALUES (:company_id, :first_name, :last_name, :license_number, :contact_info)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company_id' => $data['company_id'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':license_number' => $data['license_number'],
        ':contact_info' => $data['contact_info']
    ]);

    return $pdo->lastInsertId();
}

function getDrivers() {
    global $pdo;

    $sql = "SELECT driver.*, user.full_name, user.contact_info FROM driver
    INNER JOIN user ON driver.driver_id = user.user_id
    ORDER BY driver.driver_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDriverById($id) {
    global $pdo;
    $sql = "SELECT driver.*, user.full_name, user.contact_info FROM driver
    INNER JOIN user ON driver.driver_id = user.user_id
    WHERE driver_id = :id
    ORDER BY driver.driver_id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateDriver($id, $data) {
    global $pdo;

    $sql = "UPDATE driver SET 
                company_id = :company_id,
                first_name = :first_name,
                last_name = :last_name,
                license_number = :license_number,
                contact_info = :contact_info
            WHERE driver_id = :driver_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':company_id' => $data['company_id'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':license_number' => $data['license_number'],
        ':contact_info' => $data['contact_info'],
        ':driver_id' => intval($id)
    ]);

    return $stmt->rowCount() > 0;
}

function deleteDriver($id) {
    global $pdo;

    $sql = "DELETE FROM driver WHERE driver_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
} 
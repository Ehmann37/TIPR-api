<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/StopModel.php';

date_default_timezone_set('Asia/Manila');

function getCurrentTime() {
    return date('Y-m-d H:i:s');
}

function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    
    $combined = $iv . $encrypted;
    
    // Base64 encode and then make URL-safe
    $base64 = base64_encode($combined);
    $urlSafe = str_replace(['+', '/', '='], ['-', '_', ''], $base64);
    
    return $urlSafe;
}

function decryptData($token, $key, $maxAgeSeconds = 30) {
    try {
        if (!is_string($token) || trim($token) === '') {
            return null;
        }

        // Convert URL-safe Base64 back to standard Base64
        $base64 = str_replace(['-', '_'], ['+', '/'], $token);

        // Add padding if needed
        $padding = strlen($base64) % 4;
        if ($padding) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $raw = base64_decode($base64, true);
        if ($raw === false) return null;

        // Check if IV + encrypted data exists
        if (strlen($raw) <= 16) return null;

        // Extract IV (first 16 bytes) and encrypted data
        $iv = substr($raw, 0, 16);
        $encrypted = substr($raw, 16);

        // Decrypt
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) return null;
        $decrypted = rtrim($decrypted, "\0");

        // Decode JSON
        $tripDetails = json_decode($decrypted, true);
        if (is_string($tripDetails)) {
            // Case: Double-encoded JSON (e.g., "{\"key\":\"value\"}")
            $tripDetails = json_decode($tripDetails, true);
        } else {
            // Case: Normal JSON (e.g., {"key":"value"})
            $tripDetails = $tripDetails;
        }

        // Check if timestamp exists and is within the allowed age
        if (isset($tripDetails['timestamp'])) {
            $currentTime = time();
            $tokenTime = strtotime($tripDetails['timestamp']); // Convert datetime string to Unix timestamp

            if ($tokenTime === false) return null; // Invalid timestamp format

            // if ($currentTime - $tokenTime > $maxAgeSeconds) return null; // token expired
        }

        return $tripDetails;
    } catch (Exception $e) {
        return null; // Catch unexpected errors
    }
}

function checkPayment($paymentId) {
    global $pdo;

    $sql = "SELECT ticket_id, payment_status FROM payment WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':payment_id' => $paymentId]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($payments) === 0) {
        return [
            'state' => 'not exist',
        ];
    }

    $ticketDetails = [];
    foreach ($payments as $row) {
        $ticket = getTicketById($row['ticket_id']);
        if ($ticket) {
            unset($ticket['bus_id']);
            unset($ticket['payment']['payment_status']);
            $ticketDetails[] = $ticket;
        }
    }

    return [
        'state' => $payments[0]['payment_status'],
        'destination_name' => getStopById($ticketDetails[0]['destination_stop_id'])['stop_name'],
        'contact_number' => $ticketDetails[0]['contact_info'] ?? null,
        'passengers' => count($ticketDetails) > 0 ? $ticketDetails : null
    ];
}

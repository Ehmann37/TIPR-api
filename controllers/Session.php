<?php
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
        $data = json_decode($decrypted, true);
        if (is_string($data)) {
            // Case: Double-encoded JSON (e.g., "{\"key\":\"value\"}")
            $data = json_decode($data, true);
        } else {
            // Case: Normal JSON (e.g., {"key":"value"})
            $data = $data;
        }

        // Check if timestamp exists and is within the allowed age
        if (isset($data['timestamp'])) {
            $currentTime = time();
            $tokenTime = strtotime($data['timestamp']); // Convert datetime string to Unix timestamp

            if ($tokenTime === false) return null; // Invalid timestamp format

            // if ($currentTime - $tokenTime > $maxAgeSeconds) return null; // token expired
        }

        return $data;
    } catch (Exception $e) {
        return null; // Catch unexpected errors
    }
}
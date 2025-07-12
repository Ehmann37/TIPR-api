<?php
function encryptData($data, $key) {
  $iv = openssl_random_pseudo_bytes(16);
  $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
  $combined = $iv . $encrypted;
  $base64 = base64_encode($combined);
  $urlSafe = str_replace(['+', '/', '='], ['-', '_', ''], $base64);
  
  return $urlSafe;
}

function decryptData($token, $key, $maxAgeSeconds = 30) {
  try {
      if (!is_string($token) || trim($token) === '') {
          return null;
      }
      $base64 = str_replace(['-', '_'], ['+', '/'], $token);

      $padding = strlen($base64) % 4;
      if ($padding) {
          $base64 .= str_repeat('=', 4 - $padding);
      }

      $raw = base64_decode($base64, true);
      if ($raw === false) return null;

      if (strlen($raw) <= 16) return null;

      $iv = substr($raw, 0, 16);
      $encrypted = substr($raw, 16);

      $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
      if ($decrypted === false) return null;
      $decrypted = rtrim($decrypted, "\0");

      $tripDetails = json_decode($decrypted, true);
      if (is_string($tripDetails)) {
          $tripDetails = json_decode($tripDetails, true);
      } else {
          $tripDetails = $tripDetails;
      }

      if (isset($tripDetails['timestamp'])) {
          $currentTime = time();
          $tokenTime = strtotime($tripDetails['timestamp']);

          if ($tokenTime === false) return null;

          // if ($currentTime - $tokenTime > $maxAgeSeconds) return null; // token expired
      }

      return $tripDetails;
  } catch (Exception $e) {
      return null;
  }
}


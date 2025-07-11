<?php

function getRequestBody(): array {
  $input = file_get_contents("php://input");
  $data = json_decode($input, true);

  if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
      respond(400, 'Invalid JSON body');
      exit;
  }

  return $data;
}

function getQueryParams(array $expectedKeys, array $exceptions = []): array {
  $params = [];

  foreach (array_keys($_GET) as $key) {
    if (!in_array($key, $expectedKeys, true) && !in_array($key, $exceptions, true)) {
        respond(400, "Invalid query parameter: '$key'");
        exit;
    }
  }

  foreach ($expectedKeys as $key) {
      $value = $_GET[$key] ?? null;
      $params[$key] = is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : $value;
  }
  return $params;
}

function sanitizeInput($data) {
  if (is_array($data)) {
      $sanitized = [];
      foreach ($data as $key => $value) {
          $sanitized[$key] = sanitizeInput($value);
      }
      return $sanitized;
  } elseif (is_string($data)) {
      return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
  } else {
      return $data;
  }
}
<?php

function buildWhereClause(array $filters, array &$params): string {
  $clauses = [];

  foreach ($filters as $column => $value) {
      if (!is_null($value)) {
          $paramKey = str_replace(['.', '(', ')'], '_', $column);
          $clauses[] = "$column = :$paramKey";
          $params[$paramKey] = $value;
      }
  }

  return $clauses ? ' AND ' . implode(' AND ', $clauses) : '';
}


function updateRecord($table, $idField, $id, $data, $allowedFields) {
  global $pdo;

  $fieldsToUpdate = [];
  $params = [":$idField" => $id];

  foreach ($allowedFields as $field) {
      if (isset($data[$field])) {
          $fieldsToUpdate[] = "$field = :$field";
          $params[":$field"] = $data[$field];
      }
  }

  if (empty($fieldsToUpdate)) {
      return false;
  }

  $sql = "UPDATE $table SET " . implode(', ', $fieldsToUpdate) . " WHERE $idField = :$idField";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);

  return $stmt->rowCount() > 0;
}

function checkExists($table, $field, $value, $extraConditions = []): bool {
  global $pdo;

  $sql = "SELECT COUNT(*) FROM $table WHERE $field = ?";
  $params = [$value];

  foreach ($extraConditions as $key => $val) {
    $sql .= " AND $key = ?";
    $params[] = $val;
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);

  return $stmt->fetchColumn() > 0;
}


function insertRecord(string $table, array $data): bool {
  global $pdo;

  if (empty($data)) return false;

  $columns = array_keys($data);
  $placeholders = array_map(fn($col) => ":$col", $columns);

  $sql = "INSERT INTO $table (" . implode(', ', $columns) . ")
          VALUES (" . implode(', ', $placeholders) . ")";

  $stmt = $pdo->prepare($sql);

  foreach ($data as $col => $value) {
      $stmt->bindValue(":$col", $value ?? null);
  }

  return $stmt->execute();
}


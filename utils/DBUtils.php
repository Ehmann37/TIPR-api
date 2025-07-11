<?php

function buildWhereClause(array $filters, array &$params): string {
  $clause = "";
  foreach ($filters as $key => $value) {
      if ($value !== null) {
          $paramKey = ":$key";
          $clause .= " AND $key = $paramKey";
          $params[$paramKey] = $value;
      }
  }
  return $clause;
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

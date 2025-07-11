<?php

function validateFields($data, $requiredFields){
  $missing = [];
  foreach ($requiredFields as $field) {
      if (!isset($data[$field])) {
          $missing[] = $field;
      }
  }
  return $missing;
}

function validateAtLeastOneField($data, $allowedFields) {
  foreach ($allowedFields as $field) {
    if (isset($data[$field])) {
      return true;
    }
  }
  return false;
}
<?php

function buildFilters(array $queryParams, array $allowedKeys): array {
  $filters = [];

  foreach ($allowedKeys as $key) {
      if (!empty($queryParams[$key])) {
          $filters[$key] = $queryParams[$key];
      }
  }

  return $filters;
}
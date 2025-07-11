<?php

function respond($code, $message, $data = null){
  http_response_code($code);
  echo json_encode([
      'status' => $code < 400 ? 'success' : 'error',
      'message' => $message,
      'data' => $data
  ]);
}




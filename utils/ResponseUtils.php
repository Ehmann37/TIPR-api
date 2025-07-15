<?php

function respond($code, $message, $data = null){
  // "01" 
  // "1"
  // "02"
  http_response_code((int)$code * 200);
  echo json_encode([
      'status' => $code === '1' ? 'success' : 'error',
      'message' => $message,
      'data' => $data
  ]);
  exit;
}




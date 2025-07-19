<?php

require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/FareModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/StopModel.php';
require_once __DIR__ . '/../models/LocationModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';


function handleGetTicket($queryParams) {
  $payment_id = $queryParams['payment_id'];
  $ticket_id = $queryParams['ticket_id'];
  $latitude = $queryParams['latitude'];
  $longitude = $queryParams['longitude'];
  $passenger_status = $queryParams['passenger_status'];
  $payment_status = $queryParams['payment_status'];


  if ($payment_id !== null) {
    if (!checkPaymentExists($payment_id)) {
      respond('01', 'Ticket not found');
      return;
    }

    $ticket = getTicketByPaymentId($payment_id);
    respond('1', 'Ticket fetched', $ticket);

  } elseif ($ticket_id !== null) {
    if (!checkTicketExists($ticket_id)) {
      respond('01', 'Ticket not found');
      return;
    }

    $ticket = getTicketByTicketId($ticket_id);
    respond('1', 'Ticket fetched', $ticket);

  } else {
    if ($latitude !== null && $longitude !== null){

      $trip_id = getActiveTrip() ?? null;
      if ($trip_id === null) {
        respond('01', 'No Active Trip Found for the Bus');
      } 

      $current_stop_id = findNearestStop($latitude, $longitude)['stop_id'] ?? null;
      if ($current_stop_id === null) {
        respond('01', 'Location Provided has no nearby stop');
      }

      $stops = [];
      $stops[] = [
        'stop_name' => getStopById($current_stop_id)['stop_name'], 
        'stop_id' => getStopById($current_stop_id)['stop_id']
      ];
      
      $stops = array_merge($stops, getStopsByTripId($trip_id, $current_stop_id));

      $data = [
        'current_stop' => findNearestStop($latitude, $longitude)['stop_name']
      ];
      foreach ($stops as $stop){
        $tickets = getTicketsByLocation($stop['stop_id'], $trip_id);

        $data['tickets_per_stop'][] = [
            'destination' => $stop['stop_name'],
            'ticket_count' => count($tickets),
            'tickets' => $tickets
        ];
        
      }

      if (count($data) === 0) {
        respond('1', 'No tickets found for the provided location');
      } else {
        respond('1', 'Tickets fetched', $data);
      }

    } else {
      $allowed = ['passenger_status', 'payment_status', 'passenger_category', 'trip_id'];
      $filters = buildFilters($queryParams, $allowed);
      $tickets = getTickets($filters);

      foreach ($tickets as &$ticket) {
        $ticket['associate_ticket'] = [
          'seat_number' => getAssociateSeatByPaymentId($ticket['payment_id'], $ticket['ticket_id']),
          'total_fare_amount' => getTotalFareByPaymentId($ticket['payment_id']),
        ];
      }

      if (count($tickets) === 0) {
        respond('1', 'No tickets found');
      } else {
        respond('1', 'Tickets fetched', $tickets);
      }
    }
  }
}

function handleCreateTicket() {
  $data = sanitizeInput(getRequestBody());

  $requiredFields = ['trip_id', 'origin_stop_id', 'destination_stop_id', 'boarding_time', 'contact_info', 'passengers', 'payment'];
  $missing = validateFields($data, $requiredFields);
  if (!empty($missing)) {
    respond('01', 'Missing required fields: ' . implode(', ', $missing));
    return;
  }

  $sharedTicketData = [
    'trip_id' => $data['trip_id'],
    'origin_stop_id' => $data['origin_stop_id'],
    'destination_stop_id' => $data['destination_stop_id'],
    'boarding_time' => $data['boarding_time'],
    'arrival_time' => $data['arrival_time'] ?? null,
    'contact_info' => $data['contact_info'],
    'payment_id' => $data['payment']['payment_id']
  ];

  $paymentFields = ['payment_id', 'payment_mode', 'payment_status'];
  $paymentData = [];

  foreach ($paymentFields as $field) {
    $paymentData[$field] = $data['payment'][$field] ?? null;
  }

  $seats = array_map(fn($p) => $p['seat_number'], $data['passengers']);
  
  $seats = array_filter($seats, fn($seat) => $seat !== 'Aisle' && $seat !== null);
  $conflictingSeats = checkSeatConflicts($seats, $data['trip_id']);

  if (!empty($conflictingSeats)) {
    respond('01', 'Occupied Seats: ' . implode(', ', $conflictingSeats));
    return;
  }

  $destinationCoordinates = getStopCoordinates($data['destination_stop_id']);
  $originCoordinates = getStopCoordinates($data['origin_stop_id']);

  $distance = findDistance(
    $originCoordinates['latitude'],
    $originCoordinates['longitude'],
    $destinationCoordinates['latitude'],
    $destinationCoordinates['longitude']
  );

  $fare_amount = calculateFare($distance)['total_fare'];

  try {
    $paymentInserted = addPayment($paymentData);
    if (!$paymentInserted) {
        respond('01', 'Failed to create payment');
        return;
    }
  } catch (Exception $e) {
    respond(500, 'Payment insertion error: ' . $e->getMessage());
    return;
  }

  $insertedTickets = [];

  foreach ($data['passengers'] as $passenger) {
    $discountedCategory = ['senior', 'student', 'pwd'];

    $discount = (in_array($passenger['passenger_category'], $discountedCategory)) ? 0.2 : 0.0;
    $ticket = array_merge($sharedTicketData, [
        'full_name' => $passenger['full_name'],
        'seat_number' => $passenger['seat_number'],
        'passenger_category' => $passenger['passenger_category'],
        'passenger_status' => 'on_bus',
        'fare_amount' => $fare_amount * (1 - $discount) 
    ]);

    try {
        $ticketId = addTicket($ticket);
        $insertedTickets[] = $ticketId;
    } catch (Exception $e) {
        respond(500, 'Ticket insertion error: ' . $e->getMessage());
        return;
    }
  }

  $numPassengers = count($data['passengers']);
  incrementTotalPassengers($data['trip_id'], $numPassengers);
  $totalFare = getTotalFareByPaymentId($paymentData['payment_id']);

  if ($paymentData['payment_status'] === "paid") {
    incrementTotalRevenue($data['trip_id'], $totalFare);
  }
  
  respond('1', 'Tickets created successfully', [
    'payment_id' => $paymentData['payment_id'],
    'ticket_ids' => $insertedTickets
  ]);
}

function updateTicketHandler($ticket_id){
  $data = sanitizeInput(getRequestBody());

  if ($ticket_id === null) {
    if (empty($data)) {
      respond('01', 'Missing ticket_id/s');
      return;
    }
    
    $unfound_ticket = [];
    foreach ($data as $id){
      if (!checkTicketExists($id)) {
        $unfound_ticket[] = $id;
      }
    }

    if (!empty($unfound_ticket)) {
      respond('01', 'Ticket not found: ' . implode(', ', $unfound_ticket));
      return;
    }

    foreach ($data as $id){
      $update = updateTicket($id, ['passenger_status' => 'left_bus'], ['passenger_status']);  
    } 
    
  } else {

    $ticketData = getTicketByTicketId($ticket_id);

    $ticket_info = [
      'fare_amount' => $ticketData['fare_amount'], 
      'passenger_category' => $ticketData['passenger_category']
    ];
    
    if (!checkTicketExists($ticket_id)) {
      respond('01', 'Ticket not found');
      return;
    }
  
    $trip_id = getActiveTrip();
    $payment_id = getTicketByTicketId($ticket_id)['payment_id'];
    $totalFare = getTotalFareByPaymentId($payment_id);
  
  
    $allowed = ['passenger_category', 'seat_number','payment_status', 'fare_amount'];
    
    if (!validateAtLeastOneField($data, $allowed)) {
      respond('01', 'No valid fields provided for update');
      return;
    }
  
    if ($data['payment_status'] !== null) {
      $payment = updatePayment($ticket_id, ['payment_status' => $data['payment_status']], $allowed);
      if (!$payment) {
        respond('01', 'Payment_Status not changed');
        exit;
      }
      incrementTotalRevenue($trip_id, $totalFare);
  
    } 
    unset($data['payment_status']);
  
    if ($data['passenger_category'] != null || $data['seat_number'] != null) {
  
      if ($data['seat_number'] !== null && $data['seat_number'] !== 'Aisle') {
        $seats = (array) $data['seat_number'];
  
        $conflictingSeats = checkSeatConflicts($seats, $trip_id);
        if (!empty($conflictingSeats)) {
          respond('01', 'Occupied Seats: ' . implode(', ', $conflictingSeats));
        }
      }
  
      if ($data['passenger_category'] !== null) {
        $state_1 = ['regular'];
        $state_2 = ['senior', 'student', 'pwd'];
        if (in_array($data['passenger_category'], $state_1) && in_array($ticket_info['passenger_category'], $state_2)) {
          $data['fare_amount'] = $ticket_info['fare_amount'] * 1.25;
        } elseif (in_array($data['passenger_category'], $state_2) && in_array($ticket_info['passenger_category'], $state_1)) {
          $data['fare_amount'] = $ticket_info['fare_amount'] * 0.8;
        } else {
          $data['fare_amount'] = $ticket_info['fare_amount'];
        }
      }                                                                             
      $ticket = updateTicket($ticket_id, $data, $allowed);
      if (!$ticket) {
        respond('01', 'Ticket not updated');
        return;
      }
    }
  }

  

  respond ('1', 'Ticket updated successfully');
}
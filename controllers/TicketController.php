<?php

require_once __DIR__ . '/../models/TicketModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/TripModel.php';
require_once __DIR__ . '/../models/BusModel.php';
require_once __DIR__ . '/../models/StopModel.php';
require_once __DIR__ . '/../utils/RequestUtils.php';
require_once __DIR__ . '/../utils/QueryUtils.php';
require_once __DIR__ . '/../utils/ValidationUtils.php';
require_once __DIR__ . '/../utils/ResponseUtils.php';


function handleGetTicket($queryParams) {
  $payment_id = $queryParams['payment_id'];
  $ticket_id = $queryParams['ticket_id'];
  $latitude = $queryParams['latitude'];
  $longitude = $queryParams['longitude'];
  $bus_id = $queryParams['bus_id'];
  $passenger_status = $queryParams['passenger_status'];
  $payment_status = $queryParams['payment_status'];


  if ($payment_id !== null) {
    if (!checkPaymentExists($payment_id)) {
      respond(404, 'Ticket not found');
      return;
    }

    $ticket = getTicketByPaymentId($payment_id);
    respond(200, 'Ticket fetched', $ticket);

  } elseif ($ticket_id !== null) {
    if (!checkTicketExists($ticket_id)) {
      respond(404, 'Ticket not found');
      return;
    }

    $ticket = getTicketByTicketId($ticket_id);
    respond(200, 'Ticket fetched', $ticket);

  } else {
    if ($latitude !== null && $longitude !== null){
      if ($bus_id === null) {
        respond(400, 'Bus ID is required when latitude and longitude are provided');
      }

      if (!checkBusExists($bus_id)) {
        respond(404, 'Bus not found');
        return;
      }

      $trip_id = getActiveTrip($bus_id) ?? null;
      if ($trip_id === null) {
        respond(404, 'No Active Trip Found for the Bus');
      } 

      $current_stop_id = findNearestStop($latitude, $longitude)['stop_id'] ?? null;
      if ($current_stop_id === null) {
        respond(404, 'Location Provided has no nearby stop');
      }

      $stops = getStopsByBusId($bus_id, $current_stop_id);

      $data = [];
      foreach ($stops as $stop){
        $tickets = getTicketsByLocation($stop['stop_id'], $trip_id);

        $data[] = [
            'destination' => $stop['stop_name'],
            'ticket_count' => count($tickets),
            'tickets' => $tickets
        ];
      }

      if (count($data) === 0) {
        respond(404, 'No tickets found for the provided location');
      } else {
        respond(200, 'Tickets fetched', $data);
      }

    } else {
      $allowed = ['bus_id', 'passenger_status', 'payment_status', 'passenger_category'];
      $filters = buildFilters($queryParams, $allowed);
      $tickets = getTickets($filters);
      if (count($tickets) === 0) {
        respond(404, 'No tickets found');
      } else {
        respond(200, 'Tickets fetched', $tickets);
      }
    }
  }
}

function handleCreateTicket() {
  $data = sanitizeInput(getRequestBody());

  $requiredFields = ['bus_id', 'origin_stop_id', 'destination_stop_id', 'full_name', 'seat_number', 'passenger_category', 'passenger_status', 'boarding_time', 'trip_id'];

  $missing = validateFields($data, $requiredFields);
  if (!empty($missing)) {
    respond(400, 'Missing required fields: ' . implode(', ', $missing));
    return;
  }


}

function updateTicketHandler($ticket_id){
  if ($ticket_id === null) {
    respond(400, 'Missing ticket ID');
    return;
  }

  $data = sanitizeInput(getRequestBody());

  $allowed = ['passenger_category', 'seat_number', 'payment_status'];
  if (!validateAtLeastOneField($data, $allowed)) {
    respond(400, 'No valid fields provided for update');
    return;
  }

  if (isset($data['payment_status'])) {
    $payment = updatePayment($ticket_id, $data, $allowed);
    if ($payment) {
      respond (200, 'Payment updated successfully');
    } else {
      respond(404, 'Payment not found or no changes made');
      return;
    }
  } else {
    $ticket = updateTicket($ticket_id, $data, $allowed);
    if ($ticket) {
      respond(200, 'Ticket updated successfully');
    } else {
      respond(404, 'Ticket not found or no changes made');
      return;
    }
  }
}
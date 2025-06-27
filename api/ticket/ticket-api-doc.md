# Ticket API Documentation

This document outlines the endpoints for managing tickets within the TRIP API system.

**Base URL:** `http://your-domain/api/ticket/index.php`
**Authentication:** trip123api
**Content-Type:** Application/JSON




## Important Endpoints
- **GET** `/ticket/index.php`
- **GET** `/ticket/index.php?id={id}` 
- **GET** `/ticket/index.php?busID={busId}`
- **GET** `/ticket/index.php?boardingStatus={on_bus/left/bus}`
- **GET** `/ticket/index.php?paymentStatus={paid/pending}`
- **REMINDER:** You can combine the params except the 'id', 'id' should be alone.


### Get All Tickets
- **GET** `/ticket/index.php`
- **Description:** Retrieve all tickets with passenger and stop information
- **1st Response (without payment):**
```json
{
  "status": "success",
  "data": [
    {
      "ticket_id": 1,
      "bus_id": 1,
      "origin_stop_id": 1,
      "destination_stop_id": 2,
      "first_name": "John",
      "last_name": "Doe",
      "seat_number": "15",
      "passenger_category": "regular",
      "boarding_time": "2024-01-15 08:00:00",
      "arrival_time": "2024-01-15 09:30:00",
      "ticket_timestamp": "2024-01-15 07:45:00",
    }
  ]
}
```
- **2nd Response (with payment):**

```JSON
{
    "status": "success",
    "data": {
        "ticket_id": 2,
        "bus_id": 1,
        "origin_stop_id": 9,
        "destination_stop_id": 2,
        "first_name": "Doe",
        "last_name": "John",
        "seat_number": "15",
        "passenger_category": "regular",
        "passenger_status": "took_off",
        "boarding_time": "2024-01-15 08:00:00",
        "arrival_time": "2024-01-15 09:30:00",
        "ticket_timestamp": "2025-06-23 14:15:57",
        "origin_stop": "Boljoon",
        "destination_stop": "Minglanilla",
        "payment_id": 2,
        "payment_mode": "cash",
        "payment_platform": "terminal",
        "fare_amount": "150.00",
        "payment_timestamp": "2025-06-23 14:15:57"
    }
}
```

### Get Ticket by ticket ID
- **GET** `/ticket/index.php?id=1`
- **Description:** Retrieve a specific ticket.
- **Response:**
```json
{
    "boarding_status": "success",
    "data": {
        "ticket_id": 1,
        "bus_id": 1,
        "origin_stop_id": 9,
        "destination_stop_id": 2,
        "first_name": "Doe",
        "last_name": "John",
        "seat_number": "15",
        "passenger_category": "regular",
        "boarding_time": "2024-01-15 08:00:00",
        "arrival_time": "2024-01-15 09:30:00",
        "ticket_timestamp": "2025-06-23 14:15:57",
        "boarding_status": "on_bus",
        "payment": {
            "payment_id": 1,
            "payment_mode": "cash",
            "payment_platform": "terminal",
            "fare_amount": "150.00",
            "payment_timestamp": "2025-06-23 14:15:57",
            "payment_status": "paid"
        }
    }
}
```

### Get Ticket by bus ID
- **GET** `/ticket/index.php?busId=2`
- **Description:** Retrieve a specific ticket.
- **Response:**
```json
[
  {
    "boarding_status": "success",
    "data": [
        {
            "ticket_id": 19,
            "bus_id": 2,
            "origin_stop_id": 4,
            "destination_stop_id": 1,
            "first_name": "Charlotte",
            "last_name": "Scott",
            "seat_number": "11",
            "passenger_category": "pwd",
            "boarding_time": "2024-01-15 11:45:00",
            "arrival_time": "2024-01-15 13:30:00",
            "ticket_timestamp": "2025-06-24 14:34:57",
            "boarding_status": "on_bus",
            "payment": {
                "payment_id": 20,
                "payment_mode": "online",
                "payment_platform": "website",
                "fare_amount": "65.00",
                "payment_timestamp": "2025-06-23 19:57:22",
                "payment_status": "paid"
            }
        }
    ]
  }
]
```

### Get Ticket by Boarding Status
- **GET** `/ticket/index.php?boardingStatus=left_bus`
- **Description:** Retrieve a specific ticket.
- **Response:**
```json
[
  {
    "boarding_status": "success",
    "data": [
        {
            "ticket_id": 19,
            "bus_id": 12,
            "origin_stop_id": 4,
            "destination_stop_id": 1,
            "first_name": "Charlotte",
            "last_name": "Scott",
            "seat_number": "11",
            "passenger_category": "pwd",
            "boarding_time": "2024-01-15 11:45:00",
            "arrival_time": "2024-01-15 13:30:00",
            "ticket_timestamp": "2025-06-24 14:34:57",
            "boarding_status": "left_bus",
            "payment": {
                "payment_id": 20,
                "payment_mode": "online",
                "payment_platform": "website",
                "fare_amount": "65.00",
                "payment_timestamp": "2025-06-23 19:57:22",
                "payment_status": "paid"
            }
        }
    ]
  }
]
```

### Get Ticket by Payment Status
- **GET** `/ticket/index.php?paymentStatus=pending`
- **Description:** Retrieve a specific ticket.
- **Response:**
```json
[
  {
    "boarding_status": "success",
    "data": [
        {
            "ticket_id": 19,
            "bus_id": 12,
            "origin_stop_id": 4,
            "destination_stop_id": 1,
            "first_name": "Charlotte",
            "last_name": "Scott",
            "seat_number": "11",
            "passenger_category": "pwd",
            "boarding_time": "2024-01-15 11:45:00",
            "arrival_time": "2024-01-15 13:30:00",
            "ticket_timestamp": "2025-06-24 14:34:57",
            "boarding_status": "left_bus",
            "payment": {
                "payment_id": 20,
                "payment_mode": "online",
                "payment_platform": "website",
                "fare_amount": "65.00",
                "payment_timestamp": "2025-06-23 19:57:22",
                "payment_status": "pending"
            }
        }
    ]
  }
]
```

### Create Ticket Only
- **POST** `/ticket/`
- **Description:** Create a new ticket without an associated payment.
- **Request Body:**
```json
{
  "bus_id": 1,
  "origin_stop_id": 1,
  "destination_stop_id": 2,
  "first_name": "John",
  "last_name": "Doe",
  "seat_number": 15,
  "passenger_category": "regular",
  "boarding_time": "2024-01-15 08:00:00",
  "arrival_time": "2024-01-15 09:30:00"
}
```
- **Response:**
```json
{
  "status": "success",
  "ticket_id": 1
}
```

### Create Ticket with Payment
- **POST** `/ticket/`
- **Description:** Create a new ticket and its associated payment in a single transaction.
- **Request Body:**
```json
{
  "bus_id": 1,
  "origin_stop_id": 1,
  "destination_stop_id": 2,
  "first_name": "John",
  "last_name": "Doe",
  "seat_number": 15,
  "passenger_category": "regular",
  "boarding_time": "2024-01-15 08:00:00",
  "arrival_time": "2024-01-15 09:30:00",
  "payment": {
    "payment_mode": "cash",
    "payment_platform": "terminal",
    "fare_amount": 150.00,
    "payment_status": "paid"
  }
}
```
- **Response:**
```json
{
  "status": "success",
  "data": {
    "ticket_id": 1,
    "payment_id": 1
  }
}
```

### Update Ticket
- **PUT** `/ticket/{id}`
- **Description:** Update an existing ticket. The request body is the same as for creating a ticket only.
- **Response:**
```json
{
  "status": "success",
  "message": "Ticket updated"
}
```

### Delete Ticket
- **DELETE** `/ticket/{id}`
- **Description:** Delete a ticket and its associated payment.
- **Response:**
```json
{
  "status": "success",
  "message": "Ticket deleted"
}
```

## How to Check a Ticket's Payment
To find out if a ticket has been paid, you can:
1.  **Fetch the full ticket details** using `GET /api/ticket/{id}`. The payment information will be included in the response if it exists.
2.  **Use the payment API** at `GET /api/payment/?ticket_id={ticket_id}`.

## Data Types
### Passenger Categories
- `regular`: Regular passenger
- `student`: Student with ID
- `senior`: Senior citizen
- `pwd`: Person with disability

## Error Responses
- **400 Bad Request**: Missing a required field.
- **401 Unauthorized**: Invalid or missing authentication token.
- **404 Not Found**: The requested ticket does not exist.
- **405 Method Not Allowed**: Using an incorrect HTTP method.
- **500 Internal Server Error**: A server-side error occurred. 
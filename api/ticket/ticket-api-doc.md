# Ticket API Documentation

This document outlines the endpoints for managing tickets within the TRIP API system.

**Base URL:** `http://your-domain/api/`
**Authentication:** Bearer Token required in Authorization header
**Token:** `trip123api`

## Authentication
Include the following header in all requests:
```
Authorization: Bearer trip123api
```

## Endpoints

### Get All Tickets
- **GET** `/ticket/`
- **Description:** Retrieve all tickets with passenger and stop information
- **Response:**
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
      "seat_number": 15,
      "passenger_category": "regular",
      "boarding_time": "2024-01-15 08:00:00",
      "arrival_time": "2024-01-15 09:30:00",
      "ticket_timestamp": "2024-01-15 07:45:00",
      "origin_stop": "Terminal 1",
      "destination_stop": "Terminal 2"
    }
  ]
}
```

### Get Ticket by ID
- **GET** `/ticket/{id}`
- **Description:** Retrieve a specific ticket, including its payment information.
- **Response:**
```json
{
  "status": "success",
  "data": {
    "ticket_id": 1,
    "bus_id": 1,
    "origin_stop_id": 1,
    "destination_stop_id": 2,
    "first_name": "John",
    "last_name": "Doe",
    "seat_number": 15,
    "passenger_category": "regular",
    "boarding_time": "2024-01-15 08:00:00",
    "arrival_time": "2024-01-15 09:30:00",
    "ticket_timestamp": "2024-01-15 07:45:00",
    "origin_stop": "Terminal 1",
    "destination_stop": "Terminal 2",
    "payment_id": 1,
    "payment_mode": "cash",
    "payment_platform": "terminal",
    "fare_amount": "150.00",
    "payment_timestamp": "2024-01-15 07:45:00"
  }
}
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
    "fare_amount": 150.00
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
# Payment API Documentation

This document outlines the endpoints for managing payments within the TRIP API system.

**Base URL:** `http://your-domain/api/`
**Authentication:** Bearer Token required in Authorization header
**Token:** `trip123api`

## Authentication
Include the following header in all requests:
```
Authorization: Bearer trip123api
```

## Endpoints

### Get All Payments
- **GET** `/payment/`
- **Description:** Retrieve all payments with basic passenger information.
- **Response:**
```json
{
  "status": "success",
  "data": [
    {
      "payment_id": 1,
      "ticket_id": 1,
      "payment_mode": "cash",
      "payment_platform": "terminal",
      "fare_amount": "150.00",
      "payment_timestamp": "2024-01-15 07:45:00",
      "first_name": "John",
      "last_name": "Doe",
      "seat_number": 15
    }
  ]
}
```

### Get Payment by ID
- **GET** `/payment/{id}`
- **Description:** Retrieve a specific payment. The response is a single object with the same structure as above.

### Get Payment by Ticket ID
- **GET** `/payment/?ticket_id={ticket_id}`
- **Description:** Retrieve the payment information for a specific ticket. The response is a single object with the same structure as above.

### Create Payment
- **POST** `/payment/`
- **Description:** Create a new payment for an existing ticket. This should only be used if a ticket was created without a payment initially.
- **Request Body:**
```json
{
  "ticket_id": 1,
  "payment_mode": "cash",
  "payment_platform": "terminal",
  "fare_amount": 150.00
}
```
- **Response:**
```json
{
  "status": "success",
  "payment_id": 1
}
```

### Update Payment
- **PUT** `/payment/{id}`
- **Description:** Update an existing payment. The request body is the same as for creating a payment.
- **Response:**
```json
{
  "status": "success",
  "message": "Payment updated"
}
```

### Delete Payment
- **DELETE** `/payment/{id}`
- **Description:** Delete a payment. Note: this does not delete the associated ticket.
- **Response:**
```json
{
  "status": "success",
  "message": "Payment deleted"
}
```

## Data Types
### Payment Modes
- `cash`: Cash payment
- `card`: Credit/Debit card
- `GCash`: GCash mobile payment

### Payment Platforms
- `terminal`: Payment terminal
- `mobile app`: Mobile application

## Error Responses
- **400 Bad Request**: Missing a required field.
- **401 Unauthorized**: Invalid or missing authentication token.
- **404 Not Found**: The requested payment does not exist.
- **405 Method Not Allowed**: Using an incorrect HTTP method.
- **500 Internal Server Error**: A server-side error occurred. 
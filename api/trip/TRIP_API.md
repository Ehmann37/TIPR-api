# Trip API Documentation

## Overview

The Trip API is used to update the status of a trip associated with a specific bus. This API supports the `PUT` HTTP method and expects a JSON request body.

The endpoint for updating trip status is:

```
PUT /api/trip/index.php
```

## Request

### Headers

- `Authorization: trip123api`
- `Content-Type: application/json`

### JSON Body Parameters

| Parameter | Type   | Description                          |
|-----------|--------|--------------------------------------|
| `bus_id`  | int    | The unique identifier of the bus     |
| `status`  | string | The new status of the trip. Options: `"active"` or `"complete"` |

### Example Request

```json
{
  "bus_id": 1,
  "status": "complete"
}
```

## Example Use Case

1. **Start of a trip**:
   - The Trip API is called using the `PUT` method.
   - The bus ID is sent along with `"status": "active"` to start a new trip.

2. **During trip**:
   - Session API is called with bus ID and coordinates.
   - Trip is created and its ID is encrypted into a token.
   - It is then decypted and all information along with the trip id is sent to front end.

2. **End of the trip**:
   - The Trip API is called using the `PUT` method.
   - The bus ID is sent along with `"status": "complete"` to mark the trip as finished.

---



## Response

A successful response typically returns a JSON object with a success message or status code. The exact format may vary based on your backend implementation.

---

## Important Notes

### Relationship with Session API

When a session is created using the Session API by providing:

- `bus_id`
- GPS `latitude` and `longitude` (coordinates)

The backend **encrypts the generated Trip ID** and stores it as a **token**. This token is used to identify and secure the trip session.

This design ensures that:

- The client never directly sees or manipulates the raw trip ID.
- The token can later be decrypted to retrieve the trip ID internally for operations such as updating the trip status via the Trip API.

---

## Status Reference

| Status Value | Meaning                                       |
|--------------|-----------------------------------------------|
| `active`     | It creates new trip for the bus               |
| `complete`   | It ends or Completes the active trip of the bus|

---

## Security Note

Ensure that only authenticated and authorized users or systems can access this endpoint, especially since it modifies data.

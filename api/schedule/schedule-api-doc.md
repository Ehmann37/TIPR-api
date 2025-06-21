# 🚌 Schedule API Documentation

> This API handles all operations related to trip schedules, following RESTful principles. It supports creating, retrieving, updating, and deleting schedules.

---

## 🔐 Authentication

All endpoints require an `Authorization` header.

**Headers**
```
Authorization: trip123api
Content-Type: application/json
```

---

## 📦 Data Model

| Field           | Type    | Description                                       |
|-----------------|---------|---------------------------------------------------|
| `schedule_id`   | Integer | Unique ID of the schedule (auto-generated, read-only) |
| `first_trip`    | String  | First trip time (e.g., `03:00 AM` or `15:00`).     |
| `last_trip`     | String  | Last trip time (e.g., `07:00 PM` or `19:00`).      |
| `time_interval` | Integer | Time gap (in minutes) between each trip.          |

---

## 📌 Endpoints

The base URL for these endpoints is `.../api`. For example: `https://trip-api.dcism.org/api`.

### 1. Get All Schedules

Retrieves a list of all schedules.

- **Endpoint:** `/schedule`
- **Method:** `GET`

#### Response:
```json
{
  "status": "success",
  "data": [
    {
      "schedule_id": 1,
      "first_trip": "03:00:00",
      "last_trip": "19:00:00",
      "time_interval": 30
    },
    {
      "schedule_id": 2,
      "first_trip": "04:00:00",
      "last_trip": "20:00:00",
      "time_interval": 20
    }
  ]
}
```

---

### 2. Get Schedule by ID

Retrieves a single schedule by its unique ID.

- **Endpoint:** `/schedule/{id}`
- **Method:** `GET`

**Example URL:** `/schedule/1`

#### Response (Success):
```json
{
  "status": "success",
  "data": {
    "schedule_id": 1,
    "first_trip": "03:00:00",
    "last_trip": "19:00:00",
    "time_interval": 30
  }
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Schedule not found"
}
```

---

### 3. Add a New Schedule

Creates a new schedule. The `first_trip` and `last_trip` times will be converted to 24-hour format.

- **Endpoint:** `/schedule`
- **Method:** `POST`

#### Request Body:
```json
{
  "first_trip": "3:00 AM",
  "last_trip": "7:00 PM",
  "time_interval": 30
}
```

#### Response (Success):
```json
{
  "status": "success",
  "schedule_id": 3
}
```

---

### 4. Update a Schedule

Updates an existing schedule by its ID.

- **Endpoint:** `/schedule/{id}`
- **Method:** `PUT`

**Example URL:** `/schedule/1`

#### Request Body:
```json
{
  "first_trip": "4:00 AM",
  "last_trip": "6:00 PM",
  "time_interval": 20
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Schedule updated"
}
```

#### Response (Error - Not Found or No Changes):
```json
{
    "status": "error",
    "message": "Schedule not found or no changes made"
}
```

---

### 5. Delete a Schedule

Deletes a schedule by its ID.

- **Endpoint:** `/schedule/{id}`
- **Method:** `DELETE`

**Example URL:** `/schedule/1`

#### Response (Success):
```json
{
  "status": "success",
  "message": "Schedule deleted"
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Schedule not found"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using tools like **Postman**.
- Remember to set the **Authorization** header and use **raw JSON** for request bodies.

---

## 📌 Notes

- `first_trip` and `last_trip` can be in `HH:MM AM/PM` format and will be automatically converted to the `HH:MM:SS` 24-hour format.
- `time_interval` must be an integer (representing minutes).
- All responses are in **JSON** format.
- The `schedule_id` is passed via the URL for GET (single), PUT, and DELETE requests, not in the request body or query parameters.
# 🛑 Stop API Documentation

> This API handles all operations related to trip stops, following RESTful principles. It supports creating, retrieving, updating, and deleting stops.

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

| Field       | Type    | Description                                         |
|-------------|---------|-----------------------------------------------------|
| `stop_id`   | Integer | Unique ID of the stop (auto-generated, read-only)   |
| `stop_name` | String  | The name of the bus stop or terminal.               |
| `longitude` | Float   | The longitude coordinate of the stop.               |
| `latitude`  | Float   | The latitude coordinate of the stop.                |

---

## 📌 Endpoints

The base URL for these endpoints is `.../api`. For example: `https://trip-api.dcism.org/api`.

### 1. Get All Stops

Retrieves a list of all stops.

- **Endpoint:** `/stop`
- **Method:** `GET`

#### Response:
```json
{
  "status": "success",
  "data": [
    {
      "stop_id": 1,
      "stop_name": "CSBT",
      "longitude": 123.89316865374035,
      "latitude": 10.298358793808859
    },
    {
      "stop_id": 2,
      "stop_name": "Ayala Center Cebu",
      "longitude": 123.905922,
      "latitude": 10.317586
    }
  ]
}
```

---

### 2. Get Stop by ID

Retrieves a single stop by its unique ID.

- **Endpoint:** `/stop/{id}`
- **Method:** `GET`

**Example URL:** `/stop/1`

#### Response (Success):
```json
{
  "status": "success",
  "data": {
    "stop_id": 1,
    "stop_name": "CSBT",
    "longitude": 123.89316865374035,
    "latitude": 10.298358793808859
  }
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Stop not found"
}
```

---

### 3. Add a New Stop

Creates a new stop.

- **Endpoint:** `/stop`
- **Method:** `POST`

#### Request Body:
```json
{
  "stop_name": "New Terminal",
  "longitude": 123.900000,
  "latitude": 10.300000
}
```

#### Response (Success):
```json
{
  "status": "success",
  "stop_id": 3
}
```

---

### 4. Update a Stop

Updates an existing stop by its ID.

- **Endpoint:** `/stop/{id}`
- **Method:** `PUT`

**Example URL:** `/stop/1`

#### Request Body:
```json
{
  "stop_name": "Updated Terminal Name",
  "longitude": 123.911111,
  "latitude": 10.311111
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Stop updated"
}
```

#### Response (Error - Not Found or No Changes):
```json
{
    "status": "error",
    "message": "Stop not found or no changes made"
}
```

---

### 5. Delete a Stop

Deletes a stop by its ID.

- **Endpoint:** `/stop/{id}`
- **Method:** `DELETE`

**Example URL:** `/stop/1`

#### Response (Success):
```json
{
  "status": "success",
  "message": "Stop deleted"
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Stop not found"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using tools like **Postman**.
- Remember to set the **Authorization** header and use **raw JSON** for request bodies.

---

## 📌 Notes

- The `stop_id` is passed via the URL for GET (single), PUT, and DELETE requests, not in the request body or query parameters.
- Longitude and Latitude should be valid floating-point numbers.
- All responses are in **JSON** format.
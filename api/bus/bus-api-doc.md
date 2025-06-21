# 🚌 Bus API Documentation

> This API manages bus data in the TRIP system, following RESTful principles. It supports creating, retrieving, updating, and deleting bus records.

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

| Field           | Type    | Description                                    |
|-----------------|---------|------------------------------------------------|
| `bus_id`        | Integer | Unique ID of the bus (auto-generated, read-only) |
| `route_id`      | Integer | Foreign key referencing the bus route.         |
| `company_id`    | Integer | Foreign key referencing the bus company.       |
| `bus_driver_id` | Integer | Foreign key referencing the bus driver.        |
| `status`        | String  | Current operational status of the bus.         |

**Status Values:**
- `active`: Bus is currently in service
- `inactive`: Bus is temporarily out of service
- `maintenance`: Bus is under maintenance
- `retired`: Bus is permanently out of service

---

## 📌 Endpoints

The base URL for these endpoints is `.../api`. For example: `https://trip-api.dcism.org/api`.

### 1. Get All Buses

Retrieves a list of all buses in the system.

- **Endpoint:** `/bus`
- **Method:** `GET`

#### Response:
```json
{
  "status": "success",
  "data": [
    {
      "bus_id": 1,
      "route_id": 101,
      "company_id": 5,
      "bus_driver_id": 42,
      "status": "active"
    },
    {
      "bus_id": 2,
      "route_id": 102,
      "company_id": 5,
      "bus_driver_id": 43,
      "status": "maintenance"
    }
  ]
}
```

---

### 2. Get Bus by ID

Retrieves a single bus by its unique ID.

- **Endpoint:** `/bus/{id}`
- **Method:** `GET`

**Example URL:** `/bus/1`

#### Response (Success):
```json
{
  "status": "success",
  "data": {
    "bus_id": 1,
    "route_id": 101,
    "company_id": 5,
    "bus_driver_id": 42,
    "status": "active"
  }
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Bus not found"
}
```

---

### 3. Add a New Bus

Creates a new bus record.

- **Endpoint:** `/bus`
- **Method:** `POST`

#### Request Body:
```json
{
  "route_id": 101,
  "company_id": 5,
  "bus_driver_id": 42,
  "status": "active"
}
```

#### Response (Success):
```json
{
  "status": "success",
  "bus_id": 3
}
```

---

### 4. Update a Bus

Updates an existing bus by its ID.

- **Endpoint:** `/bus/{id}`
- **Method:** `PUT`

**Example URL:** `/bus/1`

#### Request Body:
```json
{
  "route_id": 101,
  "company_id": 5,
  "bus_driver_id": 42,
  "status": "inactive"
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Bus updated"
}
```

#### Response (Error - Not Found or No Changes):
```json
{
    "status": "error",
    "message": "Bus not found or no changes made"
}
```

---

### 5. Delete a Bus

Deletes a bus by its ID.

- **Endpoint:** `/bus/{id}`
- **Method:** `DELETE`

**Example URL:** `/bus/1`

#### Response (Success):
```json
{
  "status": "success",
  "message": "Bus deleted"
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Bus not found"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using tools like **Postman**.
- Remember to set the **Authorization** header and use **raw JSON** for request bodies.

---

## 📌 Notes

- The `bus_id` is passed via the URL for GET (single), PUT, and DELETE requests, not in the request body or query parameters.
- `route_id`, `company_id`, and `bus_driver_id` must correspond to existing entities in the system.
- All responses are in **JSON** format.
- When a bus is assigned to maintenance, it will not be available for scheduling. 
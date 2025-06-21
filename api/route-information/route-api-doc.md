# 🚍 Route Information API Documentation

> This API handles all operations related to route information, following RESTful principles. It supports creating, retrieving, updating, and deleting routes.

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

| Field         | Type    | Description                                           |
|---------------|---------|-------------------------------------------------------|
| `route_id`    | Integer | Unique ID of the route (auto-generated, read-only)    |
| `route_name`  | String  | The name of the route (e.g., "City Circle Line").     |
| `schedule_id` | Integer | The foreign key linking to a schedule.                |

---

## 📌 Endpoints

The base URL for these endpoints is `.../api`. For example: `https://trip-api.dcism.org/api`.

### 1. Get All Routes

Retrieves a list of all routes.

- **Endpoint:** `/route-information`
- **Method:** `GET`

#### Response:
```json
{
  "status": "success",
  "data": [
    {
      "route_id": 1,
      "route_name": "City Circle Line",
      "schedule_id": 1
    },
    {
      "route_id": 2,
      "route_name": "Express East Line",
      "schedule_id": 2
    }
  ]
}
```

---

### 2. Get Route by ID

Retrieves a single route by its unique ID.

- **Endpoint:** `/route-information/{id}`
- **Method:** `GET`

**Example URL:** `/route-information/1`

#### Response (Success):
```json
{
  "status": "success",
  "data": {
    "route_id": 1,
    "route_name": "City Circle Line",
    "schedule_id": 1
  }
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Route information not found"
}
```

---

### 3. Add a New Route

Creates a new route.

- **Endpoint:** `/route-information`
- **Method:** `POST`

#### Request Body:
```json
{
  "route_name": "New Downtown Route",
  "schedule_id": 3
}
```

#### Response (Success):
```json
{
  "status": "success",
  "route_id": 3
}
```

---

### 4. Update a Route

Updates an existing route by its ID.

- **Endpoint:** `/route-information/{id}`
- **Method:** `PUT`

**Example URL:** `/route-information/1`

#### Request Body:
```json
{
  "route_name": "Updated Downtown Route",
  "schedule_id": 4
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Route information updated"
}
```

#### Response (Error - Not Found or No Changes):
```json
{
    "status": "error",
    "message": "Route information not found or no changes made"
}
```

---

### 5. Delete a Route

Deletes a route by its ID.

- **Endpoint:** `/route-information/{id}`
- **Method:** `DELETE`

**Example URL:** `/route-information/1`

#### Response (Success):
```json
{
  "status": "success",
  "message": "Route information deleted"
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Route information not found"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using tools like **Postman**.
- Remember to set the **Authorization** header and use **raw JSON** for request bodies.

---

## 📌 Notes

- The `route_id` is passed via the URL for GET (single), PUT, and DELETE requests, not in the request body or query parameters.
- `schedule_id` must correspond to an existing ID in the `schedule` table.
- All responses are in **JSON** format.
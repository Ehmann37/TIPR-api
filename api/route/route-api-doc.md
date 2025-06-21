# 🚌 Route API Documentation

> This API manages the relationship between routes and stops. It uses a composite key (`route_id`, `stop_id`) to define the specific sequence of stops for each route.

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

| Field        | Type    | Description                                            |
|--------------|---------|--------------------------------------------------------|
| `route_id`   | Integer | Foreign key referencing `route_information`.           |
| `stop_id`    | Integer | Foreign key referencing `stop`.                        |
| `stop_order` | Integer | The order of the stop within the specified route.      |

---

## 📌 Endpoints

The base URL for these endpoints is `.../api`. For example: `https://trip-api.dcism.org/api`.

### 1. Get All Route-Stop Mappings

Retrieves a list of all route-stop assignments from the database.

- **Endpoint:** `/route`
- **Method:** `GET`

#### Response:
```json
{
  "status": "success",
  "data": [
    { "route_id": 1, "stop_id": 2, "stop_order": 1 },
    { "route_id": 1, "stop_id": 3, "stop_order": 2 },
    { "route_id": 2, "stop_id": 4, "stop_order": 1 }
  ]
}
```

---

### 2. Get Stops for a Specific Route

Retrieves all the stops assigned to a single route, ordered by `stop_order`.

- **Endpoint:** `/route/{id}`
- **Method:** `GET`

**Example URL:** `/route/1`

#### Response (Success):
```json
{
  "status": "success",
  "data": [
    { "route_id": 1, "stop_id": 2, "stop_order": 1 },
    { "route_id": 1, "stop_id": 3, "stop_order": 2 }
  ]
}
```

---

### 3. Add a Stop to a Route

Assigns a stop to a route with a specific order.

- **Endpoint:** `/route`
- **Method:** `POST`

#### Request Body:
```json
{
  "route_id": 1,
  "stop_id": 4,
  "stop_order": 3
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Route stop added successfully"
}
```

---

### 4. Update a Stop in a Route

Updates the `stop_order` of an existing stop within a route.

- **Endpoint:** `/route/{id}`
- **Method:** `PUT`

**Example URL:** `/route/1`

#### Request Body:
```json
{
  "stop_id": 4,
  "stop_order": 2
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Route stop updated"
}
```

---

### 5. Delete Stops from a Route

This endpoint has two modes for deleting stops from a route.

#### A. Delete a Specific Stop from a Route

- **Endpoint:** `/route/{id}/stops/{stop_id}`
- **Method:** `DELETE`

**Example URL:** `/route/1/stops/4`

#### Response (Success):
```json
{
  "status": "success",
  "message": "Route stop deleted"
}
```

#### B. Delete All Stops from a Route

- **Endpoint:** `/route/{id}`
- **Method:** `DELETE`

**Example URL:** `/route/1`

#### Response (Success):
```json
{
  "status": "success",
  "message": "All stops for the route have been deleted"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using tools like **Postman**.
- Remember to set the **Authorization** header and use **raw JSON** for request bodies.

---

## 📌 Notes
- This API manages the `route` table, which serves as a link between `route_information` and `stop`.
- Before using this API, ensure that the `route_information` and `stops` you intend to link already exist in their respective tables.
- The `route_id` from the URL is used for GET (single), PUT, and DELETE operations.
- For POST, the `route_id` is required in the JSON body.
- For DELETE, providing a `stop_id` in the URL will delete a single record, while omitting it will delete all records for the given `route_id`.

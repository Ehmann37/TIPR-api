# 🚌 Driver API Documentation

> This API manages bus driver data in the TRIP system, following RESTful principles. It supports creating, retrieving, updating, and deleting driver records.

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
| `driver_id`     | Integer | Unique ID of the driver (auto-generated, read-only) |
| `company_id`    | Integer | Foreign key referencing the bus company.       |
| `first_name`    | String  | Driver's first name.                           |
| `last_name`     | String  | Driver's last name.                            |
| `license_number`| String  | Driver's license number.                       |
| `contact_info`  | String  | Driver's contact information (phone number).  |

---

## 📌 Endpoints

The base URL for these endpoints is `.../api`. For example: `https://trip-api.dcism.org/api`.

### 1. Get All Drivers

Retrieves a list of all drivers in the system.

- **Endpoint:** `/driver`
- **Method:** `GET`

#### Response:
```json
{
  "status": "success",
  "data": [
    {
      "driver_id": 1,
      "company_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "license_number": "LIC12345678",
      "contact_info": "09123456789"
    },
    {
      "driver_id": 2,
      "company_id": 1,
      "first_name": "Jane",
      "last_name": "Smith",
      "license_number": "LIC87654321",
      "contact_info": "09876543210"
    }
  ]
}
```

---

### 2. Get Driver by ID

Retrieves a single driver by their unique ID.

- **Endpoint:** `/driver/{id}`
- **Method:** `GET`

**Example URL:** `/driver/1`

#### Response (Success):
```json
{
  "status": "success",
  "data": {
    "driver_id": 1,
    "company_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "license_number": "LIC12345678",
    "contact_info": "09123456789"
  }
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Driver not found"
}
```

---

### 3. Add a New Driver

Creates a new driver record.

- **Endpoint:** `/driver`
- **Method:** `POST`

#### Request Body:
```json
{
  "company_id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "license_number": "LIC12345678",
  "contact_info": "09123456789"
}
```

#### Response (Success):
```json
{
  "status": "success",
  "driver_id": 3
}
```

---

### 4. Update a Driver

Updates an existing driver by their ID.

- **Endpoint:** `/driver/{id}`
- **Method:** `PUT`

**Example URL:** `/driver/1`

#### Request Body:
```json
{
  "company_id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "license_number": "LIC12345678",
  "contact_info": "09123456789"
}
```

#### Response (Success):
```json
{
  "status": "success",
  "message": "Driver updated"
}
```

#### Response (Error - Not Found or No Changes):
```json
{
    "status": "error",
    "message": "Driver not found or no changes made"
}
```

---

### 5. Delete a Driver

Deletes a driver by their ID.

- **Endpoint:** `/driver/{id}`
- **Method:** `DELETE`

**Example URL:** `/driver/1`

#### Response (Success):
```json
{
  "status": "success",
  "message": "Driver deleted"
}
```

#### Response (Error - Not Found):
```json
{
  "status": "error",
  "message": "Driver not found"
}
```

---

## 🧪 Testing Tools

- You can test all endpoints using tools like **Postman**.
- Remember to set the **Authorization** header and use **raw JSON** for request bodies.

---

## 📌 Notes

- The `driver_id` is passed via the URL for GET (single), PUT, and DELETE requests, not in the request body or query parameters.
- `company_id` must correspond to an existing company in the system.
- All responses are in **JSON** format.
- License numbers should be unique for each driver. 